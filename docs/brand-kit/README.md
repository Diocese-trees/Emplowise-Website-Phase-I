# Emplowise brand kit and showcase suite

This folder mirrors the canonical 2026 Emplowise brand, commercial, portfolio
and pitch-deck Markdown authored in the `Emplowise-Docs` repo. The Office files
in `public/downloads/` are generated there and synced here; the hosted pages
`/company-profile`, `/portfolio` and `/pitch-deck` render the same copy.

Nothing here invents a client, a metric, a testimonial or a market size. Where a
detail depends on Emplowise's own records it is written as `[To Verify: ...]`
inside this folder's Verification sections or the deck's speaker notes, and it
never reaches a slide face, a DOCX body or a public page.

## Deliverables

| Markdown source | Office file in `public/downloads/` | Hosted route | Owner | Status |
| --- | --- | --- | --- | --- |
| [`BRAND_KIT.md`](BRAND_KIT.md) | `Emplowise-Brand-Kit-2026.docx` | none, download only | Brand lead | Source complete, live CSS drift flagged |
| [`BRANDING_GUIDELINES.md`](BRANDING_GUIDELINES.md) | `Emplowise-Branding-Guidelines-2026.docx` | none, download only | Brand lead | Source complete |
| [`COMPANY_PROFILE.md`](COMPANY_PROFILE.md) | `Emplowise-Company-Profile-2026.docx` | `/company-profile` | Founder / HR lead | Source complete, open items in the ledger |
| [`PORTFOLIO.md`](PORTFOLIO.md) | `Emplowise-Portfolio-2026.docx` | `/portfolio` | Founder | Source complete, repo-evidenced only |
| [`WEBSITE_BREAKDOWN.md`](WEBSITE_BREAKDOWN.md) | `Emplowise-Website-Breakdown-2026.docx` | none, download only | Engineering | Source complete |
| [`TOOL_STACK.md`](TOOL_STACK.md) | `Emplowise-Tool-Stack-2026.docx` | none, download only | Engineering | Source complete |
| [`PITCH_DECK.md`](PITCH_DECK.md) | `Emplowise-Pitch-Deck-2026.pptx` | `/pitch-deck` | Founder | Source complete, TAM/SAM/SOM in speaker notes only |

Status values mean: **Source complete** (the Markdown is written and gated),
**Generated** (the Office file exists and passed `verify-office`), **Blocked**
(needs a decision or data). The generator prints a per-file audit table; the
release scorecard in `CHANGELOG.md` records the sizes.

## Rebuilding the Office files

Python 3.11 or newer. The canonical tooling lives in the `Emplowise-Docs` repo
(`generators/`); `tools/brand-kit/` in this repository holds the earlier mirror.
Neither touches the site's PHP, Blade, CSS or JavaScript.

```bash
cd ../Emplowise-Docs            # or the staged _staging/Emplowise-Docs/
bash scripts/build.sh           # tokens + 6 DOCX + PPTX + verify
python3 scripts/sync.py exports # copy the built files into public/downloads/
```

`generators/build_docx.py` reads every Markdown file listed above except
`PITCH_DECK.md` and strips the trailing `## Internal Verification Notes` section
before writing, so open questions stay in the repository and out of the
client-facing file. `generators/build_pptx.py` reads `PITCH_DECK.md` and emits
eight native slides with speaker notes. `generators/verify_office.py` asserts
OOXML validity, the 500 KB ceiling, the embedded logo, non-empty headings and
tables, zero raw Markdown tokens and zero `[To Verify]` strings in any body or
slide face. It prints a table and exits non-zero on the first failure.

`public/downloads/` is committed because the static export ships `public/` as
is. Regenerate and commit the Office files in the same change set as the
Markdown that produced them.

## Office font setup

Word, PowerPoint and Google Slides substitute fonts they do not have. To see the
documents exactly as designed, install both families locally:

1. Download Montserrat (weights 700, 800) and Plus Jakarta Sans (weights 400,
   500, 600) from Google Fonts.
2. Install them for the current user (Windows: right-click the `.ttf` files,
   **Install**; macOS: open them in Font Book and choose **Install Font**).
3. Restart Word or PowerPoint. The templates name the fonts explicitly and set
   Arial and Calibri as the fallback pair, so a machine without them still gets
   readable, correctly sized output.

