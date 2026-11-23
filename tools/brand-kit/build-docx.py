#!/usr/bin/env python3
"""Render the brand-kit Markdown sources as six branded DOCX files.

Run from anywhere; paths are resolved from this file.

    python3 tools/brand-kit/build-docx.py

The builder reads docs/brand-kit/*.md, drops the trailing Internal Verification
Notes section, and writes public/downloads/*.docx. Styling follows the 2026 kit:
Navy headings, Deep Teal H2, Plus Jakarta Sans body, shaded native tables and an
ice lede panel with a teal rule. Nothing here touches the site's PHP, CSS or JS.
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_BREAK
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Pt, RGBColor

ROOT = Path(__file__).resolve().parents[2]
DOCS_MD = ROOT / "docs" / "brand-kit"
PUBLIC = ROOT / "public" / "downloads"

NAVY = RGBColor(0x08, 0x1B, 0x3E)
TEAL = RGBColor(0x0D, 0x94, 0x88)
DEEP_TEAL = RGBColor(0x0F, 0x76, 0x6E)
SLATE = RGBColor(0x1E, 0x29, 0x3B)
WHITE = RGBColor(0xFF, 0xFF, 0xFF)
GREY = RGBColor(0x54, 0x63, 0x7A)

NAVY_HEX = "081B3E"
TEAL_HEX = "0D9488"
ICE_HEX = "F0F9F9"
OFFWHITE_HEX = "F7F7F7"
BORDER_HEX = "E4EAF1"

DISPLAY = "Montserrat"
BODY = "Plus Jakarta Sans"

# (source markdown, output file, cover title, cover subtitle)
DOCUMENTS = [
    (
        "BRAND_KIT.md",
        "Emplowise-Brand-Kit-2026.docx",
        "Brand Kit 2026",
        "Logo, colour, typography and surface rules",
    ),
    (
        "BRANDING_GUIDELINES.md",
        "Emplowise-Branding-Guidelines-2026.docx",
        "Branding Guidelines 2026",
        "Voice, evidence discipline, interface rules and governance",
    ),
    (
        "COMPANY_PROFILE.md",
        "Emplowise-Company-Profile-2026.docx",
        "Company Profile 2026",
        "Remote HR for growing businesses in Pakistan",
    ),
    (
        "PORTFOLIO.md",
        "Emplowise-Portfolio-2026.docx",
        "Portfolio 2026",
        "Verified shipped work, with no invented clients",
    ),
    (
        "WEBSITE_BREAKDOWN.md",
        "Emplowise-Website-Breakdown-2026.docx",
        "Website Breakdown 2026",
        "Routes, partials, modules and delivery plumbing",
    ),
    (
        "TOOL_STACK.md",
        "Emplowise-Tool-Stack-2026.docx",
        "Tool Stack 2026",
        "Verified versions and the Phase II upgrade ledger",
    ),
]

VERIFY_HEADING = re.compile(
    r"^#{2,3}\s+(?:\d+\s+)?(?:Section [A-Z]:\s+)?Internal [Vv]erification [Nn]otes\s*$",
    re.MULTILINE,
)


# --------------------------------------------------------------------------- #
# Markdown helpers
# --------------------------------------------------------------------------- #

INLINE = re.compile(
    r"(\*\*.+?\*\*|`[^`]+`|\[[^\]]+\]\([^)]+\)|\*[^*\n]+\*)",
)


def strip_verification_notes(markdown: str) -> str:
    """Drop the internal notes section; it must never reach a client file."""
    match = VERIFY_HEADING.search(markdown)
    if match:
        markdown = markdown[: match.start()]
    return markdown.rstrip() + "\n"


def inline_runs(text: str) -> list[tuple[str, set[str]]]:
    """Split one line into (text, styles) pairs.

    Styles nest, so a code span inside a link keeps both the link colour and the
    monospace face, and bold text that quotes a hex value still sets the code.
    """
    runs: list[tuple[str, set[str]]] = []
    for part in INLINE.split(text):
        if not part:
            continue
        if part.startswith("**") and part.endswith("**") and len(part) > 4:
            runs.extend(decorate(inline_runs(part[2:-2]), "bold"))
        elif part.startswith("`") and part.endswith("`") and len(part) > 2:
            runs.append((part[1:-1], {"code"}))
        elif part.startswith("[") and "](" in part and part.endswith(")"):
            label = part[1 : part.index("](")]
            runs.extend(decorate(inline_runs(label), "link"))
        elif part.startswith("*") and part.endswith("*") and len(part) > 2:
            runs.extend(decorate(inline_runs(part[1:-1]), "italic"))
        else:
            runs.append((part, set()))
    return runs


def decorate(runs: list[tuple[str, set[str]]], style: str) -> list[tuple[str, set[str]]]:
    return [(chunk, styles | {style}) for chunk, styles in runs]


def soften(chunk: str) -> str:
    """Give long code tokens break opportunities so no line starts with a bullet."""
    if len(chunk) <= 36:
        return chunk
    return chunk.replace("/", "/\u200b").replace(",", ",\u200b")


def add_runs(paragraph, text: str, *, size: float = 10.5, color=SLATE,
             font: str = BODY, bold: bool = False) -> None:
    for chunk, styles in inline_runs(text):
        code = "code" in styles
        run = paragraph.add_run(soften(chunk) if code else chunk)
        run.font.name = "Consolas" if code else font
        run.font.size = Pt(size - 0.5 if code else size)
        run.font.bold = bold or "bold" in styles
        run.font.italic = "italic" in styles
        if code:
            run.font.color.rgb = NAVY
        elif "link" in styles:
            run.font.color.rgb = DEEP_TEAL
        else:
            run.font.color.rgb = color


def shade(cell, hex_fill: str) -> None:
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = OxmlElement("w:shd")
    shd.set(qn("w:val"), "clear")
    shd.set(qn("w:color"), "auto")
    shd.set(qn("w:fill"), hex_fill)
    tc_pr.append(shd)


def cell_borders(cell, **edges) -> None:
    """edges: left/right/top/bottom = (size_eighths, hex) or None to keep."""
    tc_pr = cell._tc.get_or_add_tcPr()
    borders = tc_pr.find(qn("w:tcBorders"))
    if borders is None:
        borders = OxmlElement("w:tcBorders")
        tc_pr.append(borders)
    for edge, spec in edges.items():
        element = OxmlElement(f"w:{edge}")
        if spec is None:
            element.set(qn("w:val"), "nil")
        else:
            size, hex_color = spec
            element.set(qn("w:val"), "single")
            element.set(qn("w:sz"), str(size))
            element.set(qn("w:space"), "0")
            element.set(qn("w:color"), hex_color)
        borders.append(element)


def paragraph_border(paragraph, size: int, hex_color: str) -> None:
    p_pr = paragraph._p.get_or_add_pPr()
    borders = OxmlElement("w:pBdr")
    bottom = OxmlElement("w:bottom")
    bottom.set(qn("w:val"), "single")
    bottom.set(qn("w:sz"), str(size))
    bottom.set(qn("w:space"), "4")
    bottom.set(qn("w:color"), hex_color)
    borders.append(bottom)
    p_pr.append(borders)


def repeat_header(row) -> None:
    tr_pr = row._tr.get_or_add_trPr()
    header = OxmlElement("w:tblHeader")
    header.set(qn("w:val"), "true")
    tr_pr.append(header)


def no_split(row) -> None:
    tr_pr = row._tr.get_or_add_trPr()
    cant = OxmlElement("w:cantSplit")
    cant.set(qn("w:val"), "true")
    tr_pr.append(cant)


def page_number_footer(paragraph) -> None:
    run = paragraph.add_run("Page ")
    run.font.size = Pt(8)
    run.font.name = BODY
    run.font.color.rgb = GREY
    field_run = paragraph.add_run()
    begin = OxmlElement("w:fldChar")
    begin.set(qn("w:fldCharType"), "begin")
    instr = OxmlElement("w:instrText")
    instr.set(qn("xml:space"), "preserve")
    instr.text = " PAGE "
    end = OxmlElement("w:fldChar")
    end.set(qn("w:fldCharType"), "end")
    field_run._r.append(begin)
    field_run._r.append(instr)
    field_run._r.append(end)
    field_run.font.size = Pt(8)
    field_run.font.name = BODY
    field_run.font.color.rgb = GREY


# --------------------------------------------------------------------------- #
# Document construction
# --------------------------------------------------------------------------- #


def base_document() -> Document:
    document = Document()
    section = document.sections[0]
    section.page_width = Cm(21.0)
    section.page_height = Cm(29.7)
    section.left_margin = Cm(2.2)
    section.right_margin = Cm(2.2)
    section.top_margin = Cm(2.4)
    section.bottom_margin = Cm(2.2)
    section.different_first_page_header_footer = True

    normal = document.styles["Normal"]
    normal.font.name = BODY
    normal.font.size = Pt(10.5)
    normal.font.color.rgb = SLATE
    normal.paragraph_format.line_spacing = 1.35
    normal.paragraph_format.space_after = Pt(6)

    for name, size, color in (
        ("Heading 1", 20, NAVY),
        ("Heading 2", 14, DEEP_TEAL),
        ("Heading 3", 11.5, NAVY),
    ):
        style = document.styles[name]
        style.font.name = DISPLAY
        style.font.bold = True
        style.font.size = Pt(size)
        style.font.color.rgb = color
        style.paragraph_format.keep_with_next = True
        style.paragraph_format.space_before = Pt(16 if name == "Heading 1" else 12)
        style.paragraph_format.space_after = Pt(6)
    return document


def add_cover(document: Document, title: str, subtitle: str, source: str) -> None:
    logo = ROOT / "public" / "img" / "logo-primary-375.png"
    paragraph = document.add_paragraph()
    paragraph.paragraph_format.space_before = Pt(90)
    if logo.exists():
        paragraph.add_run().add_picture(str(logo), width=Cm(4.6))

    heading = document.add_paragraph()
    heading.paragraph_format.space_before = Pt(28)
    heading.paragraph_format.space_after = Pt(4)
    run = heading.add_run(title)
    run.font.name = DISPLAY
    run.font.bold = True
    run.font.size = Pt(28)
    run.font.color.rgb = NAVY

    line = document.add_paragraph()
    line.paragraph_format.space_after = Pt(18)
    run = line.add_run(subtitle)
    run.font.name = BODY
    run.font.size = Pt(12)
    run.font.color.rgb = TEAL

    divider = document.add_table(rows=1, cols=2)
    divider.alignment = WD_TABLE_ALIGNMENT.LEFT
    divider.autofit = False
    fixed_width(divider, 15.6)
    widths = (Cm(9.0), Cm(6.6))
    for index, cell in enumerate(divider.rows[0].cells):
        cell.width = widths[index]
        cell.text = ""
        cell_borders(
            cell,
            left=None,
            right=None,
            top=None,
            bottom=(16, NAVY_HEX if index == 0 else TEAL_HEX),
        )
        cell.paragraphs[0].paragraph_format.space_after = Pt(0)

    meta = document.add_paragraph()
    meta.paragraph_format.space_before = Pt(18)
    run = meta.add_run(
        f"Emplowise HR Services  ·  2026 brand system  ·  Source: docs/brand-kit/{source}"
    )
    run.font.name = BODY
    run.font.size = Pt(8.5)
    run.font.color.rgb = GREY

    document.add_paragraph().add_run().add_break(WD_BREAK.PAGE)


def add_header_footer(document: Document, title: str) -> None:
    section = document.sections[0]
    header = section.header.paragraphs[0]
    header.text = ""
    run = header.add_run(f"Emplowise  ·  {title}")
    run.font.name = BODY
    run.font.size = Pt(8)
    run.font.color.rgb = GREY
    header.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    paragraph_border(header, 6, BORDER_HEX)

    footer = section.footer.paragraphs[0]
    footer.text = ""
    footer.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    page_number_footer(footer)


def add_lede(document: Document, text: str) -> None:
    table = document.add_table(rows=1, cols=1)
    table.autofit = False
    fixed_width(table, 16.6)
    cell = table.rows[0].cells[0]
    cell.width = Cm(16.6)
    shade(cell, ICE_HEX)
    cell_borders(cell, left=(24, TEAL_HEX), right=None, top=None, bottom=None)
    cell.text = ""
    paragraph = cell.paragraphs[0]
    paragraph.paragraph_format.space_before = Pt(6)
    paragraph.paragraph_format.space_after = Pt(6)
    add_runs(paragraph, text, size=10.5)
    document.add_paragraph().paragraph_format.space_after = Pt(2)


def split_row(line: str) -> list[str]:
    cells = line.strip().strip("|").split("|")
    return [cell.strip() for cell in cells]


def longest_segment(cell: str) -> int:
    """Longest run of characters Word cannot break, treating - and / as breaks."""
    pieces = re.split(r"[/\-]", cell)
    return max((len(piece.strip()) for piece in pieces), default=1)


def column_widths(rows: list[list[str]], columns: int, usable: float = 16.6,
                  char_cm: float = 0.185) -> list[float]:
    """Size columns by content, then guarantee room for the longest unbreakable run."""
    scores, minimum = [], []
    for column in range(columns):
        cells = [row[column] for row in rows if column < len(row)]
        segment = max((longest_segment(cell) for cell in cells), default=1)
        longest_cell = max((len(cell) for cell in cells), default=1)
        scores.append(max(segment * 1.3, min(longest_cell, 24)))
        minimum.append(max(min(segment * char_cm + 0.5, usable / max(columns - 1, 1)), 1.0))

    total = sum(scores)
    widths = [usable * score / total for score in scores]

    # Minimums must themselves fit the text block, otherwise the table overruns
    # the right margin; scale them down together and keep the proportions.
    if sum(minimum) > usable:
        scale = usable / sum(minimum)
        minimum = [value * scale for value in minimum]

    # Never let a column sit below the width of its longest unbreakable run.
    for _ in range(columns * 2):
        short = [i for i, width in enumerate(widths) if width + 1e-6 < minimum[i]]
        if not short:
            break
        surplus = sorted(
            (i for i in range(columns) if i not in short),
            key=lambda i: widths[i] - minimum[i], reverse=True,
        )
        for index in short:
            deficit = minimum[index] - widths[index]
            for donor in surplus:
                spare = widths[donor] - minimum[donor]
                if spare <= 0.01:
                    continue
                taken = min(deficit, spare)
                widths[donor] -= taken
                widths[index] += taken
                deficit -= taken
                if deficit <= 0.001:
                    break

    # Absorb rounding so the table lands exactly on the text block.
    drift = usable - sum(widths)
    widest = max(range(columns), key=lambda i: widths[i])
    widths[widest] = max(widths[widest] + drift, minimum[widest])
    return widths


def cell_margins(table, left: int = 100, right: int = 100) -> None:
    """Tighten default cell padding (twentieths of a point) to gain text room."""
    tbl_pr = table._tbl.tblPr
    margin = OxmlElement("w:tblCellMar")
    for edge, value in (("top", 40), ("left", left), ("bottom", 40), ("right", right)):
        element = OxmlElement(f"w:{edge}")
        element.set(qn("w:w"), str(value))
        element.set(qn("w:type"), "dxa")
        margin.append(element)
    tbl_pr.append(margin)


def fixed_width(table, centimetres: float) -> None:
    """Pin tblW to the grid so no renderer expands the table past the margin."""
    tbl_pr = table._tbl.tblPr
    tbl_w = tbl_pr.find(qn("w:tblW"))
    if tbl_w is None:
        tbl_w = OxmlElement("w:tblW")
        tbl_pr.append(tbl_w)
    tbl_w.set(qn("w:type"), "dxa")
    tbl_w.set(qn("w:w"), str(int(round(centimetres * 567))))


def add_table(document: Document, rows: list[list[str]]) -> None:
    columns = max(len(row) for row in rows)
    table = document.add_table(rows=0, cols=columns)
    table.style = "Table Grid"
    table.alignment = WD_TABLE_ALIGNMENT.LEFT
    table.autofit = False
    table.allow_autofit = False
    cell_margins(table)
    widths = column_widths(rows, columns)
    fixed_width(table, sum(widths))
    font_size = 9 if columns <= 4 else (8.5 if columns <= 6 else 8)

    for index, values in enumerate(rows):
        row = table.add_row()
        no_split(row)
        for column in range(columns):
            cell = row.cells[column]
            cell.width = Cm(widths[column])
            cell.text = ""
            paragraph = cell.paragraphs[0]
            paragraph.paragraph_format.space_before = Pt(3)
            paragraph.paragraph_format.space_after = Pt(3)
            paragraph.paragraph_format.line_spacing = 1.15
            text = values[column] if column < len(values) else ""
            if index == 0:
                shade(cell, NAVY_HEX)
                add_runs(paragraph, text, size=font_size, color=WHITE,
                         font=DISPLAY, bold=True)
            else:
                shade(cell, ICE_HEX if index % 2 == 1 else "FFFFFF")
                add_runs(paragraph, text, size=font_size)
        if index == 0:
            repeat_header(row)

    for column, width in enumerate(widths):
        table.columns[column].width = Cm(width)

    document.add_paragraph().paragraph_format.space_after = Pt(2)


def add_bullet(document: Document, text: str, marker: str = "•") -> None:
    paragraph = document.add_paragraph()
    paragraph.paragraph_format.left_indent = Cm(0.6)
    paragraph.paragraph_format.first_line_indent = Cm(-0.35)
    paragraph.paragraph_format.space_after = Pt(3)
    run = paragraph.add_run(f"{marker} ")
    run.font.name = BODY
    run.font.size = Pt(10.5)
    run.font.color.rgb = TEAL
    run.font.bold = True
    add_runs(paragraph, text)


def add_heading(document: Document, text: str, level: int) -> None:
    """Heading paragraphs whose inline code is set, not printed as backticks."""
    size, color = {1: (20, NAVY), 2: (14, DEEP_TEAL), 3: (11.5, NAVY)}.get(
        level, (11.5, NAVY)
    )
    paragraph = document.add_paragraph(style=f"Heading {level}")
    add_runs(paragraph, text, size=size, color=color, font=DISPLAY, bold=True)


def render_markdown(document: Document, markdown: str) -> None:
    lines = markdown.splitlines()
    index = 0
    lede_done = False
    number = 0

    while index < len(lines):
        line = lines[index].rstrip()
        stripped = line.strip()

        if not stripped or stripped == "---":
            index += 1
            number = 0
            continue

        if stripped.startswith("|"):
            rows = []
            while index < len(lines) and lines[index].strip().startswith("|"):
                raw = lines[index].strip()
                if not re.match(r"^\|[\s:|-]+\|?$", raw):
                    rows.append(split_row(raw))
                index += 1
            if rows:
                add_table(document, rows)
            continue

        if stripped.startswith("# "):
            index += 1  # the cover carries the title
            continue

        level = len(stripped) - len(stripped.lstrip("#"))
        if 2 <= level <= 4 and stripped.startswith("#"):
            text = stripped[level:].strip()
            add_heading(document, text, level - 1)
            index += 1
            lede_done = True
            continue

        bullet = re.match(r"^[-*]\s+(.*)$", stripped)
        numbered = re.match(r"^\d+\.\s+(.*)$", stripped)
        quote = stripped.startswith(">")

        if bullet or numbered:
            block = [bullet.group(1) if bullet else numbered.group(1)]
            index += 1
            while index < len(lines):
                nxt = lines[index].strip()
                if not nxt:
                    break
                if nxt.startswith(("- ", "* ", ">", "#", "|")) or re.match(r"^\d+\.\s", nxt):
                    break
                block.append(nxt)
                index += 1
            text = " ".join(block)
            if numbered:
                number += 1
                add_bullet(document, text, marker=f"{number}.")
            else:
                add_bullet(document, text)
            continue

        if quote:
            block = []
            while index < len(lines) and lines[index].strip().startswith(">"):
                block.append(lines[index].strip().lstrip(">").strip())
                index += 1
            add_lede(document, " ".join(block))
            lede_done = True
            continue

        block = [stripped]
        index += 1
        while index < len(lines):
            nxt = lines[index].strip()
            if not nxt or nxt.startswith(("#", "|", "- ", "* ", ">")) or re.match(
                r"^\d+\.\s", nxt
            ):
                break
            block.append(nxt)
            index += 1
        text = " ".join(block)

        if not lede_done:
            add_lede(document, text)
            lede_done = True
            continue

        paragraph = document.add_paragraph()
        paragraph.paragraph_format.space_after = Pt(7)
        add_runs(paragraph, text)


def build_one(source: str, output: str, title: str, subtitle: str) -> Path:
    markdown = (DOCS_MD / source).read_text(encoding="utf-8")
    markdown = strip_verification_notes(markdown)

    document = base_document()
    properties = document.core_properties
    properties.title = f"Emplowise {title}"
    properties.subject = subtitle
    properties.author = "Emplowise HR Services"
    properties.last_modified_by = "Emplowise"
    properties.comments = f"Source: docs/brand-kit/{source}"
    add_cover(document, title, subtitle, source)
    add_header_footer(document, title)
    render_markdown(document, markdown)

    PUBLIC.mkdir(parents=True, exist_ok=True)
    target = PUBLIC / output
    document.save(target)
    return target


def main() -> int:
    if not DOCS_MD.is_dir():
        print(f"Missing source directory: {DOCS_MD}", file=sys.stderr)
        return 1

    for source, output, title, subtitle in DOCUMENTS:
        path = build_one(source, output, title, subtitle)
        print(f"built {path.relative_to(ROOT)}  ({path.stat().st_size / 1024:.0f} KB)")

    print(f"\n{len(DOCUMENTS)} DOCX files written to public/downloads")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
