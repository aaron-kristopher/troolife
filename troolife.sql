-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 01:55 AM
-- Server version: 8.0.41
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `troolife`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `activityID` int NOT NULL,
  `userID` int DEFAULT NULL,
  `deactivated_at` datetime DEFAULT NULL,
  `deactivated_by_admin` int DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `activated_by_admin` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int NOT NULL,
  `adminID` int DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `last_updated_profile` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `adminID`, `username`, `email`, `first_name`, `last_name`, `password`, `birthday`, `profile_picture`, `date_created`, `is_active`, `last_updated_profile`) VALUES
(3, NULL, 'matt', 'matnmas@gmail.com', 'Matthew', 'Mascunana', '$2y$10$KrjbvMFuuC1Of1S5v5eOKuIToqySLN.aNm4sbTNfTjiOF0Rp/1X12', '2003-08-10', 'uploads/profile_6814fd5e4e81c7.84136920.jpg', '2025-04-26 16:11:37', 1, '2025-04-26 16:11:37'),
(4, NULL, 'aaronlim', 'aaronlim@gmail.com', 'Aaron', 'Lim', '$2y$10$34j7MV5GerAOqhGsSbi8/O0TLDgzOVuGHgMJnMG20plkZbc1FZV5K', '2025-04-26', 'uploads/profile_680c9b38ed8362.06659117.jpg', '2025-04-26 16:37:13', 1, '2025-04-26 16:37:13'),
(5, NULL, 'mattnoble', 'test@gmail.com', 'Matt', 'Noble', '$2y$10$4wNNU7f10EoRjG8u/4VoZ.8IKBw86pXzverT0VlPcaeLz/avGnaSK', '2025-05-03', 'uploads/profile_6814fe51a37d86.93795435.jpg', '2025-05-03 01:17:29', 1, '2025-05-03 01:17:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`activityID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `deactivated_by_admin` (`deactivated_by_admin`),
  ADD KEY `activated_by_admin` (`activated_by_admin`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `adminID` (`adminID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `activityID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`),
  ADD CONSTRAINT `activity_log_ibfk_2` FOREIGN KEY (`deactivated_by_admin`) REFERENCES `admin` (`adminID`),
  ADD CONSTRAINT `activity_log_ibfk_3` FOREIGN KEY (`activated_by_admin`) REFERENCES `admin` (`adminID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
