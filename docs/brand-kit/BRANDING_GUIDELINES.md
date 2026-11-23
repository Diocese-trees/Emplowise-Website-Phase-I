# Emplowise branding guidelines, 2026

How the brand behaves. `BRAND_KIT.md` defines what it looks like; this document
defines how it sounds, what it is allowed to claim, how the interface is built,
and who signs off. It is written for anyone producing a page, a document, a
deck, a social post or an email on behalf of Emplowise.

## 1. Positioning

**Emplowise is a remote HR team for growing businesses in Pakistan.** It is a
service, not software. Buyers keep their headcount and their systems; they gain
a named HR owner, written scope and a predictable cadence, on a published
monthly retainer starting at PKR 40,000.

Two audiences, ranked:

| Rank | Audience | What they are buying | Where they enter |
| --- | --- | --- | --- |
| 1 | Founders and SME owners, 5 to 100 staff, Pakistan | Structure they do not have to hire for | `/`, `/why-emplowise`, `/free-hr-audit` |
| 2 | Students and early career candidates | Free, structured practice and feedback | `/emplostart`, `/faq` |

The second audience never subsidises the first with invented proof. EmploStart is
free and genuinely useful; it is not a lead magnet that demands an email address.

### Buyer situations, not demographics

Write to a situation, never to a label. "You are hiring faster than you are
documenting" is a situation. "Millennials in Karachi" is not. The four situations
the site is built around:

1. Growth outran the paperwork: contracts, onboarding and payroll rhythm are in
   someone's head.
2. A process failed once: a payroll error, a missed confirmation, a resignation
   with no handover.
3. The founder is the HR helpdesk and cannot keep being it.
4. A buyer needs a document to show a co-founder, a board or a bank, and wants
   something credible to circulate.

## 2. Verbal identity

**Voice.** Plain, specific, unhurried. Short sentences. Concrete nouns. A
sentence that could appear on any consultancy site is rewritten or deleted.

**Rules.**

- Lead with the buyer's situation, then the mechanism, then the next step.
- Name the owner of every promise. "Your HR team" is acceptable only where the
  named team is described on the same page.
- Quantify only what is confirmed. Prices and headcount bands are confirmed;
  percentages saved, hours returned and satisfaction scores are not.
- Say what is out of scope in the same breath as what is in scope.
- Use British spelling for Pakistan-facing copy: organise, prioritise,
  optimisation. The wordmark and product names keep their own spelling.
- Never use an em dash in public copy; the site's SEO audit rejects it.
- Banned words: leverage, ecosystem, landscape, holistic, seamless. The audit
  enforces these five.
- No exclamation marks. No "revolutionary", "game-changing", "next-generation".
- Never promise a legal outcome, guaranteed productivity, or board authority.

**Naming lock.** The toolkit is `EmploStart`, always, one word, capital E and S.
`Student Hub` is retired; `/student-hub` survives only as a redirect. The company
is `Emplowise`; the legal entity is `Emplowise HR Services`.

**SEO alignment.** The public copy is frozen by contract. Titles, descriptions,
h1s, intros and FAQs come from `config/emplowise.php` and are audited for length,
keyword placement and uniqueness. New documents may reframe that copy for print
but must not contradict a number, a service name or a scope statement. If a
document needs a claim the site does not make, the claim goes to the Open
Evidence Ledger first.

## 3. Evidence discipline

Every factual statement carries an implicit tier. Higher tiers win. If two
sources disagree, the higher tier is used and the conflict is written down.

| Tier | Type | Example | Weight |
| --- | --- | --- | --- |
| 1 | Behavioural data | Audit submissions per week, tool completions, retention | Strongest |
| 2 | Customer statements | A named client's quote, a signed reference | Strong |
| 3 | Dated secondary research | A 2025 labour-market report with a URL and a date | Moderate |
| 4 | Competitor signals | A published price on a competitor site | Weak, context only |
| 5 | Internal belief | "Founders hate payroll spreadsheets" | Hypothesis |
| 6 | Untested hypothesis | "SMEs will pay for a dashboard" | Not usable as a claim |

**Operating rules.**

- A claim in tier 5 or 6 may shape positioning, never a statistic.
- No fabricated clients, testimonials, revenue, logos, case studies, awards or
  TAM/SAM/SOM. If it is not evidenced, it is not published.
- Distinguish three different things and never merge them in a table:
  **Inputs and Outputs** (what we do and produce), **Audience Effects** (what
  changes for the buyer's team), **Commercial Outcomes** (revenue, retention,
  cost). Only the first is fully evidenced today.
- Date every external source. An undated statistic is tier 6.
- Keep the Open Evidence Ledger in [`README.md`](README.md) current. A document
  that raises a new question adds it there before the document ships.

### Public-render sanitization

A verification note is an internal marker written as a square-bracketed `To Verify:` followed by the open question. It is permitted only in:

- `docs/brand-kit/*.md` internal Verification sections,
- `PITCH_DECK.md` speaker notes and evidence lines,
- the Open Evidence Ledger in `docs/brand-kit/README.md`.

It is forbidden in DOCX bodies, PPTX slide faces and public HTML. The Office
builders strip or refuse it, and a Feature test asserts that no public page
contains the string. When a document is exported for a client, the question
stays in the repository and the client sees the confirmed sentence or nothing.

## 4. Scope boundaries

Emplowise does four things and says so:

1. **Assess** the current HR state and produce a written gap map.
2. **Identify** causes, owners and a sequence.
3. **Improve** by installing policies, SOPs, payroll calendars, onboarding and
   reporting.
