# Student Hub and Chatbot Upgrade: Plan

Status: phase 1 of 5 (plan) — written before research.
Scope owner: Emplowise website.
Baseline commit: `4b231ff` on `fix/speed-bugs-scroll-qol`.

## 1. Why this work exists

The Student Hub is the site's free, no-account acquisition surface: five tools
(mock interview, ATS score, resume builder, profile optimizer, prompt library).
The chat widget is the site's only always-on answering surface. Both were built
as deterministic local engines with an optional Alibaba Cloud model behind a
server endpoint. That architecture is sound. The gaps are in depth and in
follow-through:

- The interview tool returns one composite score with no breakdown, so a
  learner cannot see which dimension to fix first.
- ATS matching compares surface tokens only. It misses synonyms, inflected
  forms, unique but critical phrases, and job-title alignment.
- The resume builder keeps state only in the page; a refresh loses everything.
- The profile optimizer cannot be aimed at a specific target role.
- The prompt library is a flat list with no categories count or bulk action.
- The chat widget has one-shot intent matching, ignores history in local mode,
  cannot link to the audit page, and offers no suggested next question.

## 2. What "improve" means here (acceptance)

Every change must stay inside the existing constraints:

- Laravel 13, PHP 8.3, no new runtime dependencies, no Node build step, no CDN.
- CSP stays strict: no inline `style=`, no inline executable script.
- Copy stays centralised in `config/emplowise.php` where it is user-facing.
- Facts stay exact: 5 tools, 14 prompts, Starter PKR 40,000 / 20, Business
  PKR 75,000 / 50, Enterprise custom 50+, 14 services, 4 stages, no invented
  metrics, clients, testimonials, or legal guarantees.
- Every server-side change ships with a feature test. Every commit is green
  (`php artisan test`) and passes `php artisan emplowise:seo-audit`.

Definition of done for the phase: all five tools measurably more useful, the
chat widget more capable and more honest about uncertainty, documentation
updated, backup verified, changes delivered as one reviewable commit each.

## 3. Work sequence (user-mandated order)

1. Plan (this document).
2. Research: external evidence with citations plus the code audit already
   completed. Output: `docs/STUDENT_HUB_CHAT_RESEARCH.md`.
3. Strategy: prioritized, effort-ranked decisions with explicit non-goals.
   Output: `docs/STUDENT_HUB_CHAT_STRATEGY.md`.
4. Backup: git tag plus bundle plus archived frontend, backend, and docs,
   verified by checksum, stored outside the repository.
5. Implementation: one change per commit, tests first where behaviour changes.

## 4. Research questions

Each question maps to a concrete decision. Sources are cited in the research
note; claims that cannot be sourced are marked as design judgement.

R1. How do real applicant tracking systems match resumes to postings, and which
    matching behaviours are worth imitating in a free local tool?
    Decision: keyword engine design (synonyms, stems, phrases, weights).

R2. What makes interview-practice feedback actionable rather than just a score?
    Decision: interview feedback dimensions and follow-up probing.

R3. Which resume-builder behaviours prevent abandonment and data loss?
    Decision: autosave, sample prefill, export format, print fidelity.

R4. How should a small, grounded chatbot behave when it does not know?
    Decision: intent scoring, clarifying questions, escalation to the audit.

R5. What accessibility and disclosure patterns apply to chat widgets and
    self-assessment tools?
    Decision: live regions, focus order, "how this score works" disclosure.

R6. What privacy claims are defensible for tools that process resumes and
    interview answers in the browser and over one request?
    Decision: wording of the privacy note, and what must never be stored.

## 5. Implementation candidates (to be ranked in the strategy)

Interview: score breakdown by dimension; one follow-up probe per question;
target length guidance; model answer that reuses the learner's own facts.

ATS: synonym and stem groups; phrase rescue for unique critical terms;
job-title alignment check; keyword-stuffing warning; copyable fix list.

Resume builder: local autosave with restore/clear; example prefill; live ATS
readiness checklist computed in the browser; plain-text and print export.

Profile optimizer: optional target-role field; before/after framing; per-block
copy buttons; stronger role extraction.

Prompt library: category counts, clear-all search, copy-all for a category,
keyboard-friendly controls.

Chatbot: scored intent with tie-breaking, history-aware follow-ups, suggested
next questions returned by the API, safe link rendering for internal routes,
conversation reset, honest "not in verified data" escalation.

Cross-cutting: documentation refresh, tests for parity between the browser
knowledge base and the server fallbacks, accessibility pass.

## 6. Explicit non-goals

- No streaming responses, no websockets, no new database tables.
- No user accounts, no server-side storage of resumes or interview answers.
- No new model vendor, no change to the Alibaba Cloud client contract.
- No redesign of the page, no new fonts, no new icon set.
- No claim of "ATS-proof", "guaranteed interview", or numeric hiring odds.

## 7. Risks and mitigations

| Risk | Mitigation |
| --- | --- |
| Synonym matching inflates scores | Cap synonym credit below exact-match credit; tests pin bounds. |
| More chat intents mis-route short messages | Score intents and require a minimum margin, else clarify. |
| Autosave stores sensitive text on shared devices | Store only in `localStorage` under a clear key, add a visible Clear button, document it. |
| Copy drift between JS and PHP answers | Add a feature test that asserts shared facts appear in both. |
| Scope creep into visual redesign | Touch CSS only for new components the features need. |

## 8. Deliverables

- `docs/STUDENT_HUB_CHAT_UPGRADE_PLAN.md` (this file)
- `docs/STUDENT_HUB_CHAT_RESEARCH.md`
- `docs/STUDENT_HUB_CHAT_STRATEGY.md`
- Verified backup archive and checksums outside the repo
- One commit per implemented change on a dedicated branch
- Updated `CONTENT_EDITING_GUIDE.md` and README section where behaviour changed
