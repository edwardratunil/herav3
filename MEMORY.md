# HERA v3 - Project Memory & Handoff Document
**Repository:** `edwardratunil/herav3`  
**Last Updated:** October 2026  
**System Classification:** Multi-Tenant Higher Education & K-12 HR, Faculty Load & Payroll CMS (Philippine Statutory & Institutional Alignment)

---

## 1. Project Background & Core Purpose
HERA v3 is a specialized multi-tenant Human Resources Information System (HRIS) and Payroll Management System tailored for Philippine educational institutions. It is architected to support:
- **Private Educational Institutions** (Basic Ed, K-12, Higher Ed, sectarian, and non-sectarian).
- **Public Educational Institutions** (DepEd Schools, LGU-funded community colleges/institutes, and State Universities and Colleges - SUCs).
- **Diverse Personnel Classifications**: Teaching faculty (tenured, probationary, part-time, overload, substitute), non-teaching academic staff, administrative personnel, contractual/job-order (JO/COS), and external consultants.

The system is designed with strict tenant isolation, immutable statutory calculation auditing, responsive accessibility across mobile/tablet/desktop, and zero-daemon cPanel deployment compatibility.

---

## 2. Key Architecture Decisions & Constraints
1. **Application Stack**:
   - Backend: Laravel 11.x (PHP 8.2 / 8.3)
   - Frontend: Vue 3 (Composition API, `<script setup>`), TypeScript, Tailwind CSS, Pinia, Vite
   - Database: MariaDB / MySQL 8.0+
   - Architecture Pattern: Modular Monolith with clean Hexagonal/Domain-Driven Service layer for calculation engines.
2. **Tenancy Strategy**:
   - Single-database multi-tenancy utilizing global tenant scoping (`tenant_id`), composite primary/foreign keys where appropriate, strict Route-Model Binding policies, and Row-Level security verification.
   - Separate tenant storage path: `storage/app/tenants/{tenant_uuid}/` completely segregated outside the public web root.
3. **cPanel Hosting Optimization**:
   - Sessions, Cache, and Queue drivers set to `database`.
   - Queue processing via single cPanel cron calling `php artisan schedule:run` every minute, orchestrating `queue:work --stop-when-empty --max-time=50 --max-jobs=25`.
   - Assets pre-compiled via Vite in CI/local workflow; production repo contains only built static assets in `public/build/`.
   - No daemon requirement (no Supervisord, Redis, or Docker needed on cPanel).
4. **Financial Arithmetic**:
   - Strict ban on native floating-point numbers (`float`/`double`) for monetary calculation.
   - Internal calculation engine uses `bcmath` or `brick/math` with 4-decimal precision, with Half-Up rounding to 2 decimal places at transaction/line-item boundaries.
5. **Payroll Immutability & Adjustments**:
   - Approved payroll runs are write-locked and cryptographically checksummed.
   - Corrections are strictly applied via Delta Adjustment Runs or retroactive adjustments in subsequent pay periods.

---

## 3. Statutory & Domain Rules Reference
| Domain Authority | Private Institution Coverage | Public Institution (DepEd / SUC / LGU) Coverage |
| :--- | :--- | :--- |
| **BIR (TRAIN Law RA 10963)** | Withholding Tax on Compensation (Rev. 2023 tables), ₱90,000 bonus threshold, De Minimis allowances. | Same TRAIN rates; special rules for PERA (₱2,000 non-taxable), statutory chalk allowances (RA 11997). |
| **Social Security (SSS / GSIS)** | SSS (RA 11199, 14.5% total contribution in 2025+, MPF/WISP cap ₱35,000 MSC). | GSIS (RA 8291, 9% employee, 12% employer based on actual basic salary without ceiling). |
| **PhilHealth (RA 11223)** | 5.0% premium, ₱10,000 floor to ₱100,000 ceiling, split 50/50. | 5.0% premium, ₱10,000 floor to ₱100,000 ceiling, split 50/50 (Government share). |
| **Pag-IBIG / HDMF (Circular 460)** | Mandatory 2% (₱200 EE / ₱200 ER up to ₱10,000 compensation floor = ₱400 total baseline). | Same baseline; optional or higher voluntary member contributions. |
| **DOLE vs CSC / DBM** | DOLE Labor Code: 13th month pay (PD 851), holiday pay (Art. 94), overtime, night diff, service incentive leave. | CSC Omnibus Rules on Leave, Proportional Vacation Pay (PVP), DBM SSL salary grades/steps, GAA ₱5,000 net take-home pay rule. |

