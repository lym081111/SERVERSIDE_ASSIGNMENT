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
  `dateReceived` date DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`achievementID`, `userID`, `title`, `category`, `dateReceived`, `description`) VALUES
(1, 1, 'CTF champion', 'IT', '2026-03-02', 'Yay! I did it!!!');

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
  `endDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`clubID`, `userID`, `clubName`, `role`, `roleDescription`, `startDate`, `endDate`) VALUES
(1, 1, 'Cybersecurity club', 'Member', 'I wish to work in cybersecurity field in the future\r\n', '2026-01-01', '2026-03-03');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `eventTitle` varchar(150) NOT NULL,
  `eventDate` date NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `userID`, `eventTitle`, `eventDate`, `location`, `description`, `created_at`) VALUES
(1, 1, 'Campus Leadership Talk', '2026-03-03', 'Heritage Hall', 'Provide insights to the juniors that wish to be a successful leader in their team.', '2026-03-03 09:28:03');

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
  `dateTo` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merits`
--

INSERT INTO `merits` (`meritID`, `userID`, `activityName`, `hours`, `dateFrom`, `dateTo`) VALUES
(1, 1, 'ABC', 2, '2026-03-02', '2026-03-02'),
(2, 1, 'DEF', 3, '2026-03-03', '2026-03-03'),
(3, 1, 'Volunteer Program', 2, '2026-03-01', '2026-03-03'),
(4, 3, 'Clean the beach', 2, '2026-03-03', '2026-03-03'),
(5, 3, 'Cleaning old folks home', 4, '2026-03-01', '2026-03-01'),
(6, 3, 'Blood donation campaign helper', 7, '0000-00-00', '0000-00-00'),
(7, 3, 'Become a MC for blood donation', 9, '2026-03-01', '2026-03-03'),
(8, 3, 'Donate old clothes to the orphanage', 1, '2026-02-11', '2026-02-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `name`, `email`, `passwordHash`, `isAdmin`, `created_at`) VALUES
(1, 'lym', 'lym@example.com', '$2y$10$2KTwpeLay31MHofNnrGPfuhMkYfYanfHk7V23Gm7FRUS0CeS8NkGC', 0, '2026-03-02 14:33:32'),
(2, 'Admin User', 'admin@gmail.com', '$2y$10$nA1L19w/XcyDQiM4QiEr3OOSDZ/Hb83ZU2wKz3DuQshgnij.KgALq', 1, '2026-03-03 09:36:32'),
(3, 'liew mei mei', 'meimei@example.com', '$2y$10$5zMkv2tRmVGTzX1F5DqdT.gbBqIcHOOcLFbw8/GlO7glEKBuTkPda', 0, '2026-03-03 14:44:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievementID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`clubID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `merits`
--
ALTER TABLE `merits`
  ADD PRIMARY KEY (`meritID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

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
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `merits`
--
ALTER TABLE `merits`
  ADD CONSTRAINT `merits_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
