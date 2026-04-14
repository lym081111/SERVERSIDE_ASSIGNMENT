-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2026 at 05:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cocu_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `achievementID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `achievementLevel` varchar(30) DEFAULT 'Faculty',
  `dateReceived` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`achievementID`, `userID`, `title`, `category`, `dateReceived`, `description`, `status`) VALUES
(1, 1, 'CTF champion', 'IT', '2026-03-02', 'Yay! I did it!!!', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `clubID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `clubName` varchar(150) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `roleDescription` text DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`clubID`, `userID`, `clubName`, `role`, `roleDescription`, `startDate`, `endDate`, `status`) VALUES
(1, 1, 'Cybersecurity club', 'Member', 'I wish to work in cybersecurity field in the future\r\n', '2026-01-01', '2026-03-03', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `eventTitle` varchar(150) NOT NULL,
  `eventType` varchar(50) DEFAULT NULL,
  `eventDate` date NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `reflection` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `userID`, `eventTitle`, `eventDate`, `location`, `description`, `created_at`, `status`) VALUES
(1, 1, 'Campus Leadership Talk', '2026-03-03', 'Heritage Hall', 'Provide insights to the juniors that wish to be a successful leader in their team.', '2026-03-03 09:28:03', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `merits`
--

CREATE TABLE `merits` (
  `meritID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `activityName` varchar(150) NOT NULL,
  `hours` int(11) NOT NULL,
  `dateFrom` date DEFAULT NULL,
  `dateTo` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL,
  `appeal_note` text DEFAULT NULL,
  `appealed_at` datetime DEFAULT NULL,
  `resubmission_count` int(11) NOT NULL DEFAULT 0,
  `last_resubmitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merits`
--

INSERT INTO `merits` (`meritID`, `userID`, `activityName`, `hours`, `dateFrom`, `dateTo`, `status`) VALUES
(1, 1, 'ABC', 2, '2026-03-02', '2026-03-02', 'approved'),
(2, 1, 'DEF', 3, '2026-03-03', '2026-03-03', 'approved'),
(3, 1, 'Volunteer Program', 2, '2026-03-01', '2026-03-03', 'approved'),
(4, 3, 'Clean the beach', 2, '2026-03-03', '2026-03-03', 'approved'),
(5, 3, 'Cleaning old folks home', 4, '2026-03-01', '2026-03-01', 'approved'),
(6, 3, 'Blood donation campaign helper', 7, '0000-00-00', '0000-00-00', 'approved'),
(7, 3, 'Become a MC for blood donation', 9, '2026-03-01', '2026-03-03', 'approved'),
(8, 3, 'Donate old clothes to the orphanage', 1, '2026-02-11', '2026-02-11', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `merit_certificates`
--

CREATE TABLE `merit_certificates` (
  `certificateID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `milestone_hours` int(11) NOT NULL,
  `approved_hours_snapshot` int(11) NOT NULL,
  `certificate_code` varchar(40) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `source_meritID` int(11) DEFAULT NULL,
  `issued_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merit_status_logs`
--

CREATE TABLE `merit_status_logs` (
  `logID` int(11) NOT NULL,
  `meritID` int(11) NOT NULL,
  `from_status` enum('pending','approved','rejected') DEFAULT NULL,
  `to_status` enum('pending','approved','rejected') NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `change_note` text DEFAULT NULL,
  `change_source` varchar(40) NOT NULL DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `student_id`, `name`, `email`, `passwordHash`, `isAdmin`, `created_at`) VALUES
(1, '2205280', 'lym', 'lym@example.com', '$2y$10$2KTwpeLay31MHofNnrGPfuhMkYfYanfHk7V23Gm7FRUS0CeS8NkGC', 0, '2026-03-02 14:33:32'),
(2, '2205281', 'Admin User', 'admin@gmail.com', '$2y$10$nA1L19w/XcyDQiM4QiEr3OOSDZ/Hb83ZU2wKz3DuQshgnij.KgALq', 1, '2026-03-03 09:36:32'),
(3, '2205282', 'liew mei mei', 'meimei@example.com', '$2y$10$5zMkv2tRmVGTzX1F5DqdT.gbBqIcHOOcLFbw8/GlO7glEKBuTkPda', 0, '2026-03-03 14:44:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievementID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`clubID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `merits`
--
ALTER TABLE `merits`
  ADD PRIMARY KEY (`meritID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `merit_certificates`
--
ALTER TABLE `merit_certificates`
  ADD PRIMARY KEY (`certificateID`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD UNIQUE KEY `userID_milestone_hours` (`userID`,`milestone_hours`),
  ADD KEY `source_meritID` (`source_meritID`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Indexes for table `merit_status_logs`
--
ALTER TABLE `merit_status_logs`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `idx_merit_status_logs_merit` (`meritID`),
  ADD KEY `idx_merit_status_logs_changed_by` (`changed_by`),
  ADD KEY `idx_merit_status_logs_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `achievementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `clubID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `merits`
--
ALTER TABLE `merits`
  MODIFY `meritID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `merit_certificates`
--
ALTER TABLE `merit_certificates`
  MODIFY `certificateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `merit_status_logs`
--
ALTER TABLE `merit_status_logs`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `achievements_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `clubs_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Constraints for table `merits`
--
ALTER TABLE `merits`
  ADD CONSTRAINT `merits_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `merits_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Constraints for table `merit_certificates`
--
ALTER TABLE `merit_certificates`
  ADD CONSTRAINT `merit_certificates_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `merit_certificates_ibfk_2` FOREIGN KEY (`source_meritID`) REFERENCES `merits` (`meritID`) ON DELETE SET NULL,
  ADD CONSTRAINT `merit_certificates_ibfk_3` FOREIGN KEY (`issued_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Constraints for table `merit_status_logs`
--
ALTER TABLE `merit_status_logs`
  ADD CONSTRAINT `fk_merit_status_logs_merit` FOREIGN KEY (`meritID`) REFERENCES `merits` (`meritID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_merit_status_logs_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
