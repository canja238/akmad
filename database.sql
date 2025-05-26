-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 07:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_system`
--
CREATE DATABASE IF NOT EXISTS `library_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `library_system`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `is_checked_out` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `is_checked_out`) VALUES
(1, '1984', 'George Orwell', 0),
(2, 'To Kill a Mockingbird', 'Harper Lee', 1),
(3, 'The Great Gatsby', 'F. Scott Fitzgerald', 0);

-- --------------------------------------------------------

--
-- Table structure for table `checkouts`
--

CREATE TABLE IF NOT EXISTS `checkouts` (
  `checkout_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `checkout_date` datetime DEFAULT current_timestamp(),
  `return_date` datetime DEFAULT NULL,
  PRIMARY KEY (`checkout_id`),
  KEY `student_id` (`student_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkouts`
--

INSERT INTO `checkouts` (`checkout_id`, `student_id`, `book_id`, `checkout_date`, `return_date`) VALUES
(1, 1, 1, '2025-05-26 23:46:18', '2025-05-26 23:50:24'),
(2, 4, 1, '2025-05-27 00:10:54', '2025-05-27 01:52:46'),
(3, 5, 3, '2025-05-27 00:26:38', '2025-05-27 00:26:46'),
(4, 6, 3, '2025-05-27 00:31:46', '2025-05-27 00:31:58'),
(5, 6, 2, '2025-05-27 00:32:31', NULL),
(6, 3, 3, '2025-05-27 00:38:08', '2025-05-27 00:39:12'),
(7, 3, 3, '2025-05-27 00:39:15', '2025-05-27 00:47:42'),
(8, 3, 3, '2025-05-27 00:49:07', '2025-05-27 00:49:20'),
(9, 3, 3, '2025-05-27 01:26:55', '2025-05-27 01:51:38');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `image_path`) VALUES
(1, 'Alice Smith', 'alice@example.com', NULL),
(2, 'Bob Johnson', 'bob@example.com', NULL),
(3, 'Jay-ar C. Gapol', 'gapoljayar945@gmail.com', NULL),
(4, 'Jay-ar C. Gapol', '18801203-student@ndu.edu.ph', NULL),
(5, 'Jay-ar C. Gapol', 'gapoljayar@gmail.com', NULL),
(6, 'Jay-ar C. Gapol', 'jay@gmail.com', NULL),
(7, 'Jay-ar C. Gapol', '123@gmail.com', 'uploads/students/6834aa15edebe.png');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD CONSTRAINT `checkouts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkouts_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
