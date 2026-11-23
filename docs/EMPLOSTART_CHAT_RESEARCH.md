# Research: EmploStart and Chatbot Upgrade

> Historical record of the five-tool upgrade research. EmploStart now carries six modules and shipped as a standalone app in v3.0; the engine audit below describes the pre-pivot code, which no longer lives in this repository. See `AGENTS.md` for the current contract.

Phase 2 of 5. Companion to `EMPLOSTART_CHAT_UPGRADE_PLAN.md`.
Method: external sources (cited) combined with a line-by-line audit of the
current implementation at commit `4b231ff`. Where evidence is thin, the note
says "design judgement" instead of importing a statistic we cannot verify.

## 1. Code audit: what exists today

### 1.1 Server engines (`app/Services/StudentToolkit.php`)

- Interview questions: nine role families x three questions, with level
  overrides for Internship and Career switch. Deterministic and safe.
- Interview feedback: internally computes four dimensions — structure out of
  35, evidence out of 25, clarity out of 25, ownership out of 15 — then
  collapses them into one integer and throws the dimensions away. The model
  answer is a static template that never uses the learner's own words.
- ATS: unigram/bigram extraction with a stop list, exact surface matching,
  keyword score capped at 55, structure 25, format 20. Bigrams only survive at
  frequency 2+, so a phrase that appears once but is the job title is lost.
- Profile: rewrites weak verbs and removes filler adjectives, then generates
  three headlines from the detected role and skills.

### 1.2 Chat (`app/Services/EmplowiseAssistant.php` + `public/js/chat.js`)

- Intent detection is an ordered `match (true)` chain: the first regex hit
  wins. Short or mixed messages therefore pick the earliest intent, not the
  best one. Example: "how much is the free audit" matches `pricing` before
  `audit`.
- Local mode ignores conversation history, so a follow-up like "and for 20
  people?" falls to `general`.
- Replies are plain text with route paths like `/free-hr-audit` that are not
  tappable.
- The browser knowledge base duplicates all 18 answers in JavaScript, and so
  does the PHP fallback. Nothing tests that the two stay in sync.
- The chat log already uses `role="log"` and `aria-live="polite"`, which is
  the right baseline.

### 1.3 Client (`public/js/student.js`)

- Resume state lives only in the DOM. A refresh, tab close, or accidental
  navigation destroys everything typed.
- The ATS result renders matched/missing keywords and three bars, but the fix
  list cannot be copied as a block.
- The profile result exposes copy buttons on headlines only; summary and
  bullets must be selected by hand.
- The prompt library has search and category filters, but no counts, no
  clear-search control, and no bulk copy.

## 2. External evidence

### 2.1 How ATS matching actually works

