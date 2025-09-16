-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 16, 2025 at 11:02 AM
-- Server version: 10.11.13-MariaDB-0+deb12u1
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cbe_student-portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `fullname`, `email`, `password`, `profile_picture`, `last_login`, `created_at`, `updated_at`, `status`) VALUES
(1, 'student-portal', 'studentportal@gmail.com', 'e0da2182b62b121fd1e27906138fee2e17645b40', NULL, NULL, '2025-09-10 06:54:01', '2025-09-10 09:29:07', 'active'),
(3, 'mbaruk', 'mbarukhemedy50@gmail.com', '3b03e19ae8e15690d7d34a189190f08dfcbc75e2', '68c3cfb4210e9.png', NULL, '2025-09-10 09:52:54', '2025-09-12 08:10:47', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_group` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `title`, `is_group`, `created_at`) VALUES
(1, 'bahati issa msuya', 0, '2025-09-16 13:35:50'),
(2, 'BIT', 0, '2025-09-16 13:37:21'),
(3, 'BIT', 0, '2025-09-16 13:37:25'),
(4, 'BIT', 1, '2025-09-16 13:43:38'),
(5, 'bahati issa msuya', 0, '2025-09-16 13:44:53');

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participants`
--

CREATE TABLE `conversation_participants` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversation_participants`
--

INSERT INTO `conversation_participants` (`id`, `conversation_id`, `user_id`) VALUES
(2, 1, 3),
(3, 2, 3),
(5, 3, 3),
(7, 4, 3),
(9, 5, 1),
(10, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(10) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `color_code` varchar(7) NOT NULL DEFAULT '#e74c3c',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_code`, `course_name`, `description`, `color_code`, `created_at`, `updated_at`) VALUES
(1, 'ACC', 'Accountancy', 'Comprehensive resources for accountancy students including financial accounting, auditing, and taxation materials.', '#101eda', '2025-08-07 09:22:05', '2025-08-07 13:26:12'),
(2, 'AF', 'Accounting and Finance', 'Documents covering financial management, corporate finance, investment analysis and accounting principles.\r\n\r\n', '#e74c3c', '2025-08-07 09:28:24', '2025-08-07 13:26:27'),
(3, 'AT', 'Accountancy and Taxation', 'Resources focused on tax laws, tax planning, and specialized accounting for taxation purposes.', '#049014', '2025-08-07 09:29:21', '2025-08-07 13:24:42'),
(4, 'BF', 'Banking and Finance', 'Materials on banking operations, financial institutions, risk management and monetary policy.', '#ddc108', '2025-08-07 09:30:16', '2025-08-07 13:27:11'),
(5, 'BA', 'Business Administration', 'General business documents covering management, operations, strategy and organizational behavior.', '#8a1dd3', '2025-08-07 09:33:10', '2025-08-07 13:27:21'),
(6, 'IT', 'Information Technology', 'Resources for IT students including programming, databases, networking and systems analysis.', '#14d279', '2025-08-07 09:33:45', '2025-08-07 13:27:58'),
(7, 'MES', 'Metrology and Standardization', 'Specialized documents on measurement science, quality standards and calibration techniques.', '#000424', '2025-08-07 09:35:12', '2025-08-07 13:28:43'),
(8, 'MK', 'Marketing', 'Marketing principles, consumer behavior, branding and digital marketing resources.', '#e77204', '2025-08-07 09:37:21', '2025-08-07 13:28:14'),
(9, 'MK-TEM', 'Marketing & Tourism/Events', 'Combined resources for marketing with focus on tourism industry and event management.', '#11ff00', '2025-08-07 09:38:13', '2025-08-07 13:28:31'),
(10, 'PS', 'Procurement & Supplies', 'Documents on supply chain management, procurement processes and inventory control.', '#21d477', '2025-08-07 09:39:09', '2025-08-07 13:28:59'),
(11, 'HRM', 'Human Resources Management', 'Resources covering recruitment, training, performance management and labor relations.', '#6b14c2', '2025-08-07 09:39:54', '2025-08-07 13:27:45'),
(12, 'BSE', 'Business Studies with Education', 'Combined business and education materials for those pursuing teaching careers in business.', '#ca5649', '2025-08-07 09:40:53', '2025-08-07 13:27:34'),
(13, 'RAM', 'Records and Archives Management', 'Documents on information management, archival science and records preservation.', '#969292', '2025-08-07 09:41:51', '2025-08-07 13:29:12');

-- --------------------------------------------------------

--
-- Table structure for table `coverpage_documents`
--

