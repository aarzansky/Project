-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 29, 2025 at 09:55 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--

DROP TABLE IF EXISTS `blood_requests`;
CREATE TABLE IF NOT EXISTS `blood_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') COLLATE utf8mb4_unicode_ci NOT NULL,
  `units` int NOT NULL,
  `urgency` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `additional_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','fulfilled','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hospital_id` int NOT NULL,
  PRIMARY KEY (`request_id`),
  KEY `fk_hospital` (`hospital_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_requests`
--

INSERT INTO `blood_requests` (`request_id`, `blood_type`, `units`, `urgency`, `additional_notes`, `status`, `created_at`, `updated_at`, `hospital_id`) VALUES
(2, 'B+', 2, 'medium', 'nothing', 'active', '2025-12-12 16:25:26', '2025-12-12 16:25:26', 1),
(3, 'O+', 4, 'low', 'hello', 'active', '2025-12-12 21:34:59', '2025-12-12 21:34:59', 1),
(4, 'B+', 2, 'medium', 'Emegency', 'active', '2025-12-13 02:43:06', '2025-12-13 02:43:06', 1),
(5, 'O-', 5, 'urgent', '', 'active', '2025-12-13 02:43:51', '2025-12-13 02:43:51', 1),
(6, 'O+', 5, 'urgent', '', 'active', '2025-12-13 02:44:47', '2025-12-13 02:44:47', 1),
(7, 'O+', 3, 'medium', '', 'active', '2025-12-13 02:45:37', '2025-12-13 02:45:37', 1),
(8, 'AB+', 4, 'urgent', 'Emergency', 'active', '2025-12-29 09:12:05', '2025-12-29 09:12:05', 1),
(9, 'AB+', 4, 'urgent', 'Emergency', 'active', '2025-12-29 09:16:37', '2025-12-29 09:16:37', 1),
(10, 'AB+', 4, 'urgent', 'Emergency', 'active', '2025-12-29 09:17:03', '2025-12-29 09:17:03', 1),
(11, 'AB+', 4, 'urgent', 'Emergency', 'active', '2025-12-29 09:18:25', '2025-12-29 09:18:25', 1),
(12, 'AB-', 4, 'low', 'Important', 'active', '2025-12-29 09:21:53', '2025-12-29 09:21:53', 1),
(13, 'A+', 5, 'high', 'Emergency Blood', 'active', '2025-12-29 09:27:36', '2025-12-29 09:27:36', 1),
(14, 'A+', 5, 'high', 'Emergency Blood', 'active', '2025-12-29 09:35:12', '2025-12-29 09:35:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
CREATE TABLE IF NOT EXISTS `donations` (
  `donation_id` int NOT NULL AUTO_INCREMENT,
  `donor_id` int NOT NULL,
  `request_id` int DEFAULT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') COLLATE utf8mb4_unicode_ci NOT NULL,
  `units_donated` int NOT NULL DEFAULT '1',
  `donation_date` date NOT NULL,
  `donation_status` enum('scheduled','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`donation_id`),
  KEY `fk_donor` (`donor_id`),
  KEY `fk_request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

