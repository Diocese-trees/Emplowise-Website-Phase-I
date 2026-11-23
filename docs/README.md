# Documentation

This folder holds the published mirrors of the Emplowise documentation. The
**authoring source of truth** is the separate `Emplowise-Docs` repository
(staged in `_staging/Emplowise-Docs/` during the 3-repo pivot), which owns:

- every `.md` source in `docs/` and `docs/brand-kit/`,
- the canonical Brand Kit 2026 tokens (`brand-tokens/tokens.css` and
  `brand-tokens.json`),
- the DOCX/PPTX generators and the verified `exports/`,
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
