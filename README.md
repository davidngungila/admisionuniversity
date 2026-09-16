# University Online Admission System (OAS)

A configuration-driven **Tanzanian University Online Admission System** modelled on the University of Dodoma (UDOM) workflow. The entire admission calendar, fees, programmes, workflow steps, and selection are managed through the admin dashboard — no developer intervention is needed to open a new round, change a deadline, or add a programme.

> **Stack:** Laravel 13 · PHP 8.4 · MySQL 8 · Tailwind CSS (CDN) · Blade · `Africa/Dar_es_Salaam` timezone

---

## 1. At a Glance

| Area       | What it does |
|------------|--------------|
| **Public** | Home, Admission Calendar (OPEN/CLOSING SOON/CLOSED/UPCOMING auto-calculated), Programme Catalogue with filters, Requirements, Fees, Guidelines, Verify Admission, Contact |
| **Applicant** | UDOM-style dashboard with progress (`completed/total × 100`), dynamic multi-step application (step definition in DB, not hard-coded), status engine, programmes-applied history by round, result & admission letter, profile |
| **Admin**   | Academic years · Rounds · Windows · Workflow steps · Campuses/Faculties/Departments/Programmes/Requirements · Applicants/Applications · Document verification · Payments · Selection batches & ranking · Users/Roles · Settings · Reports · Audit logs |

### Design Principle — Configuration-Driven

> *The university should never need a developer to change “Bachelor Round 2 closes 21-09-2026” or “MSc Round 10 costs TZS 50,000”.*

All of the following are database-backed and editable from the admin UI:

- Academic years and which one is *active* (`AcademicYear::current()`).
- Rounds per year (`ApplicationRound`), including “allow multiple applications” flag.
- **Admission Windows** — one row per `(year, level, round, applicant category)` with `opens_at`, `closes_at`, `timezone`, `application_fee`, `currency`. Public & applicant portals derive `OPEN / CLOSING SOON (≤72h) / CLOSED / UPCOMING` from these dates.
- **Workflow Steps** (`application_workflow_steps`) — defines the applicant’s sidebar & form sequence *per admission level*.

```
Bachelor (5):  Personal Info → Payment → Academic Results → Programme Application → Submit
PGD      (6):  … + Documents → Submit
Masters  (7):  … + Academic Qualifications → Programme → Research Info → Documents → Submit
PhD      (8):  … + Academic Background → Programme → Research Proposal → Supervisor → Documents → Submit
```

Changing or reordering steps for any level is a single admin edit.

---

## 2. Requirements

- PHP ≥ 8.3 (tested on 8.4)
- Composer 2.x
- MySQL 8.x (or MariaDB 10.6+)
- Node 18+ *optional* — only if you want to rebuild Tailwind via Vite (CDN is the default)

---

## 3. Installation

```sh
# 1. Clone & install PHP deps
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure DB in .env (MySQL)
# DB_CONNECTION=mysql
# DB_DATABASE=admisionsystemnew
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Timezone — Africa/Dar_es_Salaam is the default
# APP_TIMEZONE=Africa/Dar_es_Salaam   # or leave unset (defaults to Africa/Dar_es_Salaam in config/app.php:68)

# 5. Migrate & seed
php artisan migrate:fresh --seed

# 6. Storage link for document uploads
php artisan storage:link

# 7. Serve (any one)
php artisan serve --port=8000
# or: composer run dev   (Vite + queue + logs via pail)
```

Open `http://localhost:8000`.

---

## 4. Demo Accounts (seeded)

| Role        | Email                        | Password   | Note |
|-------------|------------------------------|------------|------|
| Super Admin | `admin@university.ac.tz`     | `password` | Full access |
| Staff       | `staff@university.ac.tz`     | `password` | Admissions officer |
| Applicant   | `tausila@example.com`        | `password` | Demo applicant — *Tausila Macarius Mlula* (DOB 2006-10-25, +255779643305) with one submitted BSc application |

> Change passwords immediately in production via *Admin → Users*.

---

## 5. Key Workflows

### 5.1 Applicant — End to End

