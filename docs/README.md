# Documentation

This folder holds the published mirrors of the Emplowise documentation. The
**authoring source of truth** is the separate `Emplowise-Docs` repository
(staged in `_staging/Emplowise-Docs/` during the 3-repo pivot), which owns:

- every `.md` source in `docs/` and `docs/brand-kit/`,
- the canonical Brand Kit 2026 tokens (`brand-tokens/tokens.css` and
  `brand-tokens.json`),
- the DOCX/PPTX generators and the verified `exports/`,
- the internal executive reports (`docs/reports/`, built into `reports/`) and
  their `generators/report-ui/` builder,
- the cross-repo sync scripts.

The main site consumes those outputs: `brand-tokens/tokens.css` is mirrored to
`public/css/tokens.css`, and `exports/*.docx|*.pptx` are mirrored to
`public/downloads/`. The live [`/docs`](../resources/views/pages/docs.blade.php)
page indexes every deliverable with live editions and editable downloads.

## Editing

Change documentation in `Emplowise-Docs`, run `make build` there to rebuild and
verify the Office files, then `make sync` to push the tokens and exports into
this repo. Do not hand-edit the mirrors here; `make sync-check` fails when they
drift.

The Markdown files in this folder remain readable here for convenience and for
the static `/docs` page, but they are not the place to make authoritative edits.

## What is deliberately not mirrored here

Internal executive reports stay in `Emplowise-Docs`. The 2026 executive report
carries `[CANNOT VERIFY]` markers and a labelled fictional case study, so it is
not a public deliverable: it lives in `docs/reports/EXECUTIVE_REPORT_2026.md`
with built artifacts in `reports/`, and `sync.py` copies only `.docx` and
`.pptx` from `exports/`. Nothing from it reaches `public/downloads/`, the
`/docs` hub or any rendered route.

The report is the reference for two proposed additions, one case-study section
and one dashboard preview, and for the twelve-field employee register format.
Neither addition is implemented on this site yet; the frozen copy, the routes
and the appearance are unchanged.