Google Slides keeps the fonts only if they are available to the account; sharing
a deck with an external reader may substitute them. Keep the fallback pair in
mind when judging line breaks.

## Public-render sanitization

- `[To Verify: ...]` lives only in this folder's Verification sections, in
  `PITCH_DECK.md` speaker notes and evidence lines, and in this index.
- The DOCX builder strips the Internal Verification Notes section outright.
- The PPTX builder refuses to emit a slide face containing `[To Verify`.
- The Blade views render the same copy without any bracketed marker; a Feature
  test asserts that no public page contains `[To Verify`.

## Open Evidence Ledger

Every unresolved item below is a question for Emplowise, not a placeholder to be
guessed. Tier references follow the six-tier hierarchy in
[`BRANDING_GUIDELINES.md`](BRANDING_GUIDELINES.md).

| # | Item | Where it is used | Tier | Owner |
| --- | --- | --- | --- | --- |
| 1 | Package-by-package inclusions and limits | Company profile section 07, deck slide 04 | 5 Internal belief until confirmed in writing | Founder |
| 2 | Recruitment fee: included or billed separately | Company profile 07, pricing notes | 5 | Founder |
| 3 | Payroll tax, statutory contribution and disbursement scope | Company profile 07, FAQ | 5 | Founder |
| 4 | Legal advice qualifications and jurisdiction coverage | Company profile 06, FAQ | 5 | Founder |
| 5 | Dedicated representative allocation, full time or shared | Company profile 07 | 5 | Founder |
| 6 | Assessment cadence and industry focus | Company profile 06 | 5 | Founder |
| 7 | Contract length and notice terms | Company profile 07 | 5 | Founder |
| 8 | Verified client results, testimonials, baseline metrics | Portfolio section C, deck slide 07 | 1 Behavioural data or 2 Customer statements, currently absent | Founder |
| 9 | Direct contact phone number and inbox | Company profile 10, footer | 5 | Founder |
| 10 | Market size: TAM, SAM, SOM for remote HR in Pakistan | Deck slide 05 speaker notes only | 3 Dated secondary research, none collected | Founder |
| 11 | Named decision owner for brand approvals | This table, Owner column | 5 | Founder |
| 12 | Official team portraits to replace the placeholder images | Company profile 08, `/what-we-are` | 5 | Founder |
| 13 | Advegen and Creative Nexus relationship wording for the co-founder bio | Company profile 08 | 2 Customer statement or 5 | Founder |
| 14 | Whether `/pitch-deck` stays `noindex` after launch | Route metadata | 5 | Founder |
| 15 | Permission for a named client case study, with numbers and quotes | Portfolio section C, deck slide 07 | 2 Customer statement, none cleared | Founder |
| 16 | Company-level use of the 2,000+ employee figure in the co-founder bio | Company profile 08, portfolio section A2 | 2, pending permission | Founder |
| 17 | Whether the private mirror repository may be cited publicly | Portfolio section B | 5 | Founder |
| 18 | Any partner, accelerator or programme relationship that can be named | Portfolio section B, deck slide 06 | 2 Customer statement or 3 dated source | Founder |
| 19 | Launch date for the AI automation division | Company profile 06, portfolio section D, deck slide 08 | 4 Competitor signal only today | Founder |
| 20 | Ship dates for Phase II (showcase, Office suite, vector logo) and Phase III | Deck slide 08 speaker notes | 5 | Founder |

Items 1 to 9 mirror `config('emplowise.pending_inputs')`, so the site and the
documents cannot drift apart. Item 10 is the only market-sizing question; the
deck keeps a qualitative wedge on the slide and the numbers in the notes.

## Related documentation

- [`../REPO_TOPOLOGY.md`](../REPO_TOPOLOGY.md) explains the three repositories.
- [`../STATIC_HOSTING.md`](../STATIC_HOSTING.md) explains the GitHub Pages build.
- [`../THEME_AND_MOTION.md`](../THEME_AND_MOTION.md) documents the dark mode and
  motion contract behind the live palette.
- [`../../CONTENT_EDITING_GUIDE.md`](../../CONTENT_EDITING_GUIDE.md) explains how
  to edit `config/emplowise.php` without breaking the SEO audit.