---

## 4. Current Status & Next Actions
- [x] Workspace initialized and verified empty.
- [x] Master Memory and Architecture Plan formulated in `MEMORY.md` and `docs/ARCHITECTURE_AND_IMPLEMENTATION_PLAN.md`.
- [x] Laravel 11.x foundational scaffold established in root directory.
- [x] Multi-tenancy isolation implemented (`TenantContext`, `TenantScope`, `BelongsToTenant`).
- [x] Database migrations created and verified:
  - Tenancy & organization (`tenants`, `campuses`, `departments`, `academic_terms`)
  - Personnel & academic workload (`employees`, `employment_contracts`, `faculty_loads` with encrypted SPI)
  - Payroll engine (`payroll_runs`, `payroll_items`, `payroll_item_breakdowns`, `payroll_adjustments`)
- [x] Philippine Statutory Calculation Engines implemented with pure BCMath decimal arithmetic:
  - `BirWithholdingTaxCalculator` (TRAIN Law RA 10963 Revised 2023 tables)
  - `SssCalculator` (RA 11199, 14.5% total rate, regular MSC cap ₱20,000, WISP cap ₱35,000)
  - `PhilHealthCalculator` (RA 11223, 5% premium, ₱10k floor to ₱100k ceiling, 50/50 split)
  - `PagIbigCalculator` (Circular 460, ₱10,000 maximum base, ₱200/₱200 mandatory cap)
  - `GsisCalculator` (RA 8291, 9% EE / 12% ER actual salary for public institutions)
  - `FacultyLoadCalculator` (Lecture units, lab contact hours, multiplier, regular load cap, overload rate)
- [x] `PayrollCalculationService` with immutable cryptographic locking (`HMAC-SHA256`).
- [x] cPanel Zero-Daemon automation commands:
  - `cpanel:queue-run` with atomic execution locking and bounded duration.
  - `cpanel:health` verification command.
  - Scheduled in `routes/console.php` for standard single-cron execution.
- [x] Vue 3 + Tailwind CSS frontend implemented and compiled with Vite:
  - `PayrollGrid.vue` (Adaptive layout: collapsed cards on mobile `<640px`, sticky frozen column table on tablet/desktop)
  - `EmployeeSelfService.vue` (Mobile-first ESS with touch targets $\ge 44\text{px}$, Dean-approved teaching load, and encrypted payslip preview)
- [x] Phase 2 Implemented: Biometric Attendance & Leave Credit Engine:
  - `ZkTeCoAttendanceParser` for biometric logs (.dat, .csv, tab-delimited).
  - `AttendanceDtrEngine` with Philippine day factors (313 for 6-day, 261 for 5-day), grace period logic, tardiness & undertime minute rates, and absence deductions.
  - `LeaveCreditManager` with annual allocation, two-tier approval (Dept Head -> HR), remaining balance ledgers, and automatic DTR synchronization (`status = on_leave`).
  - Attendance deductions integrated into `PayrollCalculationService`, reducing taxable income and net pay with audited item breakdowns (`TARDINESS_UNDERTIME`, `ABSENCE_UNPAID`).
  - Mobile ESS enhanced with DTR log tab, live leave balance tracking, and leave filing.
- [x] Phase 3 Implemented: Bank Advice Disbursement & Pilot Reconciliation:
  - `BankExportEngine` generating official Philippine educational bank transmittals:
    - Landbank of the Philippines (LBP weAccess / Findes) with header, detail, trailer, and centavo hash totals.
    - BDO Unibank Corporate Payroll CSV format with account numbers and batch dates.
    - BPI BizLink and Metrobank Direct Banking payroll formats.
  - `PayrollReconciler` dual-run delta analysis tool:
    - Compares legacy historical registers against HERA v3 payroll output.
    - Accurately classifies `EXACT_MATCH`, `ROUNDING_VARIANT` ($|\Delta| \le ₱0.05$), and `MATERIAL_DISCREPANCY` ($|\Delta| > ₱0.05$).
    - Automated pilot verification report supporting Comptroller sign-off.
  - Frontend updated with interactive Bank format selector and Dual-Run Reconciliation modal in `PayrollGrid.vue`.
