# Student Co-Curricular Management System: Technical Documentation Source

## 1. System Summary
- Project type: PHP + MySQL web application (MVC-style structure).
- Goal: Track student co-curricular records with admin review workflow.
- Modules: Merit, Event, Club, Achievement, Certificate, Dashboard, Admin, Auth.
- Default route: `auth/login`.

## 2. Technology Stack
- Backend: PHP 8.x (compatible with XAMPP setup).
- Database: MySQL/MariaDB (`cocu_db`).
- Server runtime: Apache via XAMPP.
- Frontend: Server-rendered HTML + CSS (no SPA framework).

## 3. Project Structure
- Entry/router: `public/index.php`
- App config and CSRF helpers: `config/config.php`
- Controllers: `app/controllers/*.php`
- Models: `app/models/*.php`
- Views: `app/views/**`
- SQL dump: `database/cocu_db.sql`
- Incremental migrations: `database/migration_*.sql`

## 4. Architecture
- Presentation layer: views under `app/views`.
- Application layer: controllers under `app/controllers`.
- Data layer: models under `app/models`, connected through PDO.
- Routing style: query-string routing with `index.php?url=<controller>/<method>`.
- Autoloading: simple `spl_autoload_register` for controllers and models.

## 5. Authentication and Authorization
- Auth flow handled by `AuthController`.
- Password storage: `password_hash`, login check with `password_verify`.
- Session data after login:
  - `user_id`
  - `user_name`
  - `isAdmin`
  - `student_id`
- Session hardening:
  - `session_regenerate_id(true)` after login.
  - Secure cookie settings in `public/index.php` (`httponly`, `samesite=Lax`, conditional `secure`).
- Role model:
  - Student: own records and student dashboard.
  - Admin: global dashboard, cross-student CRUD, review actions.
- Password reset:
  - Simulated reset flow (session token), no email transport service.

## 6. Security Controls
- CSRF:
  - Token generated in `config/config.php`.
  - Included via `csrf_field()`.
  - Verified on POST by `verify_csrf()`.
- Input validation:
  - Controller-level validation for required fields and date logic.
  - Route segment validation regex in router.
- File uploads:
  - Managed by `EvidenceUpload::uploadFromRequest`.
  - Allowed MIME types: PDF, JPEG, PNG.
  - Max size: 5 MB.
  - Stored under `public/uploads/evidence`.
- DB access:
  - PDO prepared statements across models.
  - PDO exception mode enabled.

## 7. Routing Reference
- `achievement/index`
- `achievement/summary`
- `achievement/create`
- `achievement/edit`
- `achievement/exportSelf`
- `achievement/export`
- `achievement/delete`
- `achievement/review`
- `admin/index`
- `auth/login`
- `auth/register`
- `auth/forgot`
- `auth/reset`
- `auth/logout`
- `certificate/myMerit`
- `certificate/view`
- `certificate/verify`
- `club/index`
- `club/timeline`
- `club/create`
- `club/edit`
- `club/exportSelf`
- `club/export`
- `club/delete`
- `club/review`
- `dashboard/index`
- `dashboard/transcript`
- `event/index`
- `event/create`
- `event/edit`
- `event/exportSelf`
- `event/export`
- `event/delete`
- `event/review`
- `merit/index`
- `merit/create`
- `merit/edit`
- `merit/exportSelf`
- `merit/export`
- `merit/delete`
- `merit/review`

## 8. Module Behavior
### 8.1 Merit Module
- Core fields: `activityName`, `hours`, `dateFrom`, `dateTo`.
- Student submissions default to `pending`.
- Admin creation can be auto-`approved`.
- Review states: `pending`, `approved`, `rejected`.
- Rejected records support appeal and resubmission.
- Status changes are logged into `merit_status_logs`.
- Certificate auto-issuing:
  - Triggered on approved-hour milestones (every 100h).
  - Uses unique code format like `MC-YYYY-XXXXXXXX`.

### 8.2 Event Module
- Core fields: `eventTitle`, `eventType`, `eventDate`, `location`, `description`, `reflection`.
- Event type normalized to allowed set:
  - Leadership, Volunteerism, Academic, Technical, Sports, Community.
