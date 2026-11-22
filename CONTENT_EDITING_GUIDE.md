# Content Editing Guide

Every public sentence on the Emplowise site lives in `config/emplowise.php`. You edit text there, not in the page templates. Templates only decide layout and styling.

## Where each page lives

| Page | URL | Config key |
|---|---|---|
| Meet Emplowise | `/` | `seo.home` and `copy.home` |
| Our Story | `/what-we-are` | `seo.what-we-are` and `copy.what-we-are` |
| What We Do | `/what-we-do` | `seo.what-we-do` and `copy.what-we-do` |
| What We Are Building | `/what-we-are-building` | `seo.what-we-are-building` and `copy.what-we-are-building` |
| Why Us | `/why-emplowise` | `seo.why-emplowise` and `copy.why-emplowise` |
| EmploStart | `/emplostart` | `seo.emplostart` and `copy.emplostart` |
| FAQ | `/faq` | `seo.faq`, `copy.faq`, and `faqs` |
| Contact Us | `/contact` | `seo.contact` and `copy.contact` |
| Free HR Audit | `/free-hr-audit` | `seo.free-hr-audit` and `copy.free-hr-audit` |
| Terms | `/terms` | `seo.terms` and `copy.legal.terms` |
| Privacy | `/privacy` | `seo.privacy` and `copy.legal.privacy` |

Shared header, footer, chat widget and theme-toggle text is in `copy.layout` and `copy.chat`. Package prices, the service list, the four method stages, and the FAQ answers are in the `packages`, `services`, `methodology`, and `faqs` arrays.

The keys follow the current page names, not the earlier ones: there is no `copy.pricing` or `copy.student-hub` any more, they are `copy.why-emplowise` and `copy.emplostart`. `seo.team` and `copy.team` feed the team section on Our Story; `/team` itself is a 301 to `/what-we-are#team`.

## Editing a search snippet

Each `seo` block controls one page:

- `primary_keyword`: the one phrase the page should rank for. Do not reuse it on another page.
- `secondary_keywords`: exactly two supporting phrases, different from every other page.
- `title`: 45 to 60 characters, keyword near the front, ending with `| Emplowise`.
- `description`: 135 to 155 characters, with a confirmed fact or price.
- `h1`: the main heading. Keep the primary keyword in it.
- `h1_accent`: the phrase inside the h1 shown in the italic accent style. It must appear word for word in `h1`.
- `h2`: a section heading that also contains the primary keyword.
- `intro`: the first paragraph. Keep the primary keyword in it.

Edit only the text inside the quotes. Keep the array keys unchanged.

## Editing body copy, buttons, and headings

Each `copy` block mirrors the page from top to bottom. For example, `copy.why-emplowise.compare.table` holds the five column labels in the comparison table, and `copy.free-hr-audit.success.steps` holds the three confirmation lines.

Field labels, helper hints, and tool result headings live in the same `copy` block as the rest of the page. Input placeholders stay in the Blade file next to the field, because they are example values tied to that one input. Short status messages that the JavaScript swaps in at runtime stay in `public/js`. Assistant answers are generated at request time from `app/Services/EmplowiseAssistant.php` and the local knowledge base in `public/js/chat.js`, so edit those two files to change how the chat replies.

### Header, footer and theme text

`copy.layout` holds the navigation labels, the header CTA, the footer columns and the theme toggle:

- `theme_toggle_dark` / `theme_toggle_light` are the screen-reader labels on the header and drawer buttons ("Switch to dark theme" / "Switch to light theme"). Keep them as instructions, not state names.
- `theme_footer_dark` / `theme_footer_light` are the visible drawer label, which names the mode the button will switch *to*.
- `footer_note` is the brand bio under the footer logo. It is also the page's main descriptive paragraph on `/contact`, so keep it specific: who you serve, the headcount range, the free audit, and the published starting price. Keep it to two sentences — the compact footer is deliberately short.
- `footer_tagline` is the line beside the copyright ("People analytics without bias, so CEOs can set new standards."). It replaces the old scope disclaimer; do not add a second legal paragraph unless counsel asks for one.
- `footer_legal_label`, `footer_terms`, and `footer_privacy` label the legal row at the bottom of the footer. Add a route and a link here if counsel asks for another document.
- `consent_footer` labels the "Cookie settings" button in the footer legal row. There is no floating cookie button any more; the banner opens by itself on the first visit without a saved choice and can be reopened from this button.

### Home shortcuts

`copy.home.shortcuts` is the four-card grid under the hero. Each item is
`label`, `desc`, `route`, optional `hash`, and `icon` (one of the names in
`resources/views/components/icon.blade.php`). The landing page intentionally
does not repeat the method, pricing, or EmploStart sections — they live on
`/why-emplowise` and `/emplostart`, and the cards are the navigation into them.
If you add a fifth card the grid wraps in the stylesheet; update the copy and
keep descriptions under roughly 60 characters.

### Legal pages

`copy.legal.terms` and `copy.legal.privacy` share one template
(`resources/views/pages/legal.blade.php`). Each block is an array of
`id`, `heading`, and `paragraphs`; the template numbers the sections and builds
the "On this page" list automatically. `updated` is the visible date. Keep the
tone client-facing and plain, and route anything that needs legal advice to
counsel rather than inventing a clause.