- [x] Superadmin & Authentication Ready:
  - Connected to local XAMPP MySQL database (`herav3`).
  - All migrations executed on MySQL (users, tenancy, personnel, payroll, attendance, leave).
  - Superadmin account created and seeded:
    - **Email**: `admin@hera.ph`
    - **Password**: `Password123!`
    - **Role**: `super-admin`
  - Responsive sign-in page available at `/login` with automated session handling and logout.
  - Server port configured to **8001** (`APP_URL=http://localhost:8001`).
- [x] Superadmin Reference UI & Role-Based Access Control (RBAC):
  - Created [`AdminSettingsView.vue`](file:///c:/xampp/htdocs/herav3/resources/js/components/AdminSettingsView.vue) matching user's reference mockup:
    - Left sidebar: Menu (Dashboard & Payroll, Institutions / Tenants, Personnel & Workload, Setting active), Help (User Guide, Support).
    - Top header: Search input, notifications, Super Administrator profile pill with quick Logout.
    - Setting Subtabs:
      1. `User & Permissions`: User Details table with avatar badges, active/inactive pills, formatted creation & login dates, interactive iOS-style access switches, and 3-dots action menu (`View Profile`, `Edit Details`, `Delete User`).
      2. `Roles & Role Access`: Matrix of all 8 system roles with granular permission toggles saved via `PUT /api/v1/admin/roles/{roleId}/permissions`.
      3. `Organization & Campuses`: List of registered educational institutions and campuses.
      4. `Statutory Compliance Matrix`: Overview of TRAIN law, SSS RA 11199, PhilHealth RA 11223, and Pag-IBIG Circular 460.
    - `+ Add User` Modal: Form to create new user accounts with instant role and institution tenancy assignment.
  - Backend API [`UserManagementController`](file:///c:/xampp/htdocs/herav3/app/Http/Controllers/UserManagementController.php):
    - `GET /api/v1/admin/users`: Search, filter, and fetch users, roles, and tenants.
    - `POST /api/v1/admin/users`: Account creation with validation and tenant scoping.
    - `PATCH /api/v1/admin/users/{id}/toggle-access`: Instant active/inactive access toggle.
    - `DELETE /api/v1/admin/users/{id}`: Secure account removal.
    - `PUT /api/v1/admin/roles/{roleId}/permissions`: Real-time role permissions update.
  - Dynamic Role-Based View & API Authorization:
    - User identity & role badge injected dynamically into frontend (`window.HERA_CURRENT_USER`).
    - Roles strictly gated:
      - `hr-officer`: Lands directly on **Personnel & Workload**; views staff directory as read-only; cannot modify system role permissions, toggle admin access, or view multi-tenant platform configurations.
      - `employee`: Restricted to **Employee Self-Service (ESS)** (DTR, leave filing, teaching workload, encrypted payslips).
      - `super-admin`: Retains full platform management, user administration, tenant setup, and role access matrix.
    - Controller endpoints in [`UserManagementController`](file:///c:/xampp/htdocs/herav3/app/Http/Controllers/UserManagementController.php) fortified with 403 checks: `users.manage`, `updateRolePermissions`, `toggleAccess`, and `destroy`.
  - Frontend restored strictly to reference design layout (`media_1793258626535_43b9de50.jpg`):
    - Clean top bar with wide search input ("Find percipient..."), notifications, and profile pill on the right.
    - Reference sidebar navigation: **Dashboard**, **Intake**, **Participant**, and **Setting** (active).
    - Setting screen with tabs: **Organization**, **User & Permissions**, **Roles & Role Access**, **Compliance**, and exact User Details table.
  - Automated test suite passing (25 tests, 149 assertions across Unit and Feature suites).
- [ ] Next: Ready for Pilot Deployment, Documentation Packaging, or Initial Git Commit.

