# UX metrics and event taxonomy

This document defines what we measure on the Emplowise site, how each event is
named, and what "better" means. It exists so the UX work already shipped (the
phone drawer, the thumb-reach dock, the deferred consent card, 44px targets, the
accessibility pass) can be judged against numbers rather than opinion.

Two kinds of measurement are kept deliberately separate:

- **Product analytics** — what visitors do. Needs an analytics destination and a
  consent choice; nothing here loads before a visitor opts in.
- **The UX gate** — what the interface does. A headless browser pass, no
  visitors involved, safe to run any time. It is the regression net for the
  product metrics, not a substitute for them.

## Consent-first rule

The site sets no analytics or marketing cookie before a choice. That is why the
consent card now waits for a signal of intent (a scroll past the first screen or
any pointer or key input) instead of opening on a 700 ms timer. Any analytics
tool added later must follow the same rule: load it only after
`emplowise:consent` fires with `analytics: true`, or read the stored value with
`window.Emplowise.consent.get()`.

`window.Emplowise.consent.get()` returns
`{ essential, analytics, marketing, updated }` or `null`.

## Event taxonomy

All events are pushed to `window.dataLayer` (an array that exists even when no
tag manager is present) with a flat `event` name and a small `payload`. Names are
`snake_case`, objects are `noun_verb`. Keep payloads free of personal data: no
email, phone, name, or free-text answers.

| Event | Fires when | Payload | Primary question it answers |
| --- | --- | --- | --- |
| `cta_view` | A primary CTA enters the viewport for 1s | `cta`, `page`, `placement` (`hero`, `dock`, `footer`, `header`) | Is the call to action actually seen? |
| `cta_click` | Any tracked CTA is activated | `cta`, `page`, `placement` | Which placements earn clicks? |
| `nav_open` | The phone drawer opens | `page` | Do people use the menu or the dock? |
| `dock_view` / `dock_click` | The thumb-reach dock appears / is used | `page`, `action` | Does the dock lift mobile conversion? |
| `audit_start` | The wizard leaves step 1 with valid input | `page` | How many begin the audit? |
| `audit_step` | The wizard advances to step N | `step` | Where do people drop out? |
| `audit_complete` | The success screen renders | `has_reference` | End-to-end completion rate |
| `audit_error` | A field fails validation | `step`, `field`, `reason` | Which fields block people? |
| `chat_open` | The chat sheet opens | `page` | Is the assistant found? |
| `chat_reply` | The bot answers | `intent`, `fallback` (bool) | Is the knowledge base enough? |
| `consent_view` / `consent_choice` | The card appears / a choice is saved | `analytics`, `marketing`, `delay_bucket` | Do people engage or ignore it? |
| `scroll_depth` | 25 / 50 / 75 / 100% of a page is reached | `page`, `depth` | How far do people read? |

None of these events carries personal data, but they are still behavioural
analytics, so gate them on the analytics choice to stay consistent with the
stated policy. Essential storage stays limited to the consent record itself.

## Metric definitions

- **Audit completion rate** = `audit_complete` ÷ `audit_start`. Target: lift the
  current baseline by 10% relative. The wizard is the main conversion, so this is
  the headline number.
- **Mobile CTA click-through** = `cta_click` (mobile) ÷ `cta_view` (mobile) on
  the home hero and dock. The dock is new; compare its click-through with the
  header button it replaces on phones.
- **Drawer vs dock use** = `nav_open` ÷ (`dock_click` + `nav_open`). A healthy
  site uses the flat drawer shortcuts rather than fighting a disclosure.
- **Consent engagement** = `consent_choice` ÷ `consent_view`. A decision, either
  way, is success; a card that is dismissed without a choice is not.
- **Field friction** = `audit_error` grouped by `field`. The field with the most
  errors is the next UX fix.
- **Scroll reach** = share of sessions reaching 75% on `/why-emplowise/` and
  `/what-we-do/`. Pricing and services are where the decision is made.

## Baseline and targets

Scores from the 2026-11-27 end-user review, before this pass:

| Area | Before | After this pass |
| --- | --- | --- |
| Navigation | 7.5 | drawer shortcuts, focus trap, 44px targets |
| Conversion | 7.0 | hero trim, dock, deferred consent |
| Mobile layout | 8.0 | no overflow at 6 widths, thumb-reach dock |
| Trust | 8.5 | axe clean, honest consent timing |
| Finish / polish | 8.5 | sticky table column, docs chip affordance |

The numeric "after" is what the UX gate reports, not a guess: 185 checks across
15 routes and 6 viewports pass, plus 16 interaction checks. Product-analytics
targets are set relative to whatever baseline the first instrumented week
produces — do not invent absolute conversion percentages for a business this
size; the sample will be too small to trust them.

## The UX gate

`tools/ux-gate/check.py` (wrapped by `php artisan emplowise:ux-gate --base=…`)
is the machine-checkable half:

- horizontal overflow at 360, 390 (phone), 844×390 (landscape), 768 (tablet),
  1280 and 1440 (desktop),
- header and footer interactive targets at least 44px tall and 24px wide,
- the phone drawer keeping keyboard focus inside itself under Tab,
- the thumb-reach dock appearing after the hero and never on the audit page,
- the consent card staying closed until intent, then opening on scroll,
- axe-core with zero violations on every route at every width.

It is advisory in CI (`.github/workflows/ux-gate.yml`, weekly and on demand). A
headless run must never block a deploy; read the failing checks and fix them in
a normal pull request.

## Instrumentation notes

- Prefer a first-party `dataLayer` push over a heavy third-party tag until there
  is a real analytics owner. The array costs nothing and can be replayed.
- Wire `cta_view` with `IntersectionObserver` at a 1s dwell, not on paint, so a
  passing glance is not counted as a view.
- Keep `delay_bucket` on `consent_choice` (`0-5s`, `5-30s`, `30s+`) to see whether
  the deferred card is found quickly once it appears.
- Never send free-text audit answers, names, emails, or the chat message body to
  analytics. Reference the stored audit by its public reference, not by content.
