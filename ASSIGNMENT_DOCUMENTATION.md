# Student Co-curricular Management System

## 1. Overview
The Student Co-curricular Management System is a PHP + MySQL web application that allows students to record and manage co-curricular activities in one centralized system. It supports four modules: Event, Club, Merit, and Achievement. All records are linked to the authenticated student. An optional admin module provides visibility across all students.

## 2. Objectives
- Provide centralized CRUD for co-curricular records.
- Ensure records are tied to authenticated users.
- Offer an overview dashboard with module summaries.
- Provide admin oversight for academic staff or coordinators.

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
- `database/` — Reserved for SQL dump
- `Server_side_ERD.drawio` — Attribute-level ERD

## 6. User Roles
**Student**
- Register, login, and manage own records
- View dashboard summaries

**Admin**
- Access admin dashboard
- View all students and records
- Add/edit/delete records for any student

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
**Fields:** `eventTitle`, `eventDate`, `location`, `description`  
**Functions:** Add, view, edit, delete, search, sort

### Club Tracker
**Purpose:** Track club memberships and roles.  
**Fields:** `clubName`, `role`, `roleDescription`, `startDate`, `endDate`  
**Functions:** Add, view, edit, delete, search, sort

### Merit Tracker
**Purpose:** Track contribution hours.  
**Fields:** `activityName`, `hours`, `dateFrom`, `dateTo`  
**Functions:** Add, view, edit, delete, search, sort

### Achievement Tracker
**Purpose:** Record awards/certificates.  
**Fields:** `title`, `category`, `dateReceived`, `description`  
**Functions:** Add, view, edit, delete, search, sort

## 9. Dashboard
The dashboard summarizes each module using KPI cards and provides direct access to each tracker.

## 10. Admin Module
The admin dashboard includes:
- Total counts of students and records
- Student directory
- Global record lists with search and sort
- Full CRUD for each module on behalf of students

## 11. Database Schema (Attribute-Level ERD)
**ERD File:** `Server_side_ERD.drawio`

### users
- `userID` (PK, int, auto_increment)
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

### events
- `eventID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `eventTitle` (varchar(150))
- `eventDate` (date)
- `location` (varchar(150), NULL)
- `description` (text, NULL)
- `created_at` (timestamp)

### clubs
- `clubID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `clubName` (varchar(150))
- `role` (varchar(100), NULL)
- `roleDescription` (text, NULL)
- `startDate` (date, NULL)
- `endDate` (date, NULL)

### achievements
- `achievementID` (PK, int, auto_increment)
- `userID` (FK → users.userID)
- `title` (varchar(150))
- `category` (varchar(100), NULL)
- `dateReceived` (date, NULL)
- `description` (text, NULL)

### Relationships
- `users` 1 — ∞ `merits`
- `users` 1 — ∞ `events`
- `users` 1 — ∞ `clubs`
- `users` 1 — ∞ `achievements`

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
1. Import SQL database using phpMyAdmin.
2. Update `config/config.php` with DB credentials.
3. Launch from `public/index.php` via Apache.

## 15. Deliverables Checklist
- Report (IEEE format)
- Source code folder
- SQL dump in `/database`
- Zip package for submission

## 16. Future Enhancements
- Email-based password reset
- Role-based analytics
- Export to PDF/CSV
