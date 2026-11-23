#!/usr/bin/env python3
"""Render docs/brand-kit/PITCH_DECK.md as an eight-slide native PPTX.

    python3 tools/brand-kit/build-pptx.py

The Markdown stays the single source of truth: headlines, on-slide blocks,
speaker notes and evidence lines are parsed from it, so the deck cannot drift
from the written pitch. The script only supplies the layout, brand palette and
type sizes. Unresolved questions stay in the speaker notes; the slide faces
carry confirmed copy alone.

Output: public/downloads/Emplowise-Pitch-Deck-2026.pptx
"""

from __future__ import annotations

import re
import sys
from pathlib import Path

from pptx import Presentation
from pptx.dml.color import RGBColor
from pptx.enum.shapes import MSO_SHAPE
from pptx.enum.text import MSO_ANCHOR, MSO_AUTO_SIZE, PP_ALIGN
from pptx.oxml import parse_xml
from pptx.oxml.ns import nsdecls, qn
from pptx.util import Inches, Pt

ROOT = Path(__file__).resolve().parents[2]
SOURCE = ROOT / "docs" / "brand-kit" / "PITCH_DECK.md"
PUBLIC = ROOT / "public" / "downloads"
OUTPUT = "Emplowise-Pitch-Deck-2026.pptx"

LOGO_PRIMARY = ROOT / "public" / "img" / "logo-primary-375.png"
LOGO_REVERSE = ROOT / "public" / "img" / "logo-reverse-375.png"

NAVY = RGBColor(0x08, 0x1B, 0x3E)
TEAL = RGBColor(0x0D, 0x94, 0x88)
DEEP_TEAL = RGBColor(0x0F, 0x76, 0x6E)
AQUA = RGBColor(0x5E, 0xEA, 0xD4)
ICE = RGBColor(0xF0, 0xF9, 0xF9)
OFF_WHITE = RGBColor(0xF7, 0xF7, 0xF7)
SLATE = RGBColor(0x1E, 0x29, 0x3B)
WHITE = RGBColor(0xFF, 0xFF, 0xFF)
GREY = RGBColor(0x54, 0x63, 0x7A)

BORDER_LIGHT = "E4EAF1"
BORDER_NAVY = "2A4064"
BORDER_TEAL = "2F948A"
NAVY_HEX = "081B3E"
ICE_HEX = "F0F9F9"
TEAL_HEX = "0D9488"

DISPLAY = "Montserrat"
BODY = "Plus Jakarta Sans"

SLIDE_W, SLIDE_H = 13.333, 7.5
MARGIN_X = 0.85
MARGIN_TOP = 0.6
CONTENT_W = SLIDE_W - 2 * MARGIN_X          # 11.633 in
COUNTER_TOP = 6.6

THEMES = {
    "navy": {
        "ground": NAVY, "headline": WHITE, "body": ICE, "label": AQUA,
        "rule": BORDER_NAVY, "counter": ICE, "logo": LOGO_REVERSE,
    },
    "white": {
        "ground": WHITE, "headline": NAVY, "body": SLATE, "label": DEEP_TEAL,
        "rule": BORDER_LIGHT, "counter": GREY, "logo": LOGO_PRIMARY,
    },
    "mist": {
        "ground": ICE, "headline": NAVY, "body": SLATE, "label": DEEP_TEAL,
        "rule": BORDER_LIGHT, "counter": GREY, "logo": LOGO_PRIMARY,
    },
    "offwhite": {
        "ground": OFF_WHITE, "headline": NAVY, "body": SLATE, "label": DEEP_TEAL,
        "rule": BORDER_LIGHT, "counter": GREY, "logo": LOGO_PRIMARY,
    },
    "deepteal": {
        "ground": DEEP_TEAL, "headline": WHITE, "body": WHITE, "label": WHITE,
        "rule": BORDER_TEAL, "counter": ICE, "logo": LOGO_REVERSE,
    },
}

# Ground per slide, in order. Matches PITCH_DECK.md's approved layouts.
GROUNDS = ["navy", "white", "mist", "white", "navy", "offwhite", "white", "deepteal"]


