-- Migration for existing cocu_db databases.
-- Purpose:
-- 1) add review workflow columns to module tables
-- 2) add unique student_id to users
--
-- Run this script once in phpMyAdmin SQL tab against database: cocu_db

START TRANSACTION;

-- 1) USERS: add student_id and backfill for existing accounts
ALTER TABLE users
  ADD COLUMN student_id VARCHAR(20) NULL AFTER userID;

UPDATE users
SET student_id = LPAD(userID + 2200000, 7, '0')
WHERE student_id IS NULL OR student_id = '';

ALTER TABLE users
  MODIFY student_id VARCHAR(20) NOT NULL;

ALTER TABLE users
  ADD UNIQUE KEY student_id (student_id);

-- 2) MERITS: review workflow columns
ALTER TABLE merits
  ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER dateTo,
  ADD COLUMN submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status,
  ADD COLUMN reviewed_at TIMESTAMP NULL DEFAULT NULL AFTER submitted_at,
  ADD COLUMN reviewed_by INT(11) NULL DEFAULT NULL AFTER reviewed_at,
  ADD COLUMN review_note TEXT NULL AFTER reviewed_by;

UPDATE merits SET status = 'approved' WHERE status = 'pending';

ALTER TABLE merits
  ADD KEY status (status),
  ADD KEY reviewed_by (reviewed_by);

ALTER TABLE merits
  ADD CONSTRAINT merits_ibfk_2
  FOREIGN KEY (reviewed_by) REFERENCES users(userID) ON DELETE SET NULL;

-- 3) EVENTS: review workflow columns
ALTER TABLE events
  ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER created_at,
  ADD COLUMN submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status,
  ADD COLUMN reviewed_at TIMESTAMP NULL DEFAULT NULL AFTER submitted_at,
  ADD COLUMN reviewed_by INT(11) NULL DEFAULT NULL AFTER reviewed_at,
  ADD COLUMN review_note TEXT NULL AFTER reviewed_by;

UPDATE events SET status = 'approved' WHERE status = 'pending';

ALTER TABLE events
  ADD KEY status (status),
  ADD KEY reviewed_by (reviewed_by);

ALTER TABLE events
  ADD CONSTRAINT events_ibfk_2
  FOREIGN KEY (reviewed_by) REFERENCES users(userID) ON DELETE SET NULL;

-- 4) CLUBS: review workflow columns
ALTER TABLE clubs
  ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER endDate,
  ADD COLUMN submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status,
  ADD COLUMN reviewed_at TIMESTAMP NULL DEFAULT NULL AFTER submitted_at,
  ADD COLUMN reviewed_by INT(11) NULL DEFAULT NULL AFTER reviewed_at,
  ADD COLUMN review_note TEXT NULL AFTER reviewed_by;

UPDATE clubs SET status = 'approved' WHERE status = 'pending';

ALTER TABLE clubs
  ADD KEY status (status),
  ADD KEY reviewed_by (reviewed_by);

ALTER TABLE clubs
  ADD CONSTRAINT clubs_ibfk_2
  FOREIGN KEY (reviewed_by) REFERENCES users(userID) ON DELETE SET NULL;

-- 5) ACHIEVEMENTS: review workflow columns
ALTER TABLE achievements
  ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER description,
  ADD COLUMN submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status,
  ADD COLUMN reviewed_at TIMESTAMP NULL DEFAULT NULL AFTER submitted_at,
  ADD COLUMN reviewed_by INT(11) NULL DEFAULT NULL AFTER reviewed_at,
  ADD COLUMN review_note TEXT NULL AFTER reviewed_by;

UPDATE achievements SET status = 'approved' WHERE status = 'pending';

ALTER TABLE achievements
  ADD KEY status (status),
  ADD KEY reviewed_by (reviewed_by);

ALTER TABLE achievements
  ADD CONSTRAINT achievements_ibfk_2
  FOREIGN KEY (reviewed_by) REFERENCES users(userID) ON DELETE SET NULL;

COMMIT;