DROP TABLE IF EXISTS `donors`;
CREATE TABLE IF NOT EXISTS `donors` (
  `donor_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `medical_history` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lastdonation` date DEFAULT NULL,
  PRIMARY KEY (`donor_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`donor_id`, `full_name`, `email`, `phone_number`, `blood_type`, `address`, `medical_history`, `id_proof`, `password`, `verification_status`, `created_at`, `lastdonation`) VALUES
(1, 'Aarzan Shakya', 'shakyaaarzan@gmail.com', '9818154284', 'A+', 'Nakabahil', 'test/Aarzan Shakya_Admit card.pdf', 'id_proof/Aarzan Shakya_ERDiagram.drawio.png', 'e474bc3ebe0a8f5ca2a8727a3090cb21', 'approved', '2025-12-10 16:49:52', '2022-02-28'),
(2, 'Aarzan Shakya', 'aarzansky7@gmail.com', '9841200646', 'A-', 'Nakabahil', 'test/Aarzan Shakya_Invitation.pdf', 'id_proof/Aarzan Shakya_ERDiagram.drawio (3).png', '40a8a6e5b778bfadcd51c350cadc1cbc', 'approved', '2025-12-12 03:48:23', '2025-12-10'),
(3, 'Amogh Shakya', 'amoghshakya@gmail.com', '9876543240', 'O+', 'Chyasal', 'test/Amogh Shakya_Medical Report.pdf', 'id_proof/Amogh Shakya_id.jpg', '45ba77db832d2036cddcd9863743158b', 'approved', '2025-12-12 21:30:26', '2025-12-01'),
(4, 'Ishahak Khadgi', 'ishahak@gmail.com', '9867854323', 'A+', 'Alko, Patan', 'test/Ishahak Khadgi_Medical Report.pdf', 'id_proof/Ishahak Khadgi_id.jpg', '75068ee1e23957e0e512d9b9dba4f6c7', 'approved', '2025-12-12 21:44:45', '2025-08-14'),
(5, 'Sujata Bajracharya', 'sujata@gmail.com', '9867543473', 'O+', 'Nakabahil-16, Patan', 'test/Sujata Bajracharya_Medical Report.pdf', 'id_proof/Sujata Bajracharya_id.jpg', '3f8d0274192fb7fc24dfd3de01361e44', 'approved', '2025-12-12 21:46:35', '2025-12-01'),
(6, 'Samanta Bajracharya', 'samanta@gmail.com', '9765432356', 'AB+', 'Lagankhel', 'test/Samanta Bajracharya_Medical Report.pdf', 'id_proof/Samanta Bajracharya_id.jpg', '2879a4ddff275fc9524e101176e8fc60', 'approved', '2025-12-12 21:53:10', '2025-07-01'),
(7, 'Meena Laxmi Shakya', 'meena@yahoo.com', '9876543213', 'A+', 'Nakabahil', 'test/Meena Laxmi Shakya_Medical Report.pdf', 'id_proof/Meena Laxmi Shakya_id.jpg', '7b1c9ad95718f5560b7774012f415778', 'rejected', '2025-12-12 21:54:58', '2025-12-09'),
(8, 'Mahesh Shakya', 'mahesh@gmail.com', '9876543119', 'A-', 'Nakabahil', 'test/Mahesh Shakya_Blooddonorsystem.pdf', 'id_proof/Mahesh Shakya_id.jpg', 'ab2c3c0f409beff460eed57e84dfed09', 'approved', '2025-12-13 02:42:06', '2025-11-05'),
(10, 'Aarzan Shakya', 'aarzanshakya@gmail.com', '9818154282', 'AB-', 'Nakabahil', 'test/Aarzan Shakya_Medical Report.pdf', 'id_proof/Aarzan Shakya_id.jpg', '40a8a6e5b778bfadcd51c350cadc1cbc', 'approved', '2025-12-29 09:21:19', '2025-12-01');

-- --------------------------------------------------------

--
-- Table structure for table `donor_responses`
--

DROP TABLE IF EXISTS `donor_responses`;
CREATE TABLE IF NOT EXISTS `donor_responses` (
  `response_id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `donor_id` int NOT NULL,
  `response_status` enum('pending','accepted','rejected','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `response_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`response_id`),
  KEY `fk_response_donor` (`donor_id`),
  KEY `fk_response_request` (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donor_responses`
--

INSERT INTO `donor_responses` (`response_id`, `request_id`, `donor_id`, `response_status`, `response_date`) VALUES
(1, 3, 3, 'accepted', '2025-12-12 21:38:21'),
(2, 3, 5, 'accepted', '2025-12-12 21:47:35'),
(3, 6, 3, 'accepted', '2025-12-13 02:45:10'),
(4, 7, 3, 'accepted', '2025-12-13 02:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

DROP TABLE IF EXISTS `hospitals`;
CREATE TABLE IF NOT EXISTS `hospitals` (
  `hospital_id` int NOT NULL AUTO_INCREMENT,
  `hospital_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact_person` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hospital_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hospital_id`, `hospital_name`, `email`, `phone_number`, `emergency_contact_person`, `address`, `city`, `district`, `registration_certificate`, `medical_license`, `log_password`, `verification_status`, `created_at`) VALUES
(1, 'Admin Hospital', 'admin@gmail.com', '1234567890', 'Admin User', 'System Address', 'System City', 'System District', NULL, NULL, '25d55ad283aa400af464c76d713c07ad', 'approved', '2025-12-10 16:48:32');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD CONSTRAINT `fk_hospital` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`);

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `fk_donor` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`),
  ADD CONSTRAINT `fk_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`request_id`);

--
-- Constraints for table `donor_responses`
--
ALTER TABLE `donor_responses`
  ADD CONSTRAINT `fk_response_donor` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`),
  ADD CONSTRAINT `fk_response_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`request_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