```
Register (name, email, phone, password)
  → Login
  → Dashboard shows Academic Year + OPEN windows
  → “Start Application” for a window (blocked if window CLOSED or duplicate not allowed)
  → Personal Information  (names, DOB, gender, citizenship, NIDA, disability, address: region/district/ward)
  → Payment             (fee from window; control number generated; FREE windows auto-confirm)
  → Academic Results    (dynamic subject/grade rows; multiple exam sittings; e.g. O-Level + A-Level)
  → Programme Application (search + select 1–3 programmes matching the window’s level)
  → Documents           (upload PDF/JPG/PNG ≤2MB per file; step only appears for PGD/MSc/PhD)
  → Submit              (application_number = UNI-YYYY-NNNNNN, status → SUBMITTED, progress 100%)
  → Status / Summary / Programmes Applied (history grouped by year|round)
  → Admission Result & Letter (once selection is run)
```

Progress is `completed_steps / total_steps × 100` and is recalculated on each step completion (`ApplicationStepCompletion`).

A step is reachable only when all previous steps are completed (`ApplicationController::ensureStepReachable`).

### 5.2 Admin — Calendar & Windows

```
Academic Years → Rounds → Admission Windows

Create window:
  Academic Year [2026/2027]
  Admission Level [Bachelor ▼]
  Round [2]
  Applicant Category [Tanzanian ▼]  (Tanzanian/ Foreign — fee can be 0 = FREE)
  Opening Date [08/09/2026]  Opening Time [00:00]
  Closing Date [21/09/2026]  Closing Time [23:59]
  Timezone Africa/Dar_es_Salaam (default)
  Application Fee [10000]  Currency [TZS]  Status [Active]
```

Public `Admission Calendar` and applicant `Apply Now` buttons read the same table.

### 5.3 Admin — Programmes

Each programme holds `code, name, level, department → faculty, campus, duration, study mode, tuition, capacity, status` plus `programme_requirements` (subject, minimum grade, principal passes or free-text).

### 5.4 Status Engine

```
DRAFT → PAYMENT_PENDING → PAYMENT_CONFIRMED → APPLICATION_IN_PROGRESS
  → SUBMITTED → UNDER_REVIEW → ELIGIBILITY_CHECKED → ELIGIBLE
  → SELECTED → ADMITTED

Side branches: PAYMENT_FAILED, DOCUMENT_CORRECTION_REQUIRED,
               INELIGIBLE, REJECTED, WAITLISTED, WITHDRAWN
```

`Application::advanceStatus()` writes to `application_status_histories`.

### 5.5 Selection

`Admin → Selection → Create Batch (year + round [+ level]) → Run Selection`

The seed implementation ranks `SUBMITTED` applications by average O/A-Level grade and assigns each applicant’s **first-choice programme** as `SELECTED` (`SelectionResult`). Statuses on individual results can then be changed to `WAITLISTED / REJECTED` manually.

---

## 6. Database — 34 Tables (31 custom + 3 Laravel)

```
Geography:            countries, regions, districts, wards
Academic structure:   admission_levels, campuses, faculties, departments,
                      programmes, programme_requirements
Admission calendar:   academic_years, application_rounds, admission_windows,
                      application_workflow_steps
Applicants:           applicants, applicant_addresses
Applications:         applications, application_step_completions,
                      application_programmes, application_documents,
                      application_status_histories
Finance:              payments
Academic records:     academic_results
Selection:            selection_batches, selection_results, admission_letters
System:               roles, permissions, permission_role, role_user,
                      notifications_logs, settings, audit_logs,
                      users (+ role/phone/is_active/avatar), cache, jobs, sessions
```

Key relations:

```
Applicant —1:N— Application —1:N— ApplicationProgramme —N:1— Programme
          —1:N— ApplicantAddress
          —1:1— User

Application —N:1— AdmissionWindow —N:1— ApplicationRound —N:1— AcademicYear
                              —N:1— AdmissionLevel —1:N— ApplicationWorkflowStep

AdmissionWindow.isOpen() / statusLabel()   // OPEN | CLOSING SOON | CLOSED | UPCOMING
```

---

## 7. Routes

### Public (`/`)

| Method | URI | Name |
|--------|-----|------|
| GET | `/` | `home` |
| GET | `/admission-calendar` | `public.calendar` |
| GET | `/programmes` | `public.programmes` |
| GET | `/programmes/{programme}` | `public.programmes.show` |
| GET | `/requirements` | `public.requirements` |
| GET | `/fees` | `public.fees` |
| GET | `/guidelines` | `public.guidelines` |
| GET | `/contact` | `public.contact` |
| GET | `/verify-admission?application_number=…` | `public.verify` |