# --------------------------------------------------------------------------- #
# Parsing
# --------------------------------------------------------------------------- #


def parse_deck(markdown: str) -> list[dict]:
    sections = re.split(r"^## Slide (\d{2}): .*$", markdown, flags=re.MULTILINE)
    slides = []
    for index in range(1, len(sections), 2):
        number = int(sections[index])
        body = sections[index + 1]
        slides.append(parse_slide(number, body))
    return slides


def parse_bullets(text: str) -> list[str]:
    """Join a Markdown bullet list, folding continuation lines into their item."""
    items: list[str] = []
    for line in text.splitlines():
        if line.startswith("- "):
            items.append(line[2:].strip())
        elif line.strip() and items and line.startswith(("  ", "\t")):
            items[-1] += " " + line.strip()
    return [" ".join(item.split()) for item in items]


def parse_slide(number: int, body: str) -> dict:
    headline = re.search(r"^Headline: \*(.+?)\*\s*$", body, re.MULTILINE)
    blocks_section = re.search(
        r"\*\*On-Slide WH Content\.\*\*(.*?)\*\*Speaker Notes\.\*\*", body, re.DOTALL
    )
    notes_section = re.search(
        r"\*\*Speaker Notes\.\*\*(.*?)\*\*Evidence Status\.\*\*", body, re.DOTALL
    )
    evidence = re.search(r"\*\*Evidence Status\.\*\*\s*(.+?)(?:\Z|^\s*---)", body,
                         re.DOTALL | re.MULTILINE)

    blocks = []
    for item in parse_bullets(blocks_section.group(1)):
        match = re.match(r"\*\*(.+?)\.\*\*\s*(.+)$", item)
        if match:
            blocks.append((match.group(1), match.group(2)))

    notes = [note.replace("`", "") for note in parse_bullets(notes_section.group(1))]

    return {
        "number": number,
        "headline": " ".join(headline.group(1).split()),
        "blocks": blocks,
        "notes": notes,
        "evidence": " ".join(evidence.group(1).split()) if evidence else "",
    }


# --------------------------------------------------------------------------- #
# Drawing helpers
# --------------------------------------------------------------------------- #


def text_frame(shape, *, anchor=MSO_ANCHOR.TOP):
    frame = shape.text_frame
    frame.word_wrap = True
    frame.auto_size = MSO_AUTO_SIZE.NONE
    frame.vertical_anchor = anchor
    frame.margin_left = 0
    frame.margin_right = 0
    frame.margin_top = 0
    frame.margin_bottom = 0
    return frame


def box(slide, left, top, width, height, *, anchor=MSO_ANCHOR.TOP):
    shape = slide.shapes.add_textbox(Inches(left), Inches(top), Inches(width), Inches(height))
    return text_frame(shape, anchor=anchor)


def para(frame, text, *, size=14, color=SLATE, font=BODY, bold=False, first=False,
         align=PP_ALIGN.LEFT, line=1.15, before=0, after=0, spacing=None):
    paragraph = frame.paragraphs[0] if first else frame.add_paragraph()
    paragraph.alignment = align
    paragraph.line_spacing = line
    paragraph.space_before = Pt(before)
    paragraph.space_after = Pt(after)
    run = paragraph.add_run()
    run.text = text
    run.font.name = font
    run.font.size = Pt(size)
    run.font.bold = bold
    run.font.color.rgb = color
    if spacing:
        run.font._rPr.set("spc", str(spacing))
    return paragraph


def flat(item):
    """Drop the theme style reference so no inherited shadow is painted."""
    style = item._element.find(qn("p:style"))
    if style is not None:
        item._element.remove(style)
    item.shadow.inherit = False
    return item


def panel(slide, left, top, width, height, *, fill=None, line=None, line_pt=1.0,
          radius=0.04, shape=MSO_SHAPE.ROUNDED_RECTANGLE):
    item = slide.shapes.add_shape(shape, Inches(left), Inches(top), Inches(width), Inches(height))
    flat(item)
    if shape == MSO_SHAPE.ROUNDED_RECTANGLE:
        item.adjustments[0] = radius
    if fill is None:
        item.fill.background()
    else:
        item.fill.solid()
        item.fill.fore_color.rgb = (
            RGBColor.from_string(fill) if isinstance(fill, str) else fill
        )
    if line is None:
        item.line.fill.background()
    else:
        item.line.color.rgb = RGBColor.from_string(line)
        item.line.width = Pt(line_pt)
    item.text_frame.word_wrap = True
    return item


