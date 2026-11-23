# Emplowise Brand Kit 2026

The brand system as it should be built, held up against the system as it is
built today. Where the live stylesheets drift from the specification, the
difference is named in section 04 rather than smoothed over. Hex values are
lowercase in CSS and uppercase in print specifications; they are the same
colours.

## 01 Brand Essence

**Tagline.** Clarity Built on Structure.

**Philosophy.** Empower people. Simplify work. Build momentum.

**Essence.** Senior HR, without the senior hire. Emplowise is a remote HR team
for startups and SMEs in Pakistan, sold as a monthly retainer rather than a
software licence. The brand's job is to make an intangible service feel
structured: named owners, written scope, published prices, predictable cadence.

**Personality.** Calm, exact, accountable. It explains rather than impresses. It
would rather say "confirmed in writing after your Free HR Audit" than invent a
number. It never sounds like a consultancy pitching transformation, and it never
sounds like a job board chasing clicks.

**Two audiences, one system.** B2B buyers (founders and SME owners) are primary.
Career pivoters and early career candidates using EmploStart are secondary. The visual system is
identical for both; the density and the call to action are not. A buyer page
ends in an audit booking. A tool page ends in using a tool.

**What the brand refuses.** Neck-breaking hero animation, neon glows, gradient
washes used as decoration, blurred drop shadows, invented client logos, fake
dashboards, stock photography of handshakes, and any claim with no evidence
behind it.

## 02 The Logo

Three approved lockups. All ship as raster derivatives today; a vector master is
still outstanding.

| Lockup | Use | Files in `public/img/` |
| --- | --- | --- |
| Primary lockup, horizontal | Light grounds: white, mist, ice | `logo-primary.png`, `logo-primary-375.png`, `logo-primary-375.webp`, `logo-primary-250.webp`, `logo-primary-125.webp` |
| Reverse lockup, horizontal | Navy or deep teal grounds, dark mode footer | `logo-reverse.png`, `logo-reverse-375.png`, `logo-reverse-375.webp`, `logo-reverse-306.png`, `logo-reverse-306.webp`, `logo-reverse-250.webp`, `logo-reverse-204.webp`, `logo-reverse-125.webp`, `logo-reverse-102.webp` |
| Standalone icon | Favicon, avatar, small square placements | `public/img/logo-mark.png`, `public/favicon.png`, `public/favicon.ico` |

Construction: an isometric, layered "E" built from three stacked planes, paired
with an uppercase EMPLOWISE wordmark. The mark carries the structure metaphor;
the wordmark carries the name. Never separate the wordmark from the mark inside
the horizontal lockups, and never re-letter the wordmark in another typeface.

**SVG gap.** The repository has no `.svg` logo. Everything is a PNG or WebP
derivative, which is why the site ships `<picture>` elements with fixed pixel
widths. This is a documented gap: a vector master is required before any
large-format print, signage or merchandise run. Until then, print vendors must
be given the 375 px PNG and told its ceiling.

**Do not.** Recolour the mark, add a gradient to it, outline it, place it on a
busy photo, crop it, rotate it, add a shadow, rebuild it from type, or use the
reverse lockup on a light ground.

## 03 Space and Use

**Clear space.** X equals the cap height of the wordmark EMPLOWISE. Keep X of
empty space on all four sides of any lockup. Nothing enters that zone: no type,
no rules, no image edge, no button border.

**Minimum size.** 24 mm wide in print. 96 px wide on screen for the horizontal
lockup. Below 96 px, use the standalone icon instead of shrinking the wordmark
into illegibility.

**Placement.** Top left in the site header and in documents, centred on cover
pages where the cover is otherwise empty. Never bottom right, never over a
gradient, never inside a rounded card that crops the mark.

**Contrast.** Primary lockup on white, mist or ice. Reverse lockup on navy or
deep teal. On photography, use a solid navy plate behind the reverse lockup
rather than a scrim.

**Co-branding.** A partner mark sits at the same optical height, separated by
2X, with Emplowise first unless a signed agreement says otherwise. No lockup is
created by merging the two marks.

## 04 Colour System

Eight approved swatches. CMYK values are the print specification; they are
approximations of the sRGB values, so always proof a print run.

| Swatch | HEX | RGB | CMYK | Role |
| --- | --- | --- | --- | --- |
| Navy | `#081B3E` | 8, 27, 62 | 87, 56, 0, 76 | Primary ink, headers, reverse ground, primary button in dark contexts |
| Teal | `#0D9488` | 13, 148, 136 | 91, 0, 8, 42 | Primary action, accents, dividers, focus rings |
| Deep Teal | `#0F766E` | 15, 118, 110 | 87, 0, 7, 54 | Reverse ground, accessible teal text on light, hover state |
| Aqua | `#5EEAD4` | 94, 234, 212 | 60, 0, 9, 8 | Single highlight, dark-mode links, small emphasis |
| Ice Teal Mist | `#F0F9F9` | 240, 249, 249 | 4, 0, 0, 2 | Section ground, table banding, callout fill |
| Off-White | `#F7F7F7` | 247, 247, 247 | 0, 0, 0, 3 | Neutral ground for dense documents |
| Slate Charcoal | `#1E293B` | 30, 41, 59 | 49, 31, 0, 77 | Body text on light, secondary ink |
| Pure White | `#FFFFFF` | 255, 255, 255 | 0, 0, 0, 0 | Primary light ground, reverse type |

