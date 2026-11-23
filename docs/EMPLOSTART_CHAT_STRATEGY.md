# Strategy: EmploStart and Chatbot Upgrade

> Historical record of the five-tool upgrade strategy. EmploStart now carries six modules; see `AGENTS.md` for the current contract.

Phase 3 of 5. Inputs: `EMPLOSTART_CHAT_UPGRADE_PLAN.md` and
`EMPLOSTART_CHAT_RESEARCH.md`. Baseline: `4b231ff`.

## 1. Principles

1. Diagnose, do not decorate. Every new number must come with the reason
   behind it and one concrete next action.
2. Recover, do not punish. Autosave the builder, fall forward in chat, and
   never leave a dead end without the Free HR Audit link.
3. Stay honest. Heuristic scores are labelled as heuristic. Local mode is
   labelled as local. No invented statistics or hiring guarantees.
4. Small, reversible commits. One behaviour per commit, tests included, no
   bundled refactors.

## 2. Priority ranking

Scored by user impact (1–5), confidence in the evidence (1–5), and effort
inverse (1–5). Ties break toward the tool earlier in the tab order.

| # | Change | Impact | Evidence | Effort | Score | Commit |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | Interview score breakdown + next probe | 5 | 5 | 4 | 14 | C2 |
| 2 | Chat: scored intents, history, suggestions, links | 5 | 5 | 3 | 13 | C6 |
| 3 | ATS: synonym/stem matching + title check | 5 | 4 | 3 | 12 | C3 |
| 4 | Resume builder autosave + example + readiness | 4 | 5 | 3 | 12 | C4 |
| 5 | Profile target role + copy blocks | 3 | 4 | 4 | 11 | C5 |
| 6 | Prompt library counts + clear + copy visible | 3 | 3 | 5 | 11 | C7 |
| 7 | Accessibility pass | 3 | 4 | 4 | 11 | C6/C8 |
| 8 | Docs refresh | 2 | 5 | 5 | 12 | C8 |

## 3. Decisions

### D1. Interview feedback exposes dimensions, not a new score

Return the four existing dimensions (structure 35, evidence 25, clarity 25,
ownership 15) in the API response and render them as small bars under the ring.
Keep the composite score and its bounds unchanged so existing tests and user
expectations hold. Add a `next_probe`: one sentence generated from the first
missing STAR element, so the learner immediately practices the weak part.

Evidence: STAR is the structure interviewers score against, and a 60–90 second
action-heavy answer is the target. Missing-element feedback is actionable;
a bare score is not.

Rejected: a five-dimension model rewrite. It changes score distributions
without evidence and breaks bounds tests.

### D2. ATS matching gains synonyms and a title check, not a new scale

Match exact tokens first, then a curated synonym group (for example
`js`/`javascript`, `powerbi`/`power bi`, `communication`/`communicate`) at
reduced credit, then a simple suffix stem. Keep the 55/25/20 weighting. Add:

- phrase rescue: job-title bigram and any bigram in the first two lines of the
  posting are treated as critical even at frequency one;
- title alignment: does the target title appear in the resume header or
  summary;
- stuffing warning: a term repeated more than six times is called out as a
  readability risk rather than rewarded.

Scores stay clamped 3–98. Synonym credit is capped so a synonym-only match can
never score like an exact match.

Evidence: recruiters search a keyword index by keyword and title, and parsers
depend on conventional headings and single-column text.

Rejected: TF-IDF or embedding similarity. No dependencies allowed, and it
would make the score unexplainable.

### D3. Resume builder autosaves locally, visibly, and clears on demand

Persist the form (identity, summary, roles, education, skills) to
`localStorage` under `emplowise_resume_draft_v1`, debounced on input. Show a
quiet "Saved in this browser" status. Add "Load example" and "Clear draft".
Never store interview answers, ATS pastes, or chat text.

Privacy note updated in the resume panel: the draft stays in this browser and
can be cleared at any time.

Evidence: autosave prevents accidental loss and needs visible save state;
localStorage is unsuitable for genuinely sensitive data, so scope and
disclosure are the mitigation.

Rejected: server-side draft storage. It would create the account and data
retention surface this site explicitly avoids.

### D4. Chat uses scored intents, conversation context, and fall-forward

