# Student Co-curricular Management System

## 1. Overview
The Student Co-curricular Management System is a PHP + MySQL web application that allows students to record and manage co-curricular activities in one centralized system. It supports four modules: Event, Club, Merit, and Achievement. All records are linked to the authenticated student. An optional admin module provides visibility across all students.

## 2. Objectives
- Provide centralized CRUD for co-curricular records.
- Ensure records are tied to authenticated users.
- Offer an overview dashboard with module summaries.
- Provide admin oversight for academic staff or coordinators.
- Add a review and approval workflow so records are verified before being finalized.

## 3. Technology Stack
- Backend: PHP (MVC-style)
- Database: MySQL
- Server: XAMPP / Apache / phpMyAdmin
- UI: HTML + CSS

## 4. System Architecture (3-Tier)
**Presentation Layer:** Views under `app/views/`  
**Application Layer:** Controllers under `app/controllers/`  
**Data Layer:** Models under `app/models/` + MySQL database

**System Relationship Diagram:**  
- `Server_side_System_Relationship.drawio` shows how Students/Admins interact with the router, controllers, views, models, and database.

## 5. File Structure (Key Paths)
- `public/index.php` — Entry point and router
- `config/config.php` — Configuration + CSRF helpers
- `app/controllers/` — Auth, Dashboard, Merit, Event, Club, Achievement, Admin
- `app/models/` — User, Merit, Event, Club, Achievement, Database
- `app/views/` — All UI views (auth, modules, admin, layout)
- `database/cocu_db.sql` — SQL dump for import (phpMyAdmin)
- `Server_side_ERD.drawio` — Attribute-level ERD

## 6. User Roles
**Student**
- Register, login, and manage own records
- View dashboard summaries
- Export own tracker data as CSV from each module; use browser print for a simple PDF-style report
- Submit records for admin review and see approval status and feedback
- Each student is assigned a unique student ID (e.g., 2205280)

**Admin**
- Access admin dashboard
- View all students and records
- Add/edit/delete records for any student
- Review and approve or reject student submissions with optional notes

## 7. Authentication & Security
- Centralized login and registration (shared across all modules)
- Session-based authentication
- CSRF protection for all POST forms
- Passwords stored using `password_hash` and verified by `password_verify`
- Login cookie (`last_login`) stored for reference
- Simulated password reset (no email sending)

### Simulated Password Reset Flow
1. Student opens Forgot Password page.
2. System verifies email exists.
3. A simulated reset link is generated on-screen.
4. Student resets password and logs in with new credentials.

## 8. Modules and Functional Requirements

### Event Tracker
**Purpose:** Record formal programs/events.  
**Fields:** `eventTitle`, `eventType`, `eventDate`, `location`, `description`, `reflection`  
**Functions:** Add, view, edit, delete, search, sort (search matches title, type, location, description, reflection, and date text), and status review (pending/approved/rejected).  
**Proof Upload:** Students can upload optional evidence files (PDF/JPG/PNG) when creating or editing pending/rejected records.

### Club Tracker
**Purpose:** Track club memberships and roles.  
**Fields:** `clubName`, `role`, `roleDescription`, `startDate`, `endDate`  
**Functions:** Add, view, edit, delete, search, sort (search matches club name, role, and role description), and status review (pending/approved/rejected)
**Date Logic:** `startDate` is required; `endDate` is optional for ongoing/new memberships.

### Merit Tracker
**Purpose:** Track contribution hours.  
**Fields:** `activityName`, `hours`, `dateFrom`, `dateTo`  
**Functions:** Add, view, edit, delete, search, sort, and status review (pending/approved/rejected).  
**Date Logic:** `dateFrom` is required; `dateTo` is optional for one-day activities (auto-mapped to `dateFrom` when empty).
**Approval Lock Rule:** Once a record is approved, students can no longer edit/delete it (admin only). Pending/rejected records can be edited and re-submitted.
**Appeal + Resubmission Flow:** Rejected records can be corrected and resubmitted with optional new proof and an appeal note. Resubmission moves status from rejected to pending for re-review.
**Audit Trail:** Every merit status transition is logged (from/to status, who changed it, when, source, note) for accountability.
**Auto Certification:** Merit certificates are auto-issued for each 100 approved-hour milestone (100h, 200h, ...), with a unique verification code.

### Achievement Tracker
**Purpose:** Record awards/certificates.  
**Fields:** `title`, `category`, `achievementLevel`, `dateReceived`, `description`  
**Functions:** Add, view, edit, delete, search, sort (search matches title, category, level, and description), and status review (pending/approved/rejected).  
**Date Logic:** `dateReceived` is required.
**Highlights Feature:** Achievements are organized by level (Faculty/University/National/International), with top highlights shown in the student view.

## 9. Dashboard
The dashboard summarizes each module using KPI cards, includes pending review counts, and provides direct access to each tracker. Students can print the page or any module list (browser Print / Save as PDF) using print-optimized styles.
It also provides an **Official Co-Curricular Transcript** view (dashboard action button) that formats approved records from Merit/Event/Club/Achievement in a university-style printable transcript layout.

## 10. Admin Module
The admin dashboard includes:
- Total counts of students and records (including system-wide merit hours and recent activity)
- Pending review totals across all modules
- Student directory
- Global record lists with search and sort
- Full CRUD for each module on behalf of students
- CSV export of filtered global lists (admin only; per module admin index)
- Review controls to approve, reject, or set submissions back to pending with a note

