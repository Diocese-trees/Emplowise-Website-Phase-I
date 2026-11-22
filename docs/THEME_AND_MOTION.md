# Theme and motion

How the light/dark theme is chosen, how the stylesheets express it, and how long
the site's motion lasts. Read this before adding a component, a toggle, or a new
animation.

## 1. How the theme is chosen

`public/js/theme.js` is loaded **synchronously in `<head>`, before the
stylesheet**. The CSP is `script-src 'self'`, so it has to be an external file,
and deferring it would flash the light palette on every dark visit.

| Step | Behaviour |
| --- | --- |
| 1 | `localStorage['emplowise:theme']` if it is exactly `dark` or `light` |
| 2 | otherwise `prefers-color-scheme` |
| 3 | otherwise light |

Only dark sets an attribute: `<html data-theme="dark">`. The light theme is the
absence of the attribute, which keeps every existing selector working. The meta
`theme-color` is updated to `#081B3E` (light) or `#060d1a` (dark).

`theme.js` also:

- adds `html.js-theme` — every toggle is hidden unless this class is present, so
  a no-JS visitor never sees a button that cannot work;
- follows OS changes **only** while nothing is stored;
- exposes `window.Emplowise.theme.get()` and `.set('dark' | 'light')` (the setter
  persists, the same as clicking).

Storage failures (private mode) degrade to a per-page-view preference.

## 2. Toggle contract

Any element with `data-theme-toggle` is wired by event delegation — no per-button
setup, and the header, drawer and footer stay in sync automatically. The
required attributes:

```html
<button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false"
        data-label-dark="{{ $ui['theme_toggle_dark'] }}"
        data-label-light="{{ $ui['theme_toggle_light'] }}">
    <span class="theme-glyph icon-moon"><!-- moon icon --></span>
    <span class="theme-glyph icon-sun"><!-- sun icon --></span>
</button>
```

- `aria-pressed` is `true` in dark mode; the two `data-label-*` strings become
  the `aria-label` (dark mode announces "Switch to light theme").
- Optional: `data-text-dark` / `data-text-light` plus a `[data-theme-text]`
  child, used by the drawer row to add a visible label beside the icon.
- The glyph state machine is CSS only: `.theme-glyph` is hidden, `.icon-moon`
  shows in light, `.icon-sun` shows in dark.

Placement, by breakpoint (all gated on `html.js-theme`):

| Surface | Where it lives | Visibility |
| --- | --- | --- |
| Header actions | `.theme-toggle` (icon, 42px round) | only above 1040px |
| Nav drawer row | `.nav-theme` inside `nav#main-nav` | only at 1040px and below |

The footer toggle was removed when the closing band was simplified: dark mode
stays available in the header (desktop) and the drawer (everything narrower).
Cookie settings moved to the floating `.consent-fab` button.

The header control is desktop-only because it pushed the header into horizontal
overflow just above the drawer breakpoint (1041px) once the audit CTA sat next
to it; the drawer row takes over below that. If you add a fourth toggle, give it
the same data attributes and it will sync itself.

Copy keys live in `config/emplowise.php` → `copy.layout`:
`theme_toggle_dark`, `theme_toggle_light` (aria labels) and
`theme_footer_dark`, `theme_footer_light` (visible text).

## 3. How the CSS is organised

`public/css/theme.css` is the only file that contains theme-specific rules, and
it sits **after `components.css` and `pages.css`, before `chat.css` and
`inline.css`** in the `site.css` manifest. That position is deliberate: it must
win over component defaults without out-ranking the chat and inline layers.
Reordering the manifest changes cascade behaviour — treat it as part of the
theme contract.

Two mechanisms, used for different jobs:

**Re-point tokens that only ever describe light chrome** (on
`html[data-theme="dark"]`): `--line`, `--line-strong`, `--surface-2`,
`--surface-3`, `--ice`, `--offwhite`, `--sand`, `--glass`, the `--slate--`
ramp and the shadows.

**Do not re-point `--white` or `--navy`.** They are semantic: `--navy` is the
navy band and text-on-light, `--white` is text-on-navy. Re-pointing them would
invert the hero, `section--navy`, the CTA band and the footer, which already
work on dark. Sections therefore need explicit overrides:

```css
html[data-theme="dark"] .section:not(.section--navy),
html[data-theme="dark"] .page-head { background: transparent; }
html[data-theme="dark"] .section--alt { background: rgba(148, 197, 224, .035); }
```

**Override hard-coded light surfaces and text** with
`html[data-theme="dark"] .selector` rules. The semantic tokens written for that
layer are:

| Token | Value | Use |
| --- | --- | --- |
| `--d-page` | `#060d1a` | page background |
| `--d-surface` | `#0c1728` | cards |
| `--d-surface-2` | `#101d31` | insets, inputs |
| `--d-surface-3` | `#14243c` | raised chips |
| `--d-ink` | `#dbe6f4` | body text |
| `--d-ink-soft` | `#a7b9d1` | secondary text |
| `--d-ink-dim` | `#7f93ae` | meta text |
| `--d-line` | `rgba(148,197,224,.16)` | borders |
| `--d-line-strong` | `rgba(148,197,224,.3)` | borders, focus |
| `--d-accent` | `#5eead4` | links, kickers, numbers |

