# UX hypotheses and what shipped

Each hypothesis is scored with ICE (Impact, Confidence, Ease, 1–10) and carries
its status. "Shipped" means the code is live on `main`; "Verified" means the UX
gate proves the behaviour; "Blocked" means it needs a decision or facts only the
owner can supply. Nothing here invents trust signals.

## Shipped and verified

| ID | Hypothesis | Change | ICE | Status |
| --- | --- | --- | --- | --- |
| H1 | The hero paragraph pushes both calls to action off a phone screen, so conversion leaks at the top. | Wrap the support copy in a `<details>` that collapses below 641px and stays open on desktop; the summary reads "Why teams switch". | 8·8·9 = 192 | Shipped, verified |
| H2 | Burying destinations behind a "Company" disclosure costs a second tap on phones. | Flatten the drawer into five direct shortcuts, hidden from 1041px up where the disclosure is better. | 7·7·9 = 175 | Shipped, verified |
| H3 | The primary action is out of thumb reach once the hero scrolls away. | Two-action thumb dock appears after 60% of a phone viewport, steps aside for the footer, consent card and chat, never renders on the audit page. | 8·6·7 = 168 | Shipped, verified |
| H4 | A cookie card over the first screen interrupts reading and depresses conversion. | Defer the consent card until a scroll past 60% or any pointer/key input; keep the footer link and Escape. No non-essential cookie loads first, so deferral is compliant. | 7·8·9 = 176 | Shipped, verified |
| H5 | An open phone drawer is a modal sheet, so Tab must not wander behind it. | On keyboard open, focus the first link; trap Tab inside `#main-nav` until it closes; Escape returns focus to the toggle. | 6·9·8 = 168 | Shipped, verified |
| H6 | Header and footer controls smaller than 44px are a fingertip lottery on touch. | 44px minimum on coarse pointers for the brand, theme and menu buttons, header CTA, footer links, footer CTA and legal links. | 6·9·9 = 180 | Shipped, verified |
| H7 | Fading content in on scroll leaves text at reduced opacity, which contrast tooling reads as low contrast and can hide text from automated audits. | Reveal on transform only; text stays fully opaque and in the accessibility tree at all times. | 5·8·9 = 165 | Shipped, verified |
| H8 | Legal and showcase pages had real WCAG contrast and link-distinction defects. | Darkened under-contrast labels within the existing teal/slate hues; underlined inline links in legal and lede prose; corrected the deep-teal deck slide. | 5·9·8 = 170 | Shipped, verified |
| H9 | The documentation jump bar gives no sign it scrolls on a narrow screen. | Proximity scroll-snap, a right-edge fade and a chevron below 900px. | 4·7·9 = 148 | Shipped, verified |
| H10 | On a narrow screen the services table scrolls and the reader loses the row label. | Pin the first column below 900px with an opaque background per theme. | 5·8·9 = 160 | Shipped, verified |

## Already present, re-verified

These were in the codebase before this pass; they were not rebuilt, only checked
against the gate and the test suite.

| ID | Behaviour | Evidence |
| --- | --- | --- |
| C4 | The audit wizard shows a step counter, dots and progress bar, and names the current step to assistive tech. | `public/js/audit.js`, `#wizard-label`, `#wizard-dots`, `#wizard-progress` |
| C5 | Per-field inline errors set `aria-invalid` and clear on correction. | `.field-error`, `setError` / `clearErrors` |
| C6 | Draft answers survive a reload in `sessionStorage` and the review step renders a summary. | `emplowise_audit_draft`, `buildReview()` |
| — | The audit page's side card is a `<div>`, not an `aside`, so it no longer trips the axe landmark rule. | `resources/views/pages/audit.blade.php` |

## Blocked on owner input

These improve trust the most, and none can be shipped without facts. Do not
invent them.

| ID | What it needs | Why it is blocked |
| --- | --- | --- |
| T1 | Real client testimonials with name, role and consent | No verifiable quotes exist in the repo; fabricating them would breach the honesty rules. |
| T2 | Client logos | No written permission on file; a logo wall without it is a false claim. |
| T3 | Case-study numbers (hours saved, error rate, cost) | No measured engagement to cite. |
| T4 | Response-time or satisfaction guarantee | A promise the business has not agreed to make. |
| T5 | Security/compliance badges (e.g. ISO, SOC 2) | The company holds no such certification; a badge would be a lie. |
| T6 | Named team credentials and photos | Placeholder portraits exist; real ones need owner sign-off and image rights. |

## Next experiments

- **Instrument before optimising further.** Wire the event taxonomy in
  `docs/UX-METRICS.md` behind consent, collect two weeks, then re-rank. A B2B site
  at this traffic is underpowered for A/B tests; prefer before/after measurement
  on the audit completion rate over split tests.
- **Audit field friction.** Group `audit_error` by field; the worst field is the
  next change, with the same inline-error pattern already in place.
- **Dock vs header CTA.** Compare `cta_click` click-through by placement on
  phones once the dock has been live long enough to clear the novelty effect.

## Guardrails that did not change

Brand kit, palette, type, logos, routes and the SEO copy freeze are untouched.
Contrast fixes only tuned lightness inside existing brand hues, which the repo
rules allow. The SEO audit still passes all 15 routes and the suite still passes.