CREATE TABLE `coverpage_documents` (
  `id` int(11) NOT NULL,
  `course` varchar(100) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `year` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'In bytes',
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `year` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 1, 'hi', 1, '2025-09-16 13:36:00'),
(2, 1, 3, 'mamb', 1, '2025-09-16 13:36:38'),
(3, 1, 1, 'poa mzm wew', 1, '2025-09-16 13:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `print_jobs`
--

CREATE TABLE `print_jobs` (
  `job_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `stationery_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `copies` int(11) NOT NULL,
  `print_type` enum('black','color') NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_jobs`
--

INSERT INTO `print_jobs` (`job_id`, `user_name`, `phone_number`, `stationery_id`, `content`, `file_path`, `copies`, `print_type`, `special_instructions`, `status`, `created_at`) VALUES
(5, 'mbarouk hemed', '0627841861', 5, NULL, 'uploads/68a3047ae0292_SeifiPlastics_Website_Proposal.doc', 1, 'black', '', 'completed', '2025-08-18 10:46:18'),
(6, 'mbarouk hemed', '0627841861', 5, NULL, 'uploads/68a3049a6d0d1_m_boy report.pdf', 1, 'black', '', 'pending', '2025-08-18 10:46:50'),
(7, 'bee', '+255716501250', 1, NULL, 'uploads/68a304b6b4d99_GROUPS 2.pdf', 1, 'black', '', 'pending', '2025-08-18 10:47:18'),
(8, 'xhidy', '0627841861', 1, NULL, 'uploads/68c2ac19d3b86_XHIDY.pdf', 1, 'color', 'please edit', 'completed', '2025-09-11 11:01:45'),
(9, 'mwajuma', '+255716501250', 5, '', 'typed_1757590941_1205.doc', 1, 'black', 'dfghjk', 'pending', '2025-09-11 11:42:21'),
(10, 'mwajuma', '+255716501250', 5, '', 'uploads/typed_1757591186_1142.doc', 1, 'black', 'dfghjk', 'cancelled', '2025-09-11 11:46:26'),
(11, 'zuhura', '0987654321', 5, NULL, 'uploads/68c2b6e2ad4c4_cover_page (6).pdf', 1, 'black', 'o', 'pending', '2025-09-11 11:47:46'),
(12, 'mbarouk hemed', '+255716501250', 5, '', 'uploads/typed_1757592427_1392.doc', 1, 'black', '', 'pending', '2025-09-11 12:07:07');

-- --------------------------------------------------------

--
-- Table structure for table `sem1_bachelor1_documents`
--

CREATE TABLE `sem1_bachelor1_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sem1_bachelor1_documents`
--

INSERT INTO `sem1_bachelor1_documents` (`doc_id`, `course_id`, `doc_type`, `title`, `description`, `file_path`, `file_name`, `file_size`, `file_type`, `level`, `uploaded_at`, `uploaded_by`, `download_count`) VALUES
(2, 1, 'lecture_notes', 'Accountancy', '', 'uploads/documents/68c90c0c74109_1758006284.pdf', 'TIMETABLE.pdf', 461161, 'pdf', 'bachelor1', '2025-09-16 07:04:44', 0, 0),
(3, 1, 'cover_pages', 'introo', '', 'uploads/documents/68c9177721a69_1758009207.pdf', 'TIMETABLE.pdf', 461161, 'pdf', 'bachelor1', '2025-09-16 07:53:27', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sem1_bachelor2_documents`
--

CREATE TABLE `sem1_bachelor2_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem1_bachelor3_documents`
--

CREATE TABLE `sem1_bachelor3_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem1_certificate_documents`
--

CREATE TABLE `sem1_certificate_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem1_diploma1_documents`
--

CREATE TABLE `sem1_diploma1_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem1_diploma2_documents`
--

CREATE TABLE `sem1_diploma2_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem2_bachelor1_documents`
--

CREATE TABLE `sem2_bachelor1_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem2_bachelor2_documents`
--

CREATE TABLE `sem2_bachelor2_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem2_bachelor3_documents`
--

CREATE TABLE `sem2_bachelor3_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem2_certificate_documents`
--

CREATE TABLE `sem2_certificate_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem2_diploma1_documents`
--

CREATE TABLE `sem2_diploma1_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sem2_diploma2_documents`
--

CREATE TABLE `sem2_diploma2_documents` (
  `doc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stationery`
--

CREATE TABLE `stationery` (
  `stationery_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `opening_hours` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stationery`
--

INSERT INTO `stationery` (`stationery_id`, `name`, `location`, `address`, `phone`, `email`, `whatsapp`, `opening_hours`, `description`, `logo`, `quantity`, `price`, `password`, `created_at`, `updated_at`) VALUES
(1, 'DIGITAL SOLUTIONS', 'CBE', 'dar-es-salaam', '0716501250', 'digitalsolutions@gmail.com', '0716501250', '24/7', 'your welcome to print with us thanks', 'uploads/stationery_logos/stationery_1_1756371886.png', 4, 100.00, '9e7d603f8e7dac32e54ced0eb3f9da384f912dce', '2025-07-31 21:08:17', '2025-08-28 09:04:47'),
(5, 'AB STATIONARY', 'CBE', 'dar-es-salaam', '0627841861', 'ab@gmail.com', '0627841861', '24/7', 'huduma za uhakika 24 hours', 'uploads/stationery_logos/stationery_5_1756374207.png', 0, 100.00, 'e0da2182b62b121fd1e27906138fee2e17645b40', '2025-08-10 09:52:49', '2025-08-28 09:50:36');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_category` varchar(50) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `reg` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `program` varchar(20) NOT NULL,
  `year` varchar(10) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `email_notifications` tinyint(1) DEFAULT 1,
  `course_updates` tinyint(1) DEFAULT 1,
  `assignment_alerts` tinyint(1) DEFAULT 1,
  `newsletter` tinyint(1) DEFAULT 1,
  `profile_visibility` tinyint(1) DEFAULT 1,
  `show_email` tinyint(1) DEFAULT 0,
  `data_collection` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `reg`, `password`, `program`, `year`, `profile_picture`, `last_seen`, `created_at`, `email_notifications`, `course_updates`, `assignment_alerts`, `newsletter`, `profile_visibility`, `show_email`, `data_collection`) VALUES
(1, 'mbaruku', 'mbarukhemedy50@gmail.com', '03.2481.01.01.2023', '6feedd98b28e276cb59045d067aaddd1a31f5b8c', '', '3', 'uploads/profile_pictures/profile_1_1757671765.png', '2025-09-16 14:00:57', '2025-05-30 17:02:48', 1, 1, 1, 1, 1, 0, 1),
(3, 'bahati issa msuya', 'bahati@gmail.com', '03.1434.01.01.2023', 'b1734567c261fa3bb2628ac4883b0a587cf76161', 'IT', '2', 'uploads/profile_pictures/profile_3_1757928469.png', '2025-09-16 13:36:34', '2025-09-15 09:27:06', 1, 1, 1, 1, 1, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`);

--
-- Indexes for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participant` (`conversation_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `coverpage_documents`
--
ALTER TABLE `coverpage_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `print_jobs`
--
ALTER TABLE `print_jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `stationery_id` (`stationery_id`);

--
-- Indexes for table `sem1_bachelor1_documents`
--
ALTER TABLE `sem1_bachelor1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem1_bachelor2_documents`
--
ALTER TABLE `sem1_bachelor2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem1_bachelor3_documents`
--
ALTER TABLE `sem1_bachelor3_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem1_certificate_documents`
--
ALTER TABLE `sem1_certificate_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem1_diploma1_documents`
--
ALTER TABLE `sem1_diploma1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem1_diploma2_documents`
--
ALTER TABLE `sem1_diploma2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem2_bachelor1_documents`
--
ALTER TABLE `sem2_bachelor1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem2_bachelor2_documents`
--
ALTER TABLE `sem2_bachelor2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem2_bachelor3_documents`
--
ALTER TABLE `sem2_bachelor3_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem2_certificate_documents`
--
ALTER TABLE `sem2_certificate_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem2_diploma1_documents`
--
ALTER TABLE `sem2_diploma1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sem2_diploma2_documents`
--
ALTER TABLE `sem2_diploma2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `stationery`
--
ALTER TABLE `stationery`
  ADD PRIMARY KEY (`stationery_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `category_name` (`setting_category`,`setting_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reg` (`reg`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `coverpage_documents`
--
ALTER TABLE `coverpage_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `print_jobs`
--
ALTER TABLE `print_jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sem1_bachelor1_documents`
--
ALTER TABLE `sem1_bachelor1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sem1_bachelor2_documents`
--
ALTER TABLE `sem1_bachelor2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem1_bachelor3_documents`
--
ALTER TABLE `sem1_bachelor3_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem1_certificate_documents`
--
ALTER TABLE `sem1_certificate_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem1_diploma1_documents`
--
ALTER TABLE `sem1_diploma1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem1_diploma2_documents`
--
ALTER TABLE `sem1_diploma2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sem2_bachelor1_documents`
--
ALTER TABLE `sem2_bachelor1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem2_bachelor2_documents`
--
ALTER TABLE `sem2_bachelor2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem2_bachelor3_documents`
--
ALTER TABLE `sem2_bachelor3_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem2_certificate_documents`
--
ALTER TABLE `sem2_certificate_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sem2_diploma1_documents`
--
ALTER TABLE `sem2_diploma1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sem2_diploma2_documents`
--
ALTER TABLE `sem2_diploma2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stationery`
--
ALTER TABLE `stationery`
  MODIFY `stationery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`),
  ADD CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `coverpage_documents`
--
ALTER TABLE `coverpage_documents`
  ADD CONSTRAINT `coverpage_documents_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `print_jobs`
--
ALTER TABLE `print_jobs`
  ADD CONSTRAINT `print_jobs_ibfk_1` FOREIGN KEY (`stationery_id`) REFERENCES `stationery` (`stationery_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