4. **Review** on a monthly and quarterly cadence with a leadership pack.

Emplowise does not: provide legal advice as a law firm, act as a payroll
disbursement agent beyond the agreed scope, guarantee compliance outcomes,
guarantee hiring results, take board authority, or replace a client's
management. Recruitment fees, payroll tax scope, legal qualifications,
representative allocation and contract terms are confirmed in writing after the
Free HR Audit. That sentence is not a hedge; it is the scope boundary.

## 5. Interface rules

These apply to every page, including the showcase pages. They are implementation
rules, not preferences.

**Layout.**

- 1 px borders (`#E4EAF1` light, `rgba(148, 163, 184, 0.16)` dark). Never a 2 px
  border on a card, never a border plus a heavy shadow.
- One idea per section. A section that needs three competing calls to action has
  no call to action.
- Nothing overlaps the sticky header: every anchor target sets
  `scroll-margin-top` to clear `--header-h` plus breathing room.
- Wide tables sit inside a `.table-scroll` wrapper with `overflow-x: auto`, so a
  360 px viewport never scrolls sideways at page level.

**Contrast and targets.**

- Body text: WCAG AA, 4.5:1 minimum. Large text and UI boundaries: 3:1.
- Interactive targets: at least 44 by 44 px on touch, 28 by 28 px minimum for
  inline footer links on pointer devices.
- Focus is always visible and uses the teal ring; never `outline: none` without
  a replacement.
- Colour is never the only signal. Errors carry text and an icon.

**Motion.**

- Opacity and transform only. No layout animation, no parallax.
- Durations come from `--dur-fast`, `--dur`, `--dur-slow`. Nothing longer than
  950 ms.
- `prefers-reduced-motion: reduce` disables every non-essential transition and
  animation; this is a hard requirement, not a nicety.

**Content security.**

- `script-src 'self'` with a SHA-256 hash for the single inline theme bootstrap.
  No new inline script, no `onclick`, no third-party tag manager.
- No inline `style` attributes in Blade or in exported HTML; utilities live in
  `inline.css`.
- New page-specific CSS goes in its own file loaded through `@push('styles')` on
  the pages that need it. A showcase stylesheet must not be added to `site.css`,
  because that would ship it to every page.
- Download links use the `download` attribute and a subpath-safe URL, so the
  same markup works on Laravel and on GitHub Pages.
- Never put a secret, a key or an internal URL in a page, a deck or a document.

## 6. Channel matrix

| Channel | Ground | Length | Call to action | Never |
| --- | --- | --- | --- | --- |
| Website page | White or ice, dark mode supported | 600 to 1,200 words | Free HR audit | New SEO copy on a frozen page |
| Website showcase | White, ice or navy panel | 900 to 1,800 words | Download the document, then audit | Unresolved verification notes, inline script |
| PPTX deck | Navy, white, mist, deep teal | 8 slides, 18 words per answer | Book the audit | Slide screenshots, stock imagery |
| DOCX report | White with mist banding | 4 to 12 pages | Audit or contact | A claim with no tier |
| Free HR Audit output | White | 2 pages | Confirm the audit window | Percentages with no baseline |
| LinkedIn | White or navy | 120 to 200 words | One link, one idea | Hashtag walls, engagement bait |
| Email | White, single column | 80 to 140 words | One action | Attachments over 2 MB, tracking pixels before consent |

## 7. Decision owners

| Decision | Owner | Consulted |
| --- | --- | --- |
| Brand system changes, logo, palette | Brand lead | Founders |
| Commercial claims, pricing, scope | Founder | HR lead |
| Public page copy and SEO | Founder | Engineering |
| Documentation and evidence tiers | Brand lead | Founder |
| Technical implementation, CSP, performance | Engineering | Brand lead |
| Release approval | Founder | Engineering |

A named brand lead is still to be confirmed; until then the founder holds that
decision. The gap is item 11 in the Open Evidence Ledger.

## 8. Seven-point creative evaluation rubric

Score every artefact 1 to 5 on each point before it ships. Below 28 of 35, fix
the weak points or do not ship. Record the score in the pull request or the
document's cover note.

| # | Criterion | Question | 1 means | 5 means |
| --- | --- | --- | --- | --- |
| 1 | Strategic fit | Does this serve a buyer situation the business actually sells to? | Decorative, audience unclear | Names the situation and the next step |
| 2 | Recognition | Would a reader know this is Emplowise with the logo covered? | Generic consultancy | Navy, teal, structure and cadence are unmistakable |
| 3 | Distinctiveness | Does it avoid the category's stock moves? | Handshake photo, gradient blob, "synergy" | Specific mechanism, published price, named owner |
| 4 | Legibility and accessibility | Can it be read at 360 px, in dark mode, with a screen reader? | Contrast failures, tiny targets | AA contrast, 44 px targets, logical headings |
| 5 | Flexibility | Does it survive a longer sentence, a fallback font, a translation? | Breaks on one extra line | Holds at 20 percent longer text and in Arial |
| 6 | Feasibility | Can it be built and maintained with this stack? | Needs a framework the repo does not have | Uses existing tokens, bundles and commands |
| 7 | Cultural and legal risk | Could a claim, image or phrase cause harm or overpromise? | Invented proof, legal guarantee | Evidence tier stated, scope boundary explicit |

Two extra gates for anything public:

- **Public-render check.** Search the artefact for `To Verify` markers, unfinished
  placeholders and sample names. Any hit blocks the release.
- **Evidence check.** Every number traces to a tier 1 to 3 source or is labelled
  as a target rather than a result.
