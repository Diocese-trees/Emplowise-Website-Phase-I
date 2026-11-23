#!/usr/bin/env python3
"""Audit the generated Office deliverables against the brand-kit rules.

    python3 tools/brand-kit/verify-office.py

Every file in public/downloads that ends in .docx is checked for:

  * a readable OOXML package (zip container, parseable document part),
  * a size under 500 KB,
  * at least one embedded raster logo,
  * at least one Heading 1 paragraph and one table,
  * zero raw Markdown tokens (``##``, ``**``, ``|---``, backticks, ``](``),
  * zero unresolved verification notes or TODO placeholders.

The pitch deck is checked for eight slides on a 16:9 canvas, a slide number on
every face, speaker notes on every slide, no raw Markdown or verification notes
on a slide face, and every shape inside the 0.65 in / 0.55 in safe margin.

The script prints one table per format and exits non-zero if any check fails, so
it can sit directly in a release gate. It never writes to the repository.
"""

from __future__ import annotations

import sys
import zipfile
from pathlib import Path

from docx import Document
from lxml import etree
from pptx import Presentation

ROOT = Path(__file__).resolve().parents[2]
PUBLIC = ROOT / "public" / "downloads"
SIZE_LIMIT = 500 * 1024

# Literal junk that means the Markdown renderer missed a construct.
RAW_TOKENS = ["##", "**", "|---", "`", "]("]
FORBIDDEN_TEXT = ["[To Verify", "TODO", "[to verify"]

DECK_SLIDES = 8
DECK_W, DECK_H = 13.333, 7.5
MARGIN_X, MARGIN_Y = 0.65, 0.55
EMU_PER_INCH = 914400


def collect_text(path: Path) -> tuple[str, dict[str, int], int]:
    """Return body text, heading/table counts and embedded image count."""
    document = Document(str(path))
    parts = [paragraph.text for paragraph in document.paragraphs]
    for table in document.tables:
        for row in table.rows:
            for cell in row.cells:
                parts.extend(paragraph.text for paragraph in cell.paragraphs)

    for section in document.sections:
        for container in (section.header, section.footer,
                          section.first_page_header, section.first_page_footer):
            parts.extend(paragraph.text for paragraph in container.paragraphs)

    headings = sum(
        1 for paragraph in document.paragraphs
        if paragraph.style is not None and paragraph.style.name == "Heading 1"
    )
    with zipfile.ZipFile(path) as package:
        media = [n for n in package.namelist() if n.startswith("word/media/")]
    return "\n".join(parts), {"headings": headings, "tables": len(document.tables)}, len(media)


def check_package(path: Path) -> str | None:
    """Return an error string when the package is unreadable."""
    try:
        with zipfile.ZipFile(path) as package:
            bad = package.testzip()
            if bad is not None:
                return f"corrupt member {bad}"
            part = package.read("word/document.xml")
    except (zipfile.BadZipFile, KeyError, OSError) as error:
        return f"unreadable package: {error}"
    try:
        etree.fromstring(part)
    except etree.XMLSyntaxError as error:
        return f"invalid document.xml: {error}"
    return None


def audit(path: Path) -> tuple[bool, list[str], dict[str, int]]:
    problems: list[str] = []
    size = path.stat().st_size
    if size >= SIZE_LIMIT:
        problems.append(f"size {size / 1024:.0f} KB >= 500 KB")

    package_error = check_package(path)
    if package_error:
        problems.append(package_error)
        return False, problems, {}

    text, counts, media = collect_text(path)
    if media == 0:
        problems.append("no embedded image")
    if counts["headings"] == 0:
        problems.append("no Heading 1")
    if counts["tables"] == 0:
        problems.append("no table")

    raw = [token for token in RAW_TOKENS if token in text]
    if raw:
        problems.append("raw Markdown: " + ", ".join(repr(token) for token in raw))
    leaks = [token for token in FORBIDDEN_TEXT if token in text]
    if leaks:
        problems.append("leaked: " + ", ".join(repr(token) for token in leaks))

    stats = {
        "kb": round(size / 1024),
        "headings": counts["headings"],
        "tables": counts["tables"],
        "images": media,
        "words": len(text.split()),
    }
    return not problems, problems, stats


def slide_text(slide) -> str:
    parts = []
    for shape in slide.shapes:
        if shape.has_text_frame:
            parts.append(shape.text_frame.text)
        if getattr(shape, "has_table", False) and shape.has_table:
            for row in shape.table.rows:
                for cell in row.cells:
                    parts.append(cell.text)
    return "\n".join(parts)