## 11. Database Schema (Attribute-Level ERD)
**ERD File:** `Server_side_ERD.drawio`

### users
- `userID` (PK, int, auto_increment)
- `student_id` (varchar(20), UNIQUE)
- `name` (varchar(100))
- `email` (varchar(100), UNIQUE)
- `passwordHash` (varchar(255))
- `isAdmin` (tinyint(1))
- `created_at` (timestamp)

### merits
- `meritID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `activityName` (varchar(150))
- `hours` (int)
- `dateFrom` (date, NULL)
- `dateTo` (date, NULL)
- `status` (enum: pending/approved/rejected, default pending)
- `submitted_at` (timestamp)
- `reviewed_at` (timestamp, NULL)
- `reviewed_by` (FK -> users.userID, NULL)
- `review_note` (text, NULL)
- `evidence_path` (varchar(255), NULL)
- `appeal_note` (text, NULL)
- `appealed_at` (datetime, NULL)
- `resubmission_count` (int, default 0)
- `last_resubmitted_at` (datetime, NULL)

### events
- `eventID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `eventTitle` (varchar(150))
- `eventType` (varchar(50), NULL)
- `eventDate` (date)
- `location` (varchar(150), NULL)
- `description` (text, NULL)
- `reflection` (text, NULL)
- `created_at` (timestamp)
- `status` (enum: pending/approved/rejected, default pending)
- `submitted_at` (timestamp)
- `reviewed_at` (timestamp, NULL)
- `reviewed_by` (FK -> users.userID, NULL)
- `review_note` (text, NULL)
- `evidence_path` (varchar(255), NULL)

### clubs
- `clubID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `clubName` (varchar(150))
- `role` (varchar(100), NULL)
- `roleDescription` (text, NULL)
- `startDate` (date, NULL)
- `endDate` (date, NULL)
- `status` (enum: pending/approved/rejected, default pending)
- `submitted_at` (timestamp)
- `reviewed_at` (timestamp, NULL)
- `reviewed_by` (FK -> users.userID, NULL)
- `review_note` (text, NULL)
- `evidence_path` (varchar(255), NULL)

### achievements
- `achievementID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `title` (varchar(150))
- `category` (varchar(100), NULL)
- `achievementLevel` (varchar(30), default Faculty)
- `dateReceived` (date, NULL)
- `description` (text, NULL)
- `status` (enum: pending/approved/rejected, default pending)
- `submitted_at` (timestamp)
- `reviewed_at` (timestamp, NULL)
- `reviewed_by` (FK -> users.userID, NULL)
- `review_note` (text, NULL)
- `evidence_path` (varchar(255), NULL)

### Relationships
- `users` 1 — ∞ `merits`
- `users` 1 — ∞ `events`
- `users` 1 — ∞ `clubs`
- `users` 1 — ∞ `achievements`

### merit_certificates
- `certificateID` (PK, int, auto_increment)
- `userID` (FK -> users.userID)
- `milestone_hours` (int)
- `approved_hours_snapshot` (int)
- `certificate_code` (varchar(40), UNIQUE)
- `issued_at` (timestamp)
- `source_meritID` (FK -> merits.meritID, NULL)
- `issued_by` (FK -> users.userID, NULL)

### merit_status_logs
- `logID` (PK, int, auto_increment)
- `meritID` (FK -> merits.meritID)
- `from_status` (enum pending/approved/rejected, NULL)
- `to_status` (enum pending/approved/rejected)
- `changed_by` (FK -> users.userID, NULL)
- `change_note` (text, NULL)
- `change_source` (varchar(40))
- `created_at` (timestamp)

## 12. Data Flow Summary
1. User logs in → session stores `userID` + `isAdmin`.
2. Controllers enforce session and route to views.
3. Models execute SQL using `userID` for student scoping.
4. Admin routes use joins to show global records.

## 13. Error Handling
- Login errors shown on the login view.
- Registration validation errors shown on register view.
- Database connection errors handled in `Database::connect`.

## 14. Deployment Notes
1. Import `database/cocu_db.sql` using phpMyAdmin (or MySQL client).
2. Update `config/config.php` with DB credentials.
3. Launch from `public/index.php` via Apache.
4. If you already have an older database, run `database/migration_2026_04_09_review_and_student_id.sql` once.
5. For proof-document support, run `database/migration_2026_04_10_evidence_upload.sql` once.
6. For merit certificate automation, run `database/migration_2026_04_10_merit_certificates.sql` once.
7. For module-specific Event/Achievement feature fields, run `database/migration_2026_04_10_module_uniqueness_features.sql` once.
8. For merit appeal + status audit trail, run `database/migration_2026_04_10_merit_appeal_and_audit.sql` once.

## 15. Deliverables Checklist
- Report (IEEE format)
- Source code folder
- SQL dump: `database/cocu_db.sql`
- Zip package for submission

## 16. Future Enhancements
- Email-based password reset (current flow is simulated on-screen only)
- Deeper role-based analytics and charts
- Optional: server-generated PDF reports (beyond browser print-to-PDF)

Additional enhancement: review workflow with pending/approved/rejected statuses and admin notes for each record.

**Implemented beyond the original baseline:** full-text search across module fields (see §8); admin CSV export; student “Export my CSV” on each tracker; print-friendly layout; admin dashboard aggregates (total merit hours, signups and new records in the last 30 days).