Measured against `--d-page`: body text 15.4:1, secondary 8.99:1, dim 5.72:1,
accent 13.1:1.

### Checklist for a new component

Before calling a component done, screenshot it in both themes and confirm:

1. surface — background is transparent, `--d-surface*`, or intentionally navy;
2. text — headings, body, list items, `dt`/`dd`, labels and placeholders have a
   dark rule (navy-on-near-black is the classic failure);
3. borders and dividers move to `--d-line`;
4. form fields — background, border, text, placeholder, focus ring and autofill;
5. buttons — `.btn--primary` on dark becomes `var(--grad-teal)` with white text,
   `.btn--ghost` / `.btn--outline` need their own dark border and text;
6. decorative images and inline SVG with baked fills (logo marks, icons);
7. shadows, which are stronger and darker on dark;
8. tables, code blocks, tooltips, empty states and the chat bubble.

Known traps already handled, keep them that way: `.price-amount`,
`.acc-trigger`, `.faq-toolbar`, `.pull-quote`, `.aside-card`, hub panels,
`.prompt-card`, `.repeat-item` inputs and the audit wizard's `.review-list`.

### Branding

The header carries two `<picture>` elements: `.brand-logo--light` (navy mark)
and `.brand-logo--dark` (reverse mark). CSS shows exactly one. The reverse
derivatives (`logo-reverse-125/250/375.webp`, `logo-reverse-375.png`) were
regenerated because the source PNG had a baked white haze (142,202 pixels at
alpha 1–10) that read as a grey rectangle on dark. When regenerating, zero the
near-transparent alpha before resizing:

```python
from PIL import Image
im = Image.open('public/img/logo-reverse.png').convert('RGBA')
a = im.getchannel('A').point(lambda v: 0 if v < 20 else v)
im.putalpha(a)
```

## 4. Motion

Tokens in `tokens.css`:

| Token | Value | Where |
| --- | --- | --- |
| `--dur-fast` | `0.22s` | hover, small state changes |
| `--dur` | `0.42s` | most transitions |
| `--dur-slow` | `0.95s` | scroll reveals |
| `--ease` | `cubic-bezier(.22,.61,.36,1)` | default |
| `--ease-out` | `cubic-bezier(.16,1,.3,1)` | entrances |
| `--ease-spring` | `cubic-bezier(.34,1.4,.5,1)` | small pops |

**Scroll reveals** — `.reveal` fades and lifts over `0.95s`; `data-delay="1..4"`
stagger by `0.12 / 0.24 / 0.36 / 0.48s`. The hidden state only applies under
`html.js-anim`, which `app.js` adds when an observer is available, so content is
never invisible without JS.

**Intro** — `public/js/intro.js`, once per browser session
(`sessionStorage['emplowise:intro']`), skipped on `saveData` or 2G. Append
`?intro=1` to any URL to replay it regardless of the session flag — useful for
demos and for re-checking the timing. Timeline:

| Moment | Normal | Reduced motion |
| --- | --- | --- |
| parts converge | 1.6s from 0.12s | delays zeroed |
| hold before exit | 1650ms | 240ms |
| exit (`is-leaving`) | 700ms | 110ms |
| worst-case safety net | 4200ms | 4200ms |

Total is ~2.5s from load. The CSS side lives in `intro.css` (overlay 0.7s, glow
1.3s at 0.5s, flash 0.78s at 0.98s, sub-line 0.9s at 1.18s). It resolves with a
`emplowise:intro-done` event, and `consent.js` has a 4400ms fallback so the page
can never stay covered.

**Hero rotator** — `home.js` rotates words every 3800ms.

**Reduced motion** — `prefers-reduced-motion: reduce` is treated as "no
movement", not "no feedback". In that mode:

- `base.css` sets `animation: none` on everything and restricts transitions to
  colour, background, border, outline, opacity, shadow, fill and stroke at
  0.18s. Transform, width and height are absent from that list, so they snap.
  Smooth scrolling is off.
- `app.js` does not add `html.js-anim`, so every `.reveal` is simply visible;
  `pages.css` stops the marquee; `home.js` renders counters and stack bars at
  their final values and disables the rotator and hero parallax;
- the intro still appears, but its parts do not travel and `intro.js` holds it
  for 240ms, exiting in 110ms.

If you add an animation, it is invisible under reduce — good. If a component
uses a transition to communicate state, use colour or opacity (which survive)
rather than a transform, and never rely on motion alone.

## 5. Validating a theme change

1. `php artisan test` — 56 tests, including static-export assertions.
2. `php artisan emplowise:seo-audit` — nine routes.
3. Screenshot all nine routes at 1440px in light and dark, with the intro and
   consent dismissed (see `docs/screenshots/*-dark.jpg`).
4. Sweep 320 / 768 / 1040 / 1041 / 1280 / 2560 for horizontal overflow in both
   themes.
5. Check the no-JS case: no visible toggle, no stuck intro, content readable.