### Proportion

Roughly 70 percent light ground, 20 percent navy or deep teal ground, 10 percent
teal and aqua accents. A page that is 40 percent teal is not on brand. Accent
colour marks action or emphasis; it never fills a large background on a light
page.

### WCAG pairings

Measured with the WCAG 2.1 relative-luminance formula. "AA body" needs 4.5:1;
"AA large" and non-text UI need 3:1.

| Foreground | Background | Ratio | Verdict |
| --- | --- | --- | --- |
| Navy `#081B3E` | White `#FFFFFF` | 16.98:1 | AA body, AAA |
| Navy `#081B3E` | Ice `#F0F9F9` | 15.87:1 | AA body, AAA |
| Navy `#081B3E` | Off-white `#F7F7F7` | 15.85:1 | AA body, AAA |
| Slate `#1E293B` | White `#FFFFFF` | 14.63:1 | AA body, AAA |
| White `#FFFFFF` | Navy `#081B3E` | 16.98:1 | AA body, AAA |
| White `#FFFFFF` | Deep Teal `#0F766E` | 5.47:1 | AA body |
| Deep Teal `#0F766E` | White `#FFFFFF` | 5.47:1 | AA body |
| Deep Teal `#0F766E` | Ice `#F0F9F9` | 5.11:1 | AA body |
| Aqua `#5EEAD4` | Navy `#081B3E` | 11.48:1 | AA body, AAA |
| Teal `#0D9488` | White `#FFFFFF` | 3.74:1 | AA large and UI only, **not** body text |
| White `#FFFFFF` | Teal `#0D9488` | 3.74:1 | AA large and UI only, **not** body text |
| Teal `#0D9488` | Ice `#F0F9F9` | 3.50:1 | AA large and UI only |
| Aqua `#5EEAD4` | Deep Teal `#0F766E` | 3.70:1 | AA large and UI only |

The practical rule: **teal `#0D9488` is an accent, not a body-text colour.** For
white text on teal at body size, use deep teal `#0F766E`. The live teal button
does not yet follow this: `.btn--teal` paints `--grad-teal` (deep teal, then teal,
then `#22d3ee` cyan) and sets white type, so the label falls from 5.47:1 at the
start of the gradient to 3.74:1 in the middle and about 1.8:1 at the cyan end.
That is a real contrast defect, recorded here rather than smoothed over. The
fix is either a flat deep teal fill or white type restricted to the dark end.

### CSS token reconciliation

`public/css/tokens.css` is the implementation. Seven of the eight swatches match
exactly; the eighth drifts. On top of the eight, the tokens add one dark-only
navy and a set of accents, gradients and glows that the specification excludes.

| Spec | Token | Status |
| --- | --- | --- |
| Navy `#081B3E` | `--navy: #081b3e` | Exact |
| Teal `#0D9488` | `--teal: #0d9488` | Exact |
| Deep Teal `#0F766E` | `--teal-deep: #0f766e` | Exact |
| Aqua `#5EEAD4` | `--aqua: #5eead4` | Exact |
| Ice `#F0F9F9` | `--ice: #f0f9f9` | Exact |
| Off-White `#F7F7F7` | `--offwhite: #f7f7f7` | Exact |
| Pure White `#FFFFFF` | `--white: #ffffff` | Exact |
| Slate Charcoal `#1E293B` | `--slate: #16233c` | **Drift.** The live slate is darker and bluer. Body copy uses `--ink-muted: #54637a` and `--slate-600: #475569`; neither equals the spec swatch. Closing this means either updating the token or amending the spec. |
| not in spec | `--navy-900: #050f26` | **Extension.** Used as the dark canvas and `theme-color` in dark mode. It reads as navy, but it is a ninth colour and is not part of the eight-swatch system. |
| not in spec | `--cyan #22d3ee`, `--indigo #4f46e5`, `--violet #7c3aed`, `--magenta #d946ef`, `--amber #f59e0b`, `--amber-deep #92400e`, `--rose #e11d48` | **Drift.** Full-chroma accents for live data, the student toolkit, scope warnings and risk states. They are visually louder than the eight-swatch system allows and are the main reason the site feels more "product" than "executive". |
| not in spec | `--grad-brand`, `--grad-teal`, `--grad-aqua`, `--grad-student`, `--grad-navy`, `--grad-amber`, `--grad-sheen`, `--grad-hairline` | **Drift.** The 2026 spec bans gradients. The live hero, buttons and card edges still use them. Removing them is a visual decision for the brand owner, not a bug fix. |
| not in spec | `--glow-aqua`, `--shadow-teal`, coloured shadows | **Drift.** The spec bans glow. These are soft, low-opacity, and brand-tinted rather than neon, but they are still glow. |

None of this is hidden in the CSS. The six exact swatches carry the identity;
the extensions and gradients are the remaining distance between the interface
and the specification.

