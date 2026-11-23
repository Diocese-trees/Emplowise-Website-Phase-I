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

The script prints one row per file and exits non-zero if any check fails, so it
can sit directly in a release gate. It never writes to the repository.
"""

from __future__ import annotations

import sys
import zipfile
from pathlib import Path

from docx import Document
from lxml import etree

ROOT = Path(__file__).resolve().parents[2]
PUBLIC = ROOT / "public" / "downloads"
SIZE_LIMIT = 500 * 1024

# Literal junk that means the Markdown renderer missed a construct.
RAW_TOKENS = ["##", "**", "|---", "`", "]("]
FORBIDDEN_TEXT = ["[To Verify", "TODO", "[to verify"]


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


def main() -> int:
    files = sorted(PUBLIC.glob("*.docx"))
    if not files:
        print(f"No DOCX files found in {PUBLIC.relative_to(ROOT)}", file=sys.stderr)
        return 1

    header = f"{'file':<44}{'KB':>5}{'H1':>5}{'tbl':>5}{'img':>5}{'words':>7}  verdict"
    print(header)
    print("-" * len(header))

    failures = 0
    for path in files:
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

    print(f"\n{len(files) - failures}/{len(files)} DOCX files passed")
    return 1 if failures else 0


if __name__ == "__main__":
    raise SystemExit(main())
