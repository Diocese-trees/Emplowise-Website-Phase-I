# Content Editing Guide

Every public sentence on the Emplowise site lives in `config/emplowise.php`. You edit text there, not in the page templates. Templates only decide layout and styling.

## Where each page lives

| Page | URL | Config key |
|---|---|---|
| Home | `/` | `seo.home` and `copy.home` |
| What We Are | `/what-we-are` | `seo.what-we-are` and `copy.what-we-are` |
| What We Do | `/what-we-do` | `seo.what-we-do` and `copy.what-we-do` |
| Pricing | `/pricing` | `seo.pricing` and `copy.pricing` |
| Student Hub | `/student-hub` | `seo.student-hub` and `copy.student-hub` |
| FAQ | `/faq` | `seo.faq`, `copy.faq`, and `faqs` |
| Free HR Audit | `/free-hr-audit` | `seo.free-hr-audit` and `copy.free-hr-audit` |

Shared header, footer, and chat widget text is in `copy.layout` and `copy.chat`. Package prices, the service list, the four method stages, and the FAQ answers are in the `packages`, `services`, `methodology`, and `faqs` arrays.

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

Each `copy` block mirrors the page from top to bottom. For example, `copy.pricing.compare.table` holds the five column labels in the comparison table, and `copy.free-hr-audit.success.steps` holds the three confirmation lines.

Field labels, helper hints, and tool result headings live in the same `copy` block as the rest of the page. Input placeholders stay in the Blade file next to the field, because they are example values tied to that one input. Short status messages that the JavaScript swaps in at runtime stay in `public/js`. Assistant answers are generated at request time from `app/Services/EmplowiseAssistant.php` and the local knowledge base in `public/js/chat.js`, so edit those two files to change how the chat replies.

## Editing prices and facts

Prices live in `packages`. Each entry has `price_pkr` (a number, or `null` for custom), `price_display`, `capacity`, and `period`. If a price changes, update both `price_pkr` and `price_display` so the visible price and the structured data stay aligned.

Do not invent client names, savings percentages, testimonials, or legal compliance guarantees. Details that depend on a client's records stay worded as confirmed in writing after the free HR audit.

## Edit, then check

Run these two commands from the project root after any edit:

```bash
php artisan emplowise:seo-audit
php artisan config:clear && php artisan config:cache
```

`emplowise:seo-audit` checks every route for missing keys, title and description length, duplicate titles, duplicate keywords, keyword placement in the title, h1, intro, and section heading, banned filler words, em dashes, and the confirmed business facts. It prints a table and exits non-zero when something needs fixing. The second command clears the old cached config and rebuilds it so the live site uses your new text.

To preview locally without caching, run `php artisan config:clear` and open the site with `php artisan serve`.