- Student edit/delete is blocked for approved records.

### 8.3 Club Module
- Core fields: `clubName`, `role`, `roleDescription`, `startDate`, `endDate`.
- `startDate` required, `endDate` optional.
- Includes student-facing timeline page (`club/timeline`).
- Overlap handling:
  - On admin approval/create/update, previous approved role period in same club is auto-closed by setting prior `endDate` to day before new `startDate`.

### 8.4 Achievement Module
- Core fields: `title`, `category`, `achievementLevel`, `dateReceived`, `description`.
- `achievementLevel` options: Faculty, University, National, International.
- Includes summary page (`achievement/summary`) with level counts and encouragement message.

### 8.5 Dashboard Module
- Student dashboard with KPI cards and module quick links.
- Includes official transcript page (`dashboard/transcript`):
  - Uses approved records only.
  - Aggregates records across all modules.
  - Generates transcript number format: `UTAR-COCU-<year>-<userID padded>`.

### 8.6 Certificate Module
- Student certificate list (`certificate/myMerit`).
- Certificate detail view (`certificate/view`), scoped by ownership for students.
- Public verification via certificate code (`certificate/verify`).

### 8.7 Admin Module
- Aggregated counts by module and status.
- Pending review queue across Merit/Event/Club/Achievement.
- Queue filtering by module and sort.
- Deep links from queue to per-record edit/review pages.

## 9. Database Design
## 9.1 Primary Tables
- `users`
- `merits`
- `events`
- `clubs`
- `achievements`
- `merit_certificates`
- `merit_status_logs`

## 9.2 Relationship Model
- One user to many merits/events/clubs/achievements.
- Reviewer fields (`reviewed_by`, `issued_by`, `changed_by`) link back to `users`.
- Certificates optionally reference source merit (`source_meritID`).
- Merit status logs reference `meritID`.

## 9.3 Key Constraints and Indexes
- Unique:
  - `users.email`
  - `users.student_id`
  - `merit_certificates.certificate_code`
  - `merit_certificates (userID, milestone_hours)`
- Foreign keys with `ON DELETE CASCADE` or `ON DELETE SET NULL` as appropriate.
- Status indexes exist on major module tables for faster review filtering.

## 10. Data Flow
- Student login -> session initialized.
- Student creates module record -> status `pending` (unless admin-created).
- Admin reviews and sets status -> state persisted with review metadata.
- Merit approval may trigger certificate issuance.
- Dashboard and transcript query module tables for summary/reporting.

## 11. Export and Reporting
- Student CSV export:
  - Merit/Event/Club/Achievement each has `exportSelf`.
- Admin CSV export:
  - Merit/Event/Club/Achievement each has `export`.
- Printable transcript:
  - `dashboard/transcript` view uses approved records only.

## 12. Migrations
- `migration_2026_04_09_review_and_student_id.sql`
  - Adds review workflow columns and unique `student_id`.
- `migration_2026_04_10_evidence_upload.sql`
  - Adds `evidence_path` to module tables.
- `migration_2026_04_10_merit_certificates.sql`
  - Adds `merit_certificates`.
- `migration_2026_04_10_merit_appeal_and_audit.sql`
  - Adds appeal fields and `merit_status_logs`.
- `migration_2026_04_10_module_uniqueness_features.sql`
  - Adds `events.eventType`, `events.reflection`, `achievements.achievementLevel`.

## 13. Deployment Steps
- Import `database/cocu_db.sql`.
- Apply migrations if database is from older baseline.
- Confirm `config/config.php` values:
  - `DB_HOST`
  - `DB_NAME`
  - `DB_USER`
  - `DB_PASS`
  - `BASE_URL`
- Start Apache and MySQL in XAMPP.
- Access `http://localhost/cocu_system/public/index.php`.

## 14. Known Limitations
- Password reset is simulated (no email delivery).
- No API layer; this is server-rendered MVC.
- No automated test suite found in repository.

## 15. Suggested Report Figures
- 3-tier architecture diagram (presentation/application/data).
- ERD for full schema.
- Sequence flow:
  - Student submission -> Admin review -> Approved record.
- Merit certificate issuance workflow.
- Club timeline overlap-closing logic workflow.