## 05 Typography

Two families, both self-hosted, both latin subset only because the site copy is
English.

| Role | Family | Weights | Files |
| --- | --- | --- | --- |
| Display and headings | Montserrat | 700, 800 | `public/fonts/montserrat-e56e84e5-latin.woff2` |
| Body and UI | Plus Jakarta Sans | 400, 500, 600 | `public/fonts/jakarta-fdb05bf0-latin.woff2` |

Both faces are declared in `public/css/fonts.css` with `font-display: swap`, a
`unicode-range` limited to Latin, and no third-party origin, which is what keeps
`font-src 'self'` viable. One file per family serves every listed weight because
the files are full-range builds; the browser synthesises nothing at 700 or 800
because the file already contains those weights.

**Fallbacks.** Montserrat falls back to Helvetica Neue, Arial, sans-serif. Plus
Jakarta Sans falls back to Segoe UI, system-ui, -apple-system, sans-serif. Office
documents fall back to Arial and Calibri. Long headlines may reflow by a line
when the fallback is used; that is expected, and the Office templates reserve
about 20 percent extra vertical room for it.

**Tracking.** Headings use tight tracking (`-0.015em` to `-0.035em` at large
sizes). Body text uses normal tracking. Never letter-space body copy to fill a
line, and never set a paragraph in Montserrat.

**Office installation.** Install Montserrat (700, 800) and Plus Jakarta Sans
(400, 500, 600) from Google Fonts before reviewing a DOCX or PPTX. Steps are in
[`README.md`](README.md).

## 06 Type Hierarchy

Print and deck sizes in points, web sizes in `rem` at a 16 px root.

| Level | Print and deck | Web | Weight | Colour |
| --- | --- | --- | --- | --- |
| Cover title | 30 pt, line 1.05 | n/a | Montserrat 800 | Navy, or white on navy |
| H1 | 24 to 26 pt | `clamp(2rem, 4.2vw, 2.875rem)` | Montserrat 800, line 1.06 | Navy |
| H2 | 15 to 16 pt | `clamp(1.55rem, 2.8vw, 2rem)` | Montserrat 800 | Navy or deep teal |
| H3 | 12 pt | `clamp(1.1rem, 1.5vw, 1.25rem)` | Montserrat 700 | Navy |
| H4 | 10.5 pt bold | `1.05rem` | Montserrat 700 | Navy |
| Body | 10.5 pt, line 1.35 | `1rem`, line 1.65 | Plus Jakarta Sans 400 | Slate `#1E293B` |
| Small print | 8.5 pt | `0.85rem` | Plus Jakarta Sans 400 | Slate, 70 percent |
| Label and kicker | 8 pt, uppercase, `0.08em` tracking | `0.78rem` | Plus Jakarta Sans 600 | Deep teal or teal |

Alignment: left. No centred paragraphs longer than two lines. No justified text
in web or Office output, because justification opens rivers at narrow widths.
Hyphenation stays off; the copy is short enough to break naturally.

## 07 On Every Surface

**Approved grounds.** Four, no others:

1. White `#FFFFFF` for documents and dense reading.
2. Mist, either `#F7F7F7` or `#F0F9F9`, for section alternation and callouts.
3. Navy reverse `#081B3E` for covers, hero panels and the deck's first slide.
4. Deep teal reverse `#0F766E` for the closing slide and one dark callout per
   document, never both navy and deep teal on the same spread.

**Web dark surfaces.** The site adds a fifth, screen-only ground: Midnight Navy,
`#050F26` canvas with `#08182F` cards and `#0E2444` elevated surfaces
(`theme.css`). It is a dark adaptation of the navy ground, not a new brand
colour, and it never appears in print. Aqua `#5EEAD4` becomes the link colour in
dark mode because teal at 3.7:1 is not readable on navy.

**Per surface:**

| Surface | Ground | Type | Notes |
| --- | --- | --- | --- |
| Website, light | White or ice | Navy and slate | 1 px `#E4EAF1` borders, no heavy shadows |
| Website, dark | Midnight Navy | `#E9EFF8` and aqua | Explicit local variables, never inherited inversion |
| DOCX | White with mist banding | Navy headings, deep teal H2 | Tables repeat their header row, rows never split across pages |
| PPTX | Slide-by-slide navy, white, mist, deep teal | White or navy per ground | Native shapes only, no slide screenshots |
| Audit report | White | Navy headings, teal rules | 2 pt rule under the title, gaps listed as a table |
| LinkedIn | White or navy | Navy or white | One idea per image, no text smaller than 24 px in a 1080 px frame |
| Email | White | Navy headings, slate body | Single column, 600 px maximum, no background images |

**Print safety.** Keep a 12 mm margin on A4, never place type inside 8 mm of the
trim, and convert dark grounds to a rich navy rather than a flat 100 K black.
Navy `#081B3E` at 87/56/0/76 is a rich build; ask the printer to confirm total
ink coverage.

**Motion.** Web motion is limited to short opacity and transform transitions
(`--dur-fast: 0.22s`, `--dur: 0.42s`), all disabled under
`prefers-reduced-motion: reduce`. Documents and decks do not animate.