def rule(slide, left, top, width, color, *, thickness=1.5):
    bar = flat(slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(left), Inches(top),
                                      Inches(width), Pt(thickness)))
    bar.fill.solid()
    bar.fill.fore_color.rgb = RGBColor.from_string(color) if isinstance(color, str) else color
    bar.line.fill.background()
    return bar


def vrule(slide, left, top, height, color, *, thickness=1.5):
    bar = flat(slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(left), Inches(top),
                                      Pt(thickness), Inches(height)))
    bar.fill.solid()
    bar.fill.fore_color.rgb = RGBColor.from_string(color) if isinstance(color, str) else color
    bar.line.fill.background()
    return bar


def cell_borders(cell, hex_color: str, width_pt: float = 1.0, edges="LRTB") -> None:
    tc_pr = cell._tc.get_or_add_tcPr()
    parts = []
    for edge in "LRTB":
        tag = f"a:ln{edge}"
        if edge in edges:
            parts.append(
                f'<{tag} {nsdecls("a")} w="{int(width_pt * 12700)}" cap="flat" cmpd="sng" algn="ctr">'
                f'<a:solidFill><a:srgbClr val="{hex_color}"/></a:solidFill>'
                f'<a:prstDash val="solid"/></{tag}>'
            )
        else:
            parts.append(f'<{tag} {nsdecls("a")} w="0"><a:noFill/></{tag}>')
    fragment = parse_xml(f"<a:wrap {nsdecls('a')}>{''.join(parts)}</a:wrap>")
    for child in list(tc_pr):
        if child.tag.endswith(("lnL", "lnR", "lnT", "lnB")):
            tc_pr.remove(child)
    for offset, child in enumerate(fragment):
        tc_pr.insert(offset, child)


def fill_cell(cell, hex_color: str | None) -> None:
    if hex_color is None:
        cell.fill.background()
    else:
        cell.fill.solid()
        cell.fill.fore_color.rgb = RGBColor.from_string(hex_color)


def logo(slide, theme, *, height=0.42):
    slide.shapes.add_picture(
        str(theme["logo"]), Inches(MARGIN_X), Inches(MARGIN_TOP),
        height=Inches(height),
    )


def headline(slide, text, theme, *, size=34):
    frame = box(slide, MARGIN_X, 1.25, CONTENT_W - 0.4, 1.15)
    para(frame, text, size=size, color=theme["headline"], font=DISPLAY, bold=True,
         first=True, line=1.05)


def counter(slide, index, theme):
    frame = box(slide, SLIDE_W - MARGIN_X - 1.4, COUNTER_TOP, 1.4, 0.3,
                anchor=MSO_ANCHOR.MIDDLE)
    para(frame, f"{index:02d} / 08", size=10, color=theme["counter"], font=BODY,
         align=PP_ALIGN.RIGHT, first=True, spacing=60)


# --------------------------------------------------------------------------- #
# Slide layouts
# --------------------------------------------------------------------------- #


def slide_intro(slide, spec, theme):
    columns = len(spec["blocks"])
    width = (CONTENT_W - 0.5 * (columns - 1)) / columns
    for index, (label, body) in enumerate(spec["blocks"]):
        left = MARGIN_X + index * (width + 0.5)
        rule(slide, left, 3.05, width * 0.34, TEAL_HEX, thickness=3)
        cell = box(slide, left, 3.3, width, 2.2)
        para(cell, label, size=11, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=50, after=6)
        para(cell, body, size=15, color=theme["body"], font=BODY, line=1.25)