- Score every intent rule and take the best; if the best score is below a
  threshold or tied, ask one clarifying question with three options.
- Use the last assistant intent as a weak prior for follow-ups ("and for 20
  people?" stays in pricing).
- Return `suggestions` (2–4 labels/queries) with every reply and render them
  as chips above the input.
- Linkify only the known internal paths `/free-hr-audit` and `/student-hub`
  into anchors; everything else stays text.
- Keep the hard caps: 4 sentences, 5 lines, 260 characters.
- Add a "Start over" control that clears the transcript and session id.

Evidence: fallback is a core path; closest matches plus escalation beat a
generic loop; 2–4 suggested replies reduce blank-input friction.

Rejected: free-form intent classification by the model. It would make local
and model modes behave differently and is not needed for 18 intents.

### D5. Profile optimizer takes an optional target role

Add a "Target role" input. When present, headlines and summary are aimed at
it; when absent, derive the role from the headline as today. Add copy buttons
for the summary and the bullet block. No change to the rewrite rules.

### D6. Prompt library shows counts and supports bulk copy

Add a visible count per category and an "All" count, a clear-search control,
and "Copy visible" which copies the currently filtered prompts with titles.
Keep exactly 14 prompts so the site's published fact stays true.

### D7. Accessibility is part of the chat commit, not a later sweep

Typing indicator marked `aria-hidden` so it is not announced as content.
Suggestion chips are real buttons with accessible names. Focus returns to the
input after a reply. Reset announces via the existing polite log. All new
EmploStart inputs have labels; score bars get `role="img"` with a text label.

## 4. Commit plan

Branch: `feat/student-hub-chat-upgrade`.

- C1 `docs(student-hub): add plan, research, and strategy` (this phase).
- C2 `feat(interview): expose score dimensions and a next probe`.
- C3 `feat(ats): match synonyms, rescue critical phrases, check title`.
- C4 `feat(resume): autosave drafts locally with restore and clear`.
- C5 `feat(profile): accept a target role and aim the rewrite`.
- C6 `feat(chat): score intents, suggest follow-ups, link internal routes`.
- C7 `feat(prompts): show category counts and copy visible results`.
- C8 `docs: update editing guide for the new controls`.

Each commit: `php artisan test` green, `php artisan emplowise:seo-audit` green,
no inline styles or scripts, facts unchanged.

### Status

All eight commits are on `feat/student-hub-chat-upgrade` in the order above.
C1–C7 are committed; C8 is the docs refresh that carries this status note.
The test suite stood at 37 tests / 344 assertions when C7 landed.

### Static-first follow-up

The GitHub Pages build no longer depends on a backend:

- `public/js/toolkit/interview.js`, `ats.js`, and `profile.js` are browser ports
  of `StudentToolkit`, loaded only on the hub. Their output is checked against
  PHP-generated fixtures with `tools/parity/check-*.mjs`.
- `php artisan emplowise:export-static` renders the seven pages with the base
  path applied, injects a CSP meta policy, strips the `#student-config` endpoint
  URLs, and points the booking form at Web3Forms.
- `.github/workflows/publish-static.yml` republishes `gh-pages` on every push to
  `main`, so the demo cannot drift from the source again.
- `student.js` and `chat.js` treat 404/405/5xx as "use the local engine", so an
  accidental API call on a static host degrades instead of showing an error.

Suite after the follow-up: 44 tests / 402 assertions.

## 5. Acceptance checks

- `grep -R "style=" resources/views` returns nothing new.
- All API responses still pass their existing shape tests.
- ATS score bounds 3–98 and breakdown maxima 55/25/20 unchanged.
- Interview score bounds and determinism unchanged for the same input.
- Chat caps: every fallback reply ≤ 5 lines and ≤ 260 characters.
- Resume autosave restores after reload in a manual check; clearing removes
  the key.
- Facts audit: 5 tools, 14 prompts, PKR 40,000/20, 75,000/50, Enterprise
  custom, 14 services, 4 stages.
- No new dependency in `composer.json` or `package.json`.

## 6. Rollback

Every commit is independent. Reverting C2–C8 in reverse order returns the site
to `4b231ff` behaviour. The pre-work backup tag and bundle are the last
resort, not the first.