def audit_deck(path: Path) -> tuple[bool, list[str], list[dict]]:
    """Check the deck as a whole and per slide; return rows for the table."""
    problems: list[str] = []
    size = path.stat().st_size
    if size >= SIZE_LIMIT:
        problems.append(f"size {size / 1024:.0f} KB >= 500 KB")

    try:
        with zipfile.ZipFile(path) as package:
            bad = package.testzip()
            if bad is not None:
                problems.append(f"corrupt member {bad}")
            etree.fromstring(package.read("ppt/presentation.xml"))
            media = [n for n in package.namelist() if n.startswith("ppt/media/")]
    except (zipfile.BadZipFile, KeyError, OSError, etree.XMLSyntaxError) as error:
        return False, [f"unreadable package: {error}"], []

    deck = Presentation(str(path))
    inches_w = deck.slide_width / EMU_PER_INCH
    inches_h = deck.slide_height / EMU_PER_INCH
    if abs(inches_w - DECK_W) > 0.01 or abs(inches_h - DECK_H) > 0.01:
        problems.append(f"canvas {inches_w:.2f} x {inches_h:.2f} in, expected 16:9")
    if len(deck.slides) != DECK_SLIDES:
        problems.append(f"{len(deck.slides)} slides, expected {DECK_SLIDES}")
    if not media:
        problems.append("no embedded image")

    rows = []
    for index, slide in enumerate(deck.slides, start=1):
        issues = []
        face = slide_text(slide)
        notes = slide.notes_slide.notes_text_frame.text.strip() if slide.has_notes_slide else ""

        if f"{index:02d} / 08" not in face:
            issues.append("no counter")
        if not notes:
            issues.append("no speaker notes")
        raw = [token for token in RAW_TOKENS if token in face]
        if raw:
            issues.append("raw Markdown " + ",".join(repr(t) for t in raw))
        leaks = [token for token in FORBIDDEN_TEXT if token in face]
        if leaks:
            issues.append("leaked " + ",".join(repr(t) for t in leaks))

        outside = 0
        for shape in slide.shapes:
            if shape.left is None or shape.top is None:
                continue
            left = shape.left / EMU_PER_INCH
            top = shape.top / EMU_PER_INCH
            right = left + (shape.width or 0) / EMU_PER_INCH
            bottom = top + (shape.height or 0) / EMU_PER_INCH
            if (left < MARGIN_X - 0.01 or top < MARGIN_Y - 0.01
                    or right > DECK_W - MARGIN_X + 0.01
                    or bottom > DECK_H - MARGIN_Y + 0.01):
                outside += 1
        if outside:
            issues.append(f"{outside} shape(s) outside margin")

        rows.append({"slide": index, "shapes": len(slide.shapes),
                     "notes": len(notes.split()), "issues": issues})
        problems.extend(f"slide {index}: {issue}" for issue in issues)

    return not problems, problems, rows


def main() -> int:
    failures = 0

    documents = sorted(PUBLIC.glob("*.docx"))
    if documents:
        header = f"{'docx':<44}{'KB':>5}{'H1':>5}{'tbl':>5}{'img':>5}{'words':>7}  verdict"
        print(header)
        print("-" * len(header))
        for path in documents:
            ok, problems, stats = audit(path)
            if ok:
                print(
                    f"{path.name:<44}{stats['kb']:>5}{stats['headings']:>5}"
                    f"{stats['tables']:>5}{stats['images']:>5}{stats['words']:>7}  pass"
                )
            else:
                failures += 1
                print(f"{path.name:<44}{'--':>5}{'--':>5}{'--':>5}{'--':>5}{'--':>7}  FAIL")
                for problem in problems:
                    print(f"    {problem}")
        print(f"\n{len(documents) - failures}/{len(documents)} DOCX files passed")

    decks = sorted(PUBLIC.glob("*.pptx"))
    for path in decks:
        print()
        ok, problems, rows = audit_deck(path)
        heading = f"{path.name}  ({path.stat().st_size / 1024:.0f} KB)"
        print(heading)
        print("-" * len(heading))
        print(f"{'slide':<8}{'shapes':>7}{'note words':>12}  verdict")
        for row in rows:
            verdict = "pass" if not row["issues"] else "FAIL: " + "; ".join(row["issues"])
            print(f"{row['slide']:>2} / 08{row['shapes']:>9}{row['notes']:>12}  {verdict}")
        if not ok:
            failures += 1
            for problem in problems:
                if not problem.startswith("slide "):
                    print(f"    {problem}")
            print(f"\n{path.name} FAILED")
        else:
            print(f"\n{path.name} passed ({len(rows)} slides)")

    if not documents and not decks:
        print(f"No Office files found in {PUBLIC.relative_to(ROOT)}", file=sys.stderr)
        return 1
    return 1 if failures else 0


if __name__ == "__main__":
    raise SystemExit(main())