def slide_problem(slide, spec, theme):
    rows = [(0, 0), (0, 1), (1, 0), (1, 1)]
    width = (CONTENT_W - 0.3) / 2
    height = 1.55
    for (row, column), (label, body) in zip(rows, spec["blocks"]):
        left = MARGIN_X + column * (width + 0.3)
        top = 2.55 + row * (height + 0.25)
        panel(slide, left, top, width, height, fill="FFFFFF", line=BORDER_LIGHT)
        cell = box(slide, left + 0.28, top + 0.22, width - 0.56, height - 0.4)
        para(cell, label, size=11, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=50, after=5)
        para(cell, body, size=13, color=theme["body"], font=BODY, line=1.2)


def slide_solution(slide, spec, theme):
    stages = [("Assess", "Gap map"), ("Identify", "Prioritised fixes"),
              ("Improve", "Running process"), ("Review", "Review pack")]
    gap = 0.32
    width = (CONTENT_W - gap * (len(stages) - 1)) / len(stages)
    for index, (stage, output) in enumerate(stages):
        left = MARGIN_X + index * (width + gap)
        panel(slide, left, 2.5, width, 1.0, fill="FFFFFF", line=BORDER_LIGHT)
        cell = box(slide, left + 0.22, 2.65, width - 0.44, 0.72)
        para(cell, stage, size=13, color=NAVY, font=DISPLAY, bold=True, first=True, after=2)
        para(cell, output, size=10.5, color=GREY, font=BODY)
        if index < len(stages) - 1:
            arrow = flat(slide.shapes.add_shape(
                MSO_SHAPE.RIGHT_ARROW, Inches(left + width + 0.06),
                Inches(2.88), Inches(gap - 0.12), Inches(0.24),
            ))
            arrow.fill.solid()
            arrow.fill.fore_color.rgb = TEAL
            arrow.line.fill.background()

    width = (CONTENT_W - 0.6) / 3
    for index, (label, body) in enumerate(spec["blocks"]):
        left = MARGIN_X + index * (width + 0.3)
        cell = box(slide, left, 3.85, width, 2.2)
        para(cell, label, size=11, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=50, after=6)
        para(cell, body, size=13.5, color=theme["body"], font=BODY, line=1.22)


def slide_model(slide, spec, theme):
    rows = [
        ("Tier", "Headcount", "Price"),
        ("Starter", "Up to 20 employees", "PKR 40,000 per month"),
        ("Business", "Up to 50 employees", "PKR 75,000 per month"),
        ("Enterprise", "50+ employees", "Custom, scoped after the audit"),
    ]
    table_shape = slide.shapes.add_table(
        len(rows), 3, Inches(MARGIN_X), Inches(2.4), Inches(CONTENT_W), Inches(2.0)
    )
    table = table_shape.table
    table.first_row = False
    table.horz_banding = False
    for column, width in enumerate((3.3, 3.9, 4.433)):
        table.columns[column].width = Inches(width)
    table.rows[0].height = Inches(0.46)
    for row_index in range(1, len(rows)):
        table.rows[row_index].height = Inches(0.52)

    for r, values in enumerate(rows):
        for c, value in enumerate(values):
            cell = table.cell(r, c)
            cell.margin_left = Inches(0.18)
            cell.margin_right = Inches(0.12)
            cell.margin_top = Inches(0.06)
            cell.margin_bottom = Inches(0.06)
            cell.vertical_anchor = MSO_ANCHOR.MIDDLE
            if r == 0:
                fill_cell(cell, NAVY_HEX)
            else:
                fill_cell(cell, ICE_HEX if r % 2 == 1 else None)
            cell_borders(cell, BORDER_LIGHT if r else NAVY_HEX)
            frame = cell.text_frame
            frame.word_wrap = True
            paragraph = frame.paragraphs[0]
            run = paragraph.add_run()
            run.text = value
            run.font.name = DISPLAY if r == 0 else BODY
            run.font.bold = r == 0 or c == 0
            run.font.size = Pt(11 if r == 0 else 12.5)
            run.font.color.rgb = WHITE if r == 0 else (NAVY if c == 0 else SLATE)

    width = (CONTENT_W - 0.45) / 4
    for index, (label, body) in enumerate(spec["blocks"]):
        left = MARGIN_X + index * (width + 0.15)
        rule(slide, left, 4.85, width * 0.4, TEAL_HEX, thickness=2)
        cell = box(slide, left, 5.02, width, 1.35)
        para(cell, label, size=10, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=30, after=4)
        para(cell, body, size=10.5, color=theme["body"], font=BODY, line=1.18)