Applicant tracking systems parse a resume into structured fields — contact,
work history, education, skills — then index it, and recruiters search that
index by keyword and job title rather than reading every file. That means
surface vocabulary still matters even where semantic ranking exists.
[100+ ATS Keywords List 2026: Applicant Tracking System Keywords](https://www.resumeadapter.com/blog/ats-keywords-list)

Formatting decides whether parsing succeeds at all: single column, read top to
bottom, and conventional section headings such as Summary, Work Experience,
Education, and Skills. Tables and multi-column layouts are the recurring cause
of scrambled extraction.
[ATS Resume Format: Best Practices for 2026 (Complete Guide)](https://www.atshiring.com/en/learn/ats-resume-format-best-practices-2025)

The practical lesson for a free tool is not "beat the ATS" but "show the
candidate the vocabulary gap and the structure gap separately." A single
number hides which of the two is costing them the interview.

Semantic screening exists and reduces pure keyword misses, which is exactly
why keyword-only scoring should not be presented as a hiring prediction.
[Semantic Search in Resume Screening: Why It Wins | Blog](https://www.brainner.ai/blog/article/the-benefits-of-semantic-search-over-keyword-matching-in-resume-screening)

### 2.2 What makes interview feedback actionable

STAR (Situation, Task, Action, Result) is the standard structure interviewers
are trained to look for, and rubrics commonly allocate roughly a fifth of the
weight to context and the rest to task, action, and result.
[Using the STAR method for your next behavioral interview (worksheet included)](https://capd.mit.edu/resources/the-star-method-for-behavioral-interviews/)

A good STAR answer runs about 60–90 seconds, spends minimal time on context,
and puts most of the words into the specific action taken.
[STAR Method Examples: 20 Behavioral Answers for 2026](https://ophyai.com/blog/interview-tips/star-method-examples-behavioral-interviews)

Feedback should point at the missing element, not just the total. "Your answer
has no result" is actionable; "62/100" is not. The same sources stress
truthfulness over polish, which matches this site's no-invented-experience
rule.

### 2.3 Autosave and data-loss prevention

Autosave exists to remove the fear of losing work; guidance is to save at
meaningful events (field blur, debounced input) and to keep a visible save
state so users trust the system. Removing the manual affordance entirely can
itself create anxiety.
[Autosave design pattern: examples and best practices](https://ui-patterns.com/patterns/autosave)

However, `localStorage` is readable by any script running on the origin and is
therefore a poor home for genuinely sensitive data.
[How to Use localStorage in JavaScript to Save and Retrieve Data](https://strapi.io/blog/how-to-use-localstorage-in-javascript)

Design consequence: a resume builder that stores a draft locally must say so
plainly, store only the draft the user typed, offer a one-click clear, and
never store interview answers or ATS pastes. Data minimisation is the
defensible position.
[How Data Minimization Protects Job Seeker Privacy](https://www.resumly.ai/blog/how-data-minimization-protects-job-seeker-privacy)

### 2.4 Chatbot fallback and recovery

Fallback is not an edge case; it is a core path. Real deployments found that
when confidence dips the bot either suppressed a good answer or looped on a
generic "I didn't understand", with no recovery options and no escalation.
The fix was a "fall-forward" pattern: say what the bot can do, surface the
closest matching options, and keep the tone human.
[Planning for failure: designing chatbot fallbacks](https://uxcontent.com/designing-chatbots-fallbacks/)

Offering two to four short suggested replies after a response reduces the
blank-input problem and keeps the conversation moving.
[UX for AI chatbots – UX Best Practices and Guidelines | SubUX](https://subux.pro/guides/article/ux-for-ai-chatbots)

Site-specific assistants should state their capabilities up front and suggest
relevant prompts rather than presenting an empty box.
[10 Guidelines for Designing Your Site's AI Chatbots](https://www.nngroup.com/articles/ai-chatbots-design-guidelines/)

Design consequence: replace first-match intent detection with scored intents,
return suggested follow-ups with each reply, and make the "I don't know" path
end at the Free HR Audit with a tappable link.

### 2.5 Accessibility for chat and self-assessment

`role="log"` is the correct role for a chat transcript, and adding a redundant
`aria-live="polite"` is recommended for maximum compatibility. Live regions
should be present in the initial markup, not injected later, and should carry
concise updates rather than the whole transcript.
[ARIA live regions - MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Guides/Live_regions)

Live chat widgets need keyboard reachability, deliberate focus management on
open and close, and announced new messages. Streaming text should not be
announced token by token.
[Making Live Chat Widgets Accessible: Keyboard, Focus, and ...](https://www.mironsoft.de/en/blog/barrierefreiheit-making-live-chat-widgets-accessible)

Design consequence: keep the existing live region, ensure the typing state is
not announced as content, return focus to the input after a reply, and label
every new control.

## 3. Synthesis

Three findings drive every decision in the strategy:

1. **Diagnosis beats a single score.** ATS and interview tools should expose
   the dimensions behind the number, because candidates can act on a
   dimension and cannot act on a mystery.
2. **Recovery is a feature.** Autosave for the builder and fall-forward for
   the chat are not polish; they are the moments where users currently leave.
3. **Grounding must be visible.** When the bot does not know, it should say so
   and route to the audit, and when scores are heuristic, the page should say
   how they are computed.

## 4. What the evidence does not support

- No claim that any specific percentage of resumes are rejected by ATS.
  Sources disagree and the number is not needed to justify the feature.
- No claim that this tool predicts hiring outcomes. It measures text overlap
  and structural completeness, which is a different thing.
- No claim that local mode is equivalent to the model. It is a deterministic
  fallback with the same verified facts and a narrower range.

## 5. Source list

1. ResumeAdapter, "100+ ATS Keywords List 2026" — parsing and indexing.
2. ATSHiring, "ATS Resume Format: Best Practices for 2026" — single column,
   standard headings.
3. Brainner, "Semantic Search in Resume Screening" — limits of keyword-only
   matching.
4. MIT CAPD, "Using the STAR method" — STAR structure and weighting.
5. OphyAI, "STAR Method Examples" — 60–90 second target, action-heavy.
6. UI Patterns, "Autosave" — save events, visible save state.
7. Strapi, "How to Use localStorage in JavaScript" — do not store sensitive
   data in localStorage.
8. Resumly, "How Data Minimization Protects Job Seeker Privacy".
9. UX Content Collective, "Planning for failure: designing chatbot fallbacks"
   — fall-forward, closest matches, escalation.
10. SubUX, "UX for AI chatbots" — 2–4 suggested replies.
11. Nielsen Norman Group, "10 Guidelines for Designing Your Site's AI
    Chatbots" — state capabilities, suggest prompts.
12. MDN, "ARIA live regions" — role=log plus aria-live=polite, initial markup.
13. Mironsoft, "Making Live Chat Widgets Accessible" — focus management.