### Auth

`GET|POST /login`, `GET|POST /register`, `POST /logout`

### Applicant (`/applicant` — `auth`)

`dashboard`, `POST apply/{window}`, `applications/{application}` (show), `applications/{application}/step/{route}` (GET show / POST save), `applications/{application}/status`, `applications/{application}/summary`, `history`, `profile` (GET/PUT), `password` (PUT), `results`, `results/{application}`, `letters/{letter}/download`

### Admin (`/admin` — `auth` + `role:super_admin,admin,staff`)

`dashboard`, `academic-years` (resource + `POST {id}/activate`), `rounds`, `windows`, `workflow`, `campuses`, `faculties`, `departments`, `programmes` (+ `POST {id}/requirements`), `applicants`, `applications` (+ `PATCH {id}/status`), `payments` (+ `POST {id}/verify`), `documents` (+ `POST {id}/verify`), `selection/batches`, `reports/*`, `users`, `settings`, `audit-logs`

---

## 8. Configuration

- **Timezone** — `config/app.php:68` defaults to `Africa/Dar_es_Salaam` (env `APP_TIMEZONE`). `AdmissionWindow` stores `timezone` per window and compares `now()` (app timezone) against `opens_at`/`closes_at`.
- **Storage** — `FILESYSTEM_DISK=local`, uploads to `storage/app/public/applications/{id}/…` (requires `storage:link`).
- **Mail** — `MAIL_MAILER=log` by default; notification bodies are also written to `notifications_logs`.
- **Queue / Cache / Session** — `database` driver by default (tables `jobs`/`cache`/`sessions` present).

---

## 9. Seeded Reference Data

- **Levels:** Certificate, Diploma, Bachelor, PGD, Masters, PhD
- **Years:** 2026/2027 (active), 2025/2026
- **Rounds (2026/27):** 1, 2 (current), 10 (current)
- **Windows (8)** matching the spec:

  | Level | Round | Category | Deadline | Fee |
  |-------|-------|----------|----------|-----|
  | Masters | 10 | TZ | 30 Sep 2026 | 50,000 |
  | PGD | 10 | TZ | 30 Sep 2026 | 50,000 |
  | PhD | 10 | TZ | 30 Sep 2026 | 50,000 |
  | Bachelor | 2 | TZ | 21 Sep 2026 | 10,000 |
  | Diploma | 2 | TZ | 21 Sep 2026 | 10,000 |
  | Bachelor | 2 | Foreign | 21 Sep 2026 | FREE |
  | Diploma | 2 | Foreign | 21 Sep 2026 | FREE |
  | Bachelor | 1 | TZ | 15 Aug 2026 | 10,000 (closed — history) |

- **13 programmes** (BSc Software Engineering DM005, BSc IT DM006, BSc IS DM007, BSc Biotechnology DM008, BSc Medical Lab DM009, …) with requirements and capacities.
- **36 workflow steps** (5 for BSc/Dip/Cert, 6 for PGD, 7 for Masters, 8 for PhD).
- **Roles/permissions** and **settings**.

---

## 10. Development Notes

- **Models** use Laravel 13 attribute style: `#[Fillable([...])]`, `#[Hidden([...])]`, `casts(): array`.
- **Middleware:** `EnsureRole` (`role` alias) and `EnsureActiveUser` (`active` alias) — registered in `bootstrap/app.php:14`.
- **Validation** is request-level in each controller; no FormRequest classes yet — easy to extract if needed.
- **File uploads** — `application_documents` (2 MB max, PDF/JPG/PNG). Verified flag + `verified_by`.
- **No frontend build required** — Tailwind is loaded from CDN. `public/build/manifest.json` is a stub; `npm run build` is optional.
- **Tests** — only the Laravel `ExampleTest` is present. Feature tests for the workflow are a good next step (`php artisan test`).

---

## 11. Next Steps / Enhancements

- NECTA / NACTVET result verification integration
- GePG / mobile-money gateway (currently control-number + manual verify)
- SMS & Email templates with a queue worker (`NotificationLog`)
- PDF admission letters (Dompdf / Snappy)
- Waiting-list promotion & capacity enforcement on selection
- Applicant photo & signature upload
- Multi-language (Swahili / English)

---

## 12. License

Proprietary — for the commissioning university. Laravel itself is MIT.