def slide_market(slide, spec, theme):
    width = (CONTENT_W - 0.3 * 3) / 4
    for index, (label, body) in enumerate(spec["blocks"]):
        left = MARGIN_X + index * (width + 0.3)
        top = 3.5 - index * 0.26
        panel(slide, left, top, width, 2.25, fill=None, line=BORDER_NAVY)
        rule(slide, left + 0.25, top + 0.28, width * 0.28, TEAL_HEX, thickness=3)
        cell = box(slide, left + 0.25, top + 0.5, width - 0.5, 1.6)
        para(cell, label, size=10.5, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=40, after=5)
        para(cell, body, size=11.5, color=theme["body"], font=BODY, line=1.2)


def slide_go_to_market(slide, spec, theme):
    stages = ["Free HR audit", "Gap map", "Retainer"]
    gap = 0.45
    width = (CONTENT_W - gap * 2) / 3
    for index, stage in enumerate(stages):
        left = MARGIN_X + index * (width + gap)
        panel(slide, left, 2.55, width, 0.8, fill="FFFFFF", line=BORDER_LIGHT)
        frame = box(slide, left, 2.55, width, 0.8, anchor=MSO_ANCHOR.MIDDLE)
        para(frame, stage, size=13, color=NAVY, font=DISPLAY, bold=True,
             first=True, align=PP_ALIGN.CENTER)
        if index < 2:
            arrow = flat(slide.shapes.add_shape(
                MSO_SHAPE.RIGHT_ARROW, Inches(left + width + 0.08), Inches(2.83),
                Inches(gap - 0.16), Inches(0.24),
            ))
            arrow.fill.solid()
            arrow.fill.fore_color.rgb = TEAL
            arrow.line.fill.background()

    width = (CONTENT_W - 0.4) / 2
    for index, (label, body) in enumerate(spec["blocks"]):
        left = MARGIN_X + (index % 2) * (width + 0.4)
        top = 3.85 + (index // 2) * 1.35
        cell = box(slide, left, top, width, 1.2)
        para(cell, label, size=10.5, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=40, after=4)
        para(cell, body, size=12, color=theme["body"], font=BODY, line=1.2)


def slide_traction(slide, spec, theme):
    divider_x = SLIDE_W / 2
    vrule(slide, divider_x - 0.008, 2.5, 3.5, TEAL_HEX, thickness=1.6)

    for column, word in ((MARGIN_X, "Shipped"), (divider_x + 0.5, "Tracked")):
        frame = box(slide, column, 2.42, 4.2, 0.3)
        para(frame, word, size=9.5, color=GREY, font=DISPLAY, bold=True,
             first=True, spacing=80)

    left_blocks = spec["blocks"][:2]
    right_blocks = spec["blocks"][2:]

    width = divider_x - MARGIN_X - 0.5
    for index, (label, body) in enumerate(left_blocks):
        cell = box(slide, MARGIN_X, 2.95 + index * 1.75, width, 1.5)
        para(cell, label, size=11, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=50, after=6)
        para(cell, body, size=14.5, color=theme["body"], font=BODY, line=1.22)

    width = SLIDE_W - MARGIN_X - (divider_x + 0.5)
    for index, (label, body) in enumerate(right_blocks):
        cell = box(slide, divider_x + 0.5, 2.95 + index * 1.75, width, 1.7)
        para(cell, label, size=11, color=theme["label"], font=DISPLAY, bold=True,
             first=True, spacing=50, after=6)
        para(cell, body, size=14.5, color=theme["body"], font=BODY, line=1.22)


def slide_roadmap(slide, spec, theme):
    phases = [
        ("Phase I", ["Live site", "Free HR audit", "EmploStart", "Brand system"]),
        ("Phase II", ["Showcase pages", "Editable documents", "Vector logo",
                      "Client proof"]),
        ("Phase III", ["AI automation division", "Pre-launch"]),
    ]
    gap = 0.3
    width = (CONTENT_W - gap * 2) / 3
    for index, (phase, items) in enumerate(phases):
        left = MARGIN_X + index * (width + gap)
        panel(slide, left, 2.4, width, 1.85, fill=None, line=BORDER_TEAL)
        cell = box(slide, left + 0.24, 2.55, width - 0.48, 1.55)
        para(cell, phase, size=10.5, color=WHITE, font=DISPLAY, bold=True,
             first=True, spacing=60, after=6)
        for item in items:
            para(cell, item, size=10.5, color=WHITE, font=BODY, line=1.15, after=1)

    width = (CONTENT_W - 0.45) / 4
    for index, (label, body) in enumerate(spec["blocks"]):
        left = MARGIN_X + index * (width + 0.15)
        rule(slide, left, 4.55, width * 0.4, TEAL_HEX, thickness=2)
        cell = box(slide, left, 4.72, width, 1.6)
        para(cell, label, size=9.5, color=WHITE, font=DISPLAY, bold=True,
             first=True, spacing=30, after=4)
        para(cell, body, size=10, color=WHITE, font=BODY, line=1.18)


LAYOUTS = {
    1: slide_intro,
    2: slide_problem,
    3: slide_solution,
    4: slide_model,
    5: slide_market,
    6: slide_go_to_market,
    7: slide_traction,
    8: slide_roadmap,
}


# --------------------------------------------------------------------------- #
# Assembly
# --------------------------------------------------------------------------- #


def check_copy(slides: list[dict]) -> None:
    if len(slides) != 8:
        raise SystemExit(f"Expected 8 slides, parsed {len(slides)}")
    for spec in slides:
        words = len(spec["headline"].split())
        if words > 9:
            raise SystemExit(f"Slide {spec['number']}: headline has {words} words")
        if not 3 <= len(spec["blocks"]) <= 4:
            raise SystemExit(f"Slide {spec['number']}: {len(spec['blocks'])} blocks")
        for label, body in spec["blocks"]:
            count = len(body.split())
            if count > 18:
                raise SystemExit(f"Slide {spec['number']} [{label}]: {count} words")
        if not spec["notes"]:
            raise SystemExit(f"Slide {spec['number']}: no speaker notes")


def build(slides: list[dict]) -> Path:
    deck = Presentation()
    deck.slide_width = Inches(SLIDE_W)
    deck.slide_height = Inches(SLIDE_H)
    properties = deck.core_properties
    properties.title = "Emplowise pitch deck 2026"
    properties.subject = "Eight slides for buyers and partners"
    properties.author = "Emplowise HR Services"
    properties.last_modified_by = "Emplowise"
    properties.comments = "Source: docs/brand-kit/PITCH_DECK.md"

    blank = deck.slide_layouts[6]

    for index, spec in enumerate(slides, start=1):
        theme = THEMES[GROUNDS[index - 1]]
        slide = deck.slides.add_slide(blank)

        background = slide.background
        background.fill.solid()
        background.fill.fore_color.rgb = theme["ground"]

        logo(slide, theme)
        width_factor = 34 if len(spec["headline"]) <= 36 else 30
        headline(slide, spec["headline"], theme, size=width_factor)
        LAYOUTS[index](slide, spec, theme)
        counter(slide, index, theme)

        notes = list(spec["notes"])
        if spec["evidence"]:
            notes.append("")
            notes.append(f"Evidence: {spec['evidence']}")
        slide.notes_slide.notes_text_frame.text = "\n".join(notes)

    PUBLIC.mkdir(parents=True, exist_ok=True)
    target = PUBLIC / OUTPUT
    deck.save(target)
    return target


def main() -> int:
    if not SOURCE.is_file():
        print(f"Missing source: {SOURCE}", file=sys.stderr)
        return 1
    slides = parse_deck(SOURCE.read_text(encoding="utf-8"))
    check_copy(slides)
    target = build(slides)
    print(f"built {target.relative_to(ROOT)}  ({target.stat().st_size / 1024:.0f} KB, "
          f"{len(slides)} slides)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