The theme toggle appears in the header above 1040px (inside the left rail from 1200px) and as a row in the mobile drawer at 1040px and below. The footer no longer carries a toggle, so editing the two keys updates both remaining controls.

## Student Hub and chat controls

The five Student Hub tools and the chat widget keep their visible labels in `copy.emplostart`:

- **Interview:** `interview.breakdown_heading` ("Why this score") and `interview.probe_heading` ("Ask yourself next") label the score dimensions and the practice prompt. `interview.score_note` names the four dimensions, so keep it in step with the 35/25/25/15 weights in `app/Services/StudentToolkit.php`.
- **Resume builder:** `resume.example`, `resume.clear`, and `resume.save_note`. The note must keep saying the draft stays in the browser and is never sent to a server.
- **Profile optimizer:** `profile.form.target` labels the optional Target role field. The rewrite uses that role when it is filled in.
- **Prompt library:** `prompts.search_placeholder`, `prompts.clear_search`, `prompts.copy`, and `prompts.copy_visible`. The counts on the filter buttons come from the `student.prompts` array, so they update automatically when you add, remove, or recategorise a prompt.
- **Chat:** `chat.reset` labels the "Start over" button and `chat.quick` holds the opening chips. Follow-up suggestion chips and intent scoring live in `app/Services/EmplowiseAssistant.php`; the browser fallback copy and its internal links live in `public/js/chat.js`. Keep both in step with the confirmed facts.

Interview and ATS scores are calculated by the local engines first. A configured model can refine the wording around a result, but the numbers a student sees always come from the deterministic local calculation, so a model change cannot move a score.

## Contact Us

The page deliberately has no form — the h1 promises "without a maze of forms" — so the only actions are the mailto link, the copy-email button and the route into the Free HR Audit. Keep it that way; if a contact form is ever required, the heading has to change with it.

`copy.contact` is laid out in the order the page reads:

| Block | Keys | Note |
| --- | --- | --- |
| Direct-line panel | `panel_kicker`, `panel_heading`, `panel_body`, `panel_email_cta`, `panel_audit_cta` | the navy panel at the top |
| Facts | `fact_hours_label`, `fact_hours`, `fact_reply_label`, `fact_reply`, `fact_market_label`, `fact_market` | office hours, reply window, team base — keep the hours in step with the FAQ |
| What happens next | `next_heading`, `next_body`, `next_steps` | three steps, each with `title` and `body`; the layout is built for three |
| Email card | `email_heading`, `email_body`, `email`, `email_write`, `copy_label`, `copy_done`, `copy_fail` | `email` is used for the visible address and the `mailto:`; change it in one place only |
| Socials card | `social_heading`, `social_body`, `social_note`, `social_pending`, `socials` | each entry is `label`, `icon`, `href`, `pending`; `pending` keeps the link inert and shows the `social_pending` note instead of navigating |
| Resources card | `resources_heading`, `resources_note`, `resources` | each entry is `label` and `route` (a Laravel route name); keep it to the pages that exist |
| AI shortcut | `ai_heading`, `ai_body`, `ai_query`, `ai_links` | each entry is `label` and a base `href`; the template appends the URL-encoded `ai_query`, so keep base URLs free of query strings. Rendered compact as the closing card. |

The page has no separate footer line: it ends on the shared `site-footer`, which carries the copyright and `copy.layout.footer_tagline`.

The social URLs are placeholders (`wa.me/923000000000`, the generic profile paths) until the real accounts exist; they are marked pending so the page does not present them as live.

## Editing prices and facts

Prices live in `packages`. Each entry has `price_pkr` (a number, or `null` for custom), `price_display`, `capacity`, and `period`. If a price changes, update both `price_pkr` and `price_display` so the visible price and the structured data stay aligned.

Do not invent client names, savings percentages, testimonials, or legal compliance guarantees. Details that depend on a client's records stay worded as confirmed in writing after the free HR audit.

## Edit, then check

Run these two commands from the project root after any edit:

```bash
php artisan emplowise:seo-audit
php artisan config:clear && php artisan config:cache
```

`emplowise:seo-audit` checks the nine marketing routes for missing keys, title and description length, duplicate titles, duplicate keywords, keyword placement in the title, h1, intro, and section heading, banned filler words, em dashes, and the confirmed business facts. Terms and privacy carry their own meta titles and descriptions and are covered by the route-render tests. It prints a table and exits non-zero when something needs fixing. The second command clears the old cached config and rebuilds it so the live site uses your new text.

To preview locally without caching, run `php artisan config:clear` and open the site with `php artisan serve`.

## Layout or styling changes

Text edits never need a rebuild. If you change a stylesheet in `public/css`, run
`php artisan emplowise:bundle-css` afterwards and commit the regenerated
`site.css`/`hub.css` in the same change set, or the site keeps serving the old
bundle. See "Styles and assets" in the README, and
[`docs/THEME_AND_MOTION.md`](docs/THEME_AND_MOTION.md) before touching colours or
adding a component that must work in dark mode.
