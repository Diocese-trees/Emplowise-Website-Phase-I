# Emplowise HR Services

> **Clarity built on structure.**  
> Remote HR solutions for growing businesses in Pakistan, paired with a free career toolkit for emerging talent.

---

## Overview

**Emplowise** is a remote HR outsourcing and managed HR service built for startups and SMEs in Pakistan. Instead of leaving founders to act as the internal HR helpdesk—or forcing small teams into expensive enterprise consultancies—Emplowise installs and runs the structure behind people operations.

All engagements follow a four-stage methodology: **Assess → Identify → Improve → Review**.

---

## Core Operating Pillars

Emplowise organizes 14 confirmed HR services across three core pillars:

1. **Payroll & HR Operations**
   - Payroll management, leave and attendance tracking, employee onboarding, and centralized HR documentation/records.
2. **Performance Management**
   - Performance support, employee relations and conflict resolution, organizational assessments, and monthly HR reporting.
3. **Policy Making & Compliance**
   - HR policies and SOPs, legal/compliance coordination, ongoing HR consultation, and complete recruitment.

*(Note: An AI automation layer across these pillars is currently proposed and not sold as standalone software.)*

---

## Pricing & Packages (2026)

Emplowise operates on a monthly retainer model priced by employee headcount. Every engagement begins with a **Free HR Audit** to map operational gaps before scoping exact deliverables.

| Package | Team Size | Monthly Retainer | Best For |
| :--- | :--- | :--- | :--- |
| **Starter** | Up to 20 employees | **PKR 40,000** / mo | Startups needing foundational HR structure without a dedicated HR hire. |
| **Business** *(Core)* | Up to 50 employees | **PKR 75,000** / mo | Growing SMEs running regular payroll and performance cycles. |
| **Enterprise** | 50+ employees | **Custom** (Quoted post-audit) | Larger organizations with layered management and specialist needs. |

---

## Free Student Hub

Alongside client HR services, Emplowise offers five free, no-account-required career tools for students and early-career professionals:

- **Mock Interview Simulator:** Role- and level-specific questions scored on structure, evidence, and clarity.
- **ATS Resume & JD Analyzer:** Compares resume text against job descriptions to highlight keyword and formatting gaps.
- **ATS-Safe Resume Builder:** Generates clean, single-column resumes exportable to PDF or `.txt`.
- **Profile Optimizer:** Rewrites headlines, summaries, and experience bullets using action-oriented phrasing.
- **Career Prompt Library:** 14 copy-paste prompts covering job search, resumes, interviews, LinkedIn, and career clarity.

---

## Technical Architecture

The Emplowise web platform is built with simplicity, security, and zero build-step overhead:

- **Backend:** Laravel 13.43.0 · PHP 8.3.33
- **Frontend:** Blade templates · Modular Vanilla CSS · Vanilla JavaScript (no bundler required)
- **Database:** SQLite (stores `audit_requests` and `chat_messages`; student tool inputs are processed ephemerally and never stored)
- **AI Integration:** Alibaba Cloud Model Studio (`qwen-max` primary, `kimi` fallback) backed by a deterministic local rules engine for offline/fallback reliability.

### Key Routes & Endpoints

- **Public Pages:** `/` (Home), `/what-we-are`, `/what-we-do`, `/pricing`, `/student-hub`, `/faq`, `/free-hr-audit`
- **API Endpoints (CSRF-protected & rate-limited):**
  - `POST /api/audit` — Four-step Free HR Audit intake (`15/min`)
  - `POST /api/chat` — Grounded AI HR assistant (`30/min`)
  - `POST /api/student/*` — Interview, ATS, and profile optimization endpoints (`20/min`)

---

## Deployment

The repository includes ready-to-use deployment configurations for multiple environments:

- **Docker:** Multi-stage `Dockerfile` (Apache + PHP 8.3) and `docker-compose.yml`
- **Managed PaaS:** Ready for Render, Railway, Fly.io, DigitalOcean App Platform, and Google Cloud Run
- **VPS / Shared Hosting:** Includes sample Nginx configurations (`deploy/nginx.conf.example`) and cPanel setup instructions in `HOSTING.md`.

---

## Grounding & Transparency Note

All published facts, pricing, and service scopes are strictly sourced from `config/emplowise.php`. Unresolved commercial details (such as tier-specific deliverable limits, recruitment fee structures, and contract notice terms) are explicitly marked as **Pending Input** on the platform and finalized during the Free HR Audit consultation.