#!/usr/bin/env python3
"""Emplowise UX gate.

A repeatable, headless pass that turns the UX score-lift work into checks a
machine can repeat: no horizontal overflow at any width, 44px touch targets in
the header and footer, the phone drawer keeping keyboard focus inside itself,
the thumb-reach dock behaving, axe finding no violations, and the consent card
waiting for intent instead of covering the first screen.

It is deliberately not wired into the required release pipeline. Run it by hand
or on a schedule so a flaky headless run can never block a deploy:

    php artisan emplowise:export-static /tmp/ux-export --base=http://127.0.0.1:8124
    php -S 127.0.0.1:8124 -t /tmp/ux-export &
    python3 tools/ux-gate/check.py --base=http://127.0.0.1:8124

Requires playwright (`pip install playwright` and `playwright install chromium`)
and axe-core (downloaded to a cache file on first run).

Exit code is 0 when every check passes, 1 on any failure, 2 when the
environment is missing.
"""

from __future__ import annotations

import argparse
import json
import sys
import urllib.request
from pathlib import Path

AXE_URL = "https://cdn.jsdelivr.net/npm/axe-core@4.10.2/axe.min.js"
AXE_CACHE = Path(__file__).resolve().parent / ".axe.min.js"

ROUTES = [
    "/", "/what-we-are/", "/what-we-do/", "/what-we-are-building/",
    "/why-emplowise/", "/emplostart/", "/faq/", "/contact/",
    "/free-hr-audit/", "/company-profile/", "/portfolio/", "/pitch-deck/",
    "/docs/", "/terms/", "/privacy/",
]

VIEWPORTS = [
    ("phone-360", 360, 780, True),
    ("phone-390", 390, 844, True),
    ("landscape", 844, 390, True),
    ("tablet-768", 768, 1024, True),
    ("desktop-1280", 1280, 800, False),
    ("wide-1440", 1440, 900, False),
]

results: list[tuple[str, bool, str]] = []


def check(name: str, ok: bool, detail: str = "") -> None:
    results.append((name, ok, detail))
    print(("PASS" if ok else "FAIL"), name, ("- " + detail) if detail else "")


def load_axe() -> str:
    if AXE_CACHE.exists():
        return AXE_CACHE.read_text()
    try:
        with urllib.request.urlopen(AXE_URL, timeout=30) as response:
            source = response.read().decode("utf-8")
    except Exception as error:  # network blocked
        raise SystemExit(f"error: could not fetch axe-core: {error}")
    AXE_CACHE.write_text(source)
    return source


def run(page, axe: str, route: str) -> None:
    label = route
    page.goto(base + route, wait_until="load")
    page.wait_for_timeout(350)

    overflow = page.evaluate(
        "() => document.documentElement.scrollWidth - document.documentElement.clientWidth"
    )
    check(f"no horizontal overflow {label}", overflow <= 1, f"overflow={overflow}px")

    page.evaluate(axe)
    violations = page.evaluate(
        "async () => { const r = await axe.run(document, {resultTypes:['violations']});"
        " return r.violations.map(v => ({id:v.id, impact:v.impact, n:v.nodes.length})); }"
    )
    check(f"axe clean {label}", not violations, json.dumps(violations)[:300])


def touch_targets(page, label: str) -> None:
    small = page.evaluate(
        """() => [...document.querySelectorAll(
             '#site-header a, #site-header button, .site-footer a, .site-footer button'
           )].filter(e => {
             const r = e.getBoundingClientRect();
             if (r.width === 0 && r.height === 0) return false;
             return r.height < 44 || r.width < 24;
           }).map(e => ({t:(e.textContent||'').trim().slice(0,20),
                         h:Math.round(e.getBoundingClientRect().height)}))"""
    )
    check(f"touch targets >=44px {label}", not small, json.dumps(small)[:300])


def interactions(page) -> None:
    page.goto(base + "/", wait_until="load")
    page.wait_for_timeout(300)

    check("consent waits for intent", not page.locator("#consent").is_visible())

    cta = page.locator(".hero-actions .btn").first.bounding_box()
    check(
        "hero CTA inside first screen",
        bool(cta) and cta["y"] + cta["height"] <= 844 and cta["height"] >= 44,
        f"bottom={0 if not cta else round(cta['y'] + cta['height'])}",
    )

    page.click("#nav-toggle")
    page.wait_for_timeout(320)
    inside = []
    page.eval_on_selector("#main-nav .nav-link", "el => el.focus()")
    for _ in range(3):
        page.keyboard.press("Tab")
        inside.append(page.evaluate("() => !!document.activeElement.closest('#main-nav')"))
    check("drawer holds keyboard focus", all(inside), f"perTab={inside}")
    page.keyboard.press("Escape")

    page.evaluate("window.scrollTo(0, Math.round(window.innerHeight * 0.8))")
    page.wait_for_timeout(500)
    dock = page.eval_on_selector("#dock", "el => el.classList.contains('is-visible')")
    inert = page.eval_on_selector("#dock", "el => el.hasAttribute('inert')")
    check("dock appears after hero", dock and not inert, f"visible={dock} inert={inert}")


def main() -> int:
    global base
    parser = argparse.ArgumentParser(description=__doc__.splitlines()[0])
    parser.add_argument("--base", default="http://127.0.0.1:8124")
    parser.add_argument("--shots", type=Path, default=None,
                        help="directory for full-page screenshots")
    args = parser.parse_args()
    base = args.base.rstrip("/")

    try:
        from playwright.sync_api import sync_playwright
    except Exception as error:
        print(f"error: playwright is not installed ({error})", file=sys.stderr)
        return 2

    axe = load_axe()
    if args.shots:
        args.shots.mkdir(parents=True, exist_ok=True)

    with sync_playwright() as runtime:
        try:
            browser = runtime.chromium.launch(
                executable_path="/usr/bin/chromium", args=["--no-sandbox"]
            )
        except Exception:
            browser = runtime.chromium.launch(args=["--no-sandbox"])

        # Overflow + axe across every viewport and route.
        for name, width, height, touch in VIEWPORTS:
            ctx = browser.new_context(
                viewport={"width": width, "height": height},
                is_mobile=touch, has_touch=touch,
            )
            page = ctx.new_page()
            for route in ROUTES:
                run(page, axe, route)
                if args.shots and name == "phone-390":
                    slug = "home" if route == "/" else route.strip("/").replace("/", "-")
                    page.screenshot(path=str(args.shots / f"{slug}.png"), full_page=True)
            ctx.close()

        # Header/footer touch targets on a phone.
        ctx = browser.new_context(viewport={"width": 390, "height": 844},
                                  is_mobile=True, has_touch=True)
        page = ctx.new_page()
        page.goto(base + "/", wait_until="load")
        page.wait_for_timeout(300)
        touch_targets(page, "home")
        ctx.close()

        # Home-specific behaviour.
        ctx = browser.new_context(viewport={"width": 390, "height": 844},
                                  is_mobile=True, has_touch=True)
        interactions(ctx.new_page())
        ctx.close()

        browser.close()

    failures = [r for r in results if not r[1]]
    print(f"\n{len(results)} checks, {len(failures)} failed")
    return 1 if failures else 0


if __name__ == "__main__":
    raise SystemExit(main())
