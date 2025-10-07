-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 07, 2025 at 10:43 AM
-- Server version: 10.11.14-MariaDB-0+deb12u2
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
(3, 1, 1, 'poa mzm wew', 1, '2025-09-16 13:36:57'),
(4, 5, 1, 'hi', 0, '2025-09-17 11:03:13'),
(5, 5, 1, 'hi', 0, '2025-09-17 11:03:33'),
(6, 5, 1, 'hi', 0, '2025-09-17 11:36:45');

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
(3, 1, 'cover_pages', 'introo', '', 'uploads/documents/68c9177721a69_1758009207.pdf', 'TIMETABLE.pdf', 461161, 'pdf', 'bachelor1', '2025-09-16 07:53:27', 0, 0),
(4, 6, 'lecture_notes', 'CAREER DEVELOPMENT', 'career first draft 2023/2024', 'uploads/documents/68e4cc60880ed_1759824992.pdf', 'career1.pdf', 55406, 'pdf', 'bachelor1', '2025-10-07 08:16:32', 0, 0),
(5, 6, 'lecture_notes', 'CAREER DEVELOPMENT', 'career second draft 2023/2024', 'uploads/documents/68e4cd583a5c2_1759825240.pdf', 'SECOND DRAFT-CAREER MANAGEMENT.pdf', 716041, 'pdf', 'bachelor1', '2025-10-07 08:20:40', 0, 0),
(6, 6, 'lecture_notes', 'CORPORATE BUSINESS COMMUNICATION', 'TOPIC ONE 2023/2024', 'uploads/documents/68e4cea200ee1_1759825570.pdf', 'TOPIC ONE.pdf', 288532, 'pdf', 'bachelor1', '2025-10-07 08:26:10', 0, 0),
(7, 6, 'lecture_notes', 'CORPORATE BUSINESS COMMUNICATION', 'CORPORATE BUSINESS COMMUNICATION - NOTEs 2023/2024', 'uploads/documents/68e4ceff5dd1c_1759825663.docx', 'CORPORATE BUSINESS COMMUNICATION - NOTES 2023.docx', 1287995, 'docx', 'bachelor1', '2025-10-07 08:27:43', 0, 0),
(8, 6, 'lecture_notes', 'COMPUTER ARCHITECTURE', '01 COMPUTER INTRODUCTION 2023/2024', 'uploads/documents/68e4d03e7dc3a_1759825982.pptx', '01 COMPUTER INTRODUCTION.pptx', 233249, 'pptx', 'bachelor1', '2025-10-07 08:33:02', 0, 0),
(9, 6, 'lecture_notes', 'COMPUTER ARCHITECTURE', '02. CLASSIFICATION OF COMPUTER 2023/2024', 'uploads/documents/68e4d09bab2d2_1759826075.pptx', '02. CLASSIFICATION OF COMPUTER.pptx', 355833, 'pptx', 'bachelor1', '2025-10-07 08:34:35', 0, 0),
(10, 6, 'lecture_notes', 'COMPUTER ARCHITECTURE', '03. GENERATIONS OF COMPUTER 2023/2024', 'uploads/documents/68e4d113e3e4e_1759826195.pdf', '03. GENERATIONS OF COMPUTER.pdf', 62449, 'pdf', 'bachelor1', '2025-10-07 08:36:35', 0, 0),
(11, 6, 'lecture_notes', 'COMPUTER ARCHITECTURE', '4 OPERATING SYSTEM 2023/2024', 'uploads/documents/68e4d14ceaf25_1759826252.pdf', '4 OPERATING SYSTEM.pdf', 449934, 'pdf', 'bachelor1', '2025-10-07 08:37:32', 0, 0),
(12, 6, 'lecture_notes', 'COMPUTER ARCHITECTURE', '5 MICROPROCESSOR 2023/2024', 'uploads/documents/68e4d1fd6c080_1759826429.pdf', '5 MICROPROCESSOR.pdf', 370663, 'pdf', 'bachelor1', '2025-10-07 08:40:29', 0, 0),
(13, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 1 2023/2024', 'uploads/documents/68e4d2d09f487_1759826640.pdf', 'Lecture 1.pdf', 889911, 'pdf', 'bachelor1', '2025-10-07 08:44:00', 0, 0),
(14, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 2 2023/2024', 'uploads/documents/68e4d36b1bab4_1759826795.pdf', 'Lecture 2.pdf', 570124, 'pdf', 'bachelor1', '2025-10-07 08:46:35', 0, 0),
(15, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 3', 'uploads/documents/68e4d39eb4233_1759826846.pdf', 'Lecture 3.pdf', 1250801, 'pdf', 'bachelor1', '2025-10-07 08:47:26', 0, 0),
(16, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 4', 'uploads/documents/68e4d3be7ae51_1759826878.pdf', 'Lecture 4.pdf', 767589, 'pdf', 'bachelor1', '2025-10-07 08:47:58', 0, 0),
(17, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 5 2023/2024', 'uploads/documents/68e4d3f190f8c_1759826929.pdf', 'Lecture 5.pdf', 644230, 'pdf', 'bachelor1', '2025-10-07 08:48:49', 0, 0),
(18, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 6 2023/2024', 'uploads/documents/68e4d4149d00b_1759826964.pdf', 'Lecture 6.pdf', 716917, 'pdf', 'bachelor1', '2025-10-07 08:49:24', 0, 0),
(19, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 7 2023/2024', 'uploads/documents/68e4d43dc9915_1759827005.pdf', 'Lecture 7.pdf', 713400, 'pdf', 'bachelor1', '2025-10-07 08:50:05', 0, 0),
(20, 6, 'lecture_notes', 'DATABASE ESSENTIAL', 'Lecture 8 2023/2024', 'uploads/documents/68e4d499aa2f7_1759827097.pdf', 'Lecture 8.pdf', 472590, 'pdf', 'bachelor1', '2025-10-07 08:51:37', 0, 0),
(21, 6, 'lecture_notes', 'PROGRAMMING IN C', 'Module 1 2023/2024', 'uploads/documents/68e4d55e6fe65_1759827294.pdf', 'Module 1.pdf', 1004594, 'pdf', 'bachelor1', '2025-10-07 08:54:54', 0, 0),
(22, 6, 'lecture_notes', 'PROGRAMMING IN C', 'Module 2 2023/2024', 'uploads/documents/68e4d5b7728a6_1759827383.pdf', 'Module 2.pdf', 570852, 'pdf', 'bachelor1', '2025-10-07 08:56:23', 0, 0),
(23, 6, 'lecture_notes', 'PROGRAMMING IN C', 'Module 3 - Notes 2023/2024', 'uploads/documents/68e4d5eb7659c_1759827435.pdf', 'Module 3 - Notes.pdf', 639140, 'pdf', 'bachelor1', '2025-10-07 08:57:15', 0, 0);

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

--
-- Dumping data for table `sem2_bachelor1_documents`
--

INSERT INTO `sem2_bachelor1_documents` (`doc_id`, `course_id`, `doc_type`, `title`, `description`, `file_path`, `file_name`, `file_size`, `file_type`, `level`, `uploaded_at`, `uploaded_by`, `download_count`) VALUES
(1, 6, 'lecture_notes', 'DEVELOPMENT STUDIES', 'corruption 2023/2024', 'uploads/documents/68e4d77b4a737_1759827835.pdf', 'corruption.pdf', 122861, 'pdf', 'bachelor1', '2025-10-07 09:03:55', 0, 0),
(2, 6, 'lecture_notes', 'DEVELOPMENT STUDIES', 'CORRUPTION 2023/2024', 'uploads/documents/68e4d8a8c22e6_1759828136.pptx', 'CORRUPTION.pptx', 1175494, 'pptx', 'bachelor1', '2025-10-07 09:08:56', 0, 0),
(3, 6, 'lecture_notes', 'DEVELOPMENT STUDIES', 'DEMOCRACY ', 'uploads/documents/68e4d8f760c31_1759828215.pptx', 'DEMOCRACY.pptx', 102358, 'pptx', 'bachelor1', '2025-10-07 09:10:15', 0, 0),
(4, 6, 'lecture_notes', 'DEVELOPMENT STUDIES', 'Development Study-1 2023/2024', 'uploads/documents/68e4d9548d801_1759828308.pdf', 'Development Study-1.pdf', 734914, 'pdf', 'bachelor1', '2025-10-07 09:11:48', 0, 0),
(5, 6, 'lecture_notes', 'DEVELOPMENT STUDIES', 'GOOD GOVERNANCE 2023/2024', 'uploads/documents/68e4d9cf9a724_1759828431.pptx', 'GOOD GOVERNANCE.pptx', 80993, 'pptx', 'bachelor1', '2025-10-07 09:13:51', 0, 0),
(6, 6, 'lecture_notes', 'DEVELOPMENT STUDIES', 'HUMAN RIGHTS IN RELATION TO DEVELOPMENT IN TANZANIA 2023/2024', 'uploads/documents/68e4da0465bee_1759828484.pptx', 'HUMAN RIGHTS IN RELATION TO DEVELOPMENT IN TANZANIA.pptx', 285797, 'pptx', 'bachelor1', '2025-10-07 09:14:44', 0, 0),
(7, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'Lecture 1-Computer Networks-1 2023/2024', 'uploads/documents/68e4daf62941e_1759828726.pptx', 'Lecture 1-Computer Networks-1.pptx', 1143240, 'pptx', 'bachelor1', '2025-10-07 09:18:46', 0, 0),
(8, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'Lecture 2 2023/2024', 'uploads/documents/68e4db3b63e40_1759828795.ppt', 'Lecture 2.ppt', 2050560, 'ppt', 'bachelor1', '2025-10-07 09:19:55', 0, 0),
(9, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'Lecture 2-Network Media 2023/2024', 'uploads/documents/68e4db79e937a_1759828857.docx', 'Lecture 2-Network Media.docx', 21988, 'docx', 'bachelor1', '2025-10-07 09:20:58', 0, 0),
(10, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'Lecture 2-UTP Cable 2023/2024', 'uploads/documents/68e4dba9a0fbe_1759828905.pptx', 'Lecture 2-UTP Cable.pptx', 1151135, 'pptx', 'bachelor1', '2025-10-07 09:21:45', 0, 0),
(11, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'Network_Topologies 2023/2024', 'uploads/documents/68e4dc4801776_1759829064.ppt', 'Network_Topologies.ppt', 1335296, 'ppt', 'bachelor1', '2025-10-07 09:24:24', 0, 0),
(12, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'OSI PPT 2023/2024', 'uploads/documents/68e4dcb29741c_1759829170.pptx', 'OSI PPT.pptx', 1200191, 'pptx', 'bachelor1', '2025-10-07 09:26:10', 0, 0),
(13, 6, 'lecture_notes', 'COMPUTER NETWORKS ADMINISTRATION', 'IP_ADDRESSING 2023/2024', 'uploads/documents/68e4dd414af00_1759829313.pdf', 'IP_ADDRESSING.pdf', 279662, 'pdf', 'bachelor1', '2025-10-07 09:28:33', 0, 0),
(14, 6, 'lecture_notes', 'OBJECTIVE ORIENTED PROGRAMMING C++', 'C++_Notes_Improved 2023/2024', 'uploads/documents/68e4de534d39e_1759829587.pdf', 'C++_Notes_Improved.pdf', 526674, 'pdf', 'bachelor1', '2025-10-07 09:33:07', 0, 0),
(15, 6, 'projects', 'FIELD REPORT', 'Example of field report for DISTRICT COUNCIL', 'uploads/documents/68e4df60a2ca0_1759829856.pdf', 'm_boy report.pdf', 475940, 'pdf', 'bachelor1', '2025-10-07 09:37:36', 0, 0),
(16, 6, 'lecture_notes', 'OPERATING SYSTEM', '1. Introduction 2023/2024', 'uploads/documents/68e4eb1ca6210_1759832860.pdf', '1. Introduction.pdf', 3331118, 'pdf', 'bachelor1', '2025-10-07 10:27:40', 0, 0),
(17, 6, 'lecture_notes', 'OPERATING SYSTEM', '2. Operating-System Services 2023/2024', 'uploads/documents/68e4eb6019bec_1759832928.pdf', '2. Operating-System Services.pdf', 8230001, 'pdf', 'bachelor1', '2025-10-07 10:28:48', 0, 0),
(18, 6, 'lecture_notes', 'OPERATING SYSTEM', '3. Processes 2023/2024', 'uploads/documents/68e4eb9b16459_1759832987.pdf', '3. Processes.pdf', 4054855, 'pdf', 'bachelor1', '2025-10-07 10:29:47', 0, 0),
(19, 6, 'lecture_notes', 'OPERATING SYSTEM', '4. Threads & Concurrency 2023/2024', 'uploads/documents/68e4ebc56d2ff_1759833029.pdf', '4. Threads & Concurrency.pdf', 4628385, 'pdf', 'bachelor1', '2025-10-07 10:30:29', 0, 0),
(20, 6, 'lecture_notes', 'OPERATING SYSTEM', '5. CPU Scheduling 2023/2024', 'uploads/documents/68e4ebf138a85_1759833073.pdf', '5. CPU Scheduling.pdf', 4747951, 'pdf', 'bachelor1', '2025-10-07 10:31:13', 0, 0),
(21, 6, 'lecture_notes', 'OPERATING SYSTEM', '6.2 Synchronization Examplee 2023/2024', 'uploads/documents/68e4ec3023780_1759833136.pdf', '6.2 Synchronization Examplee.pdf', 1425376, 'pdf', 'bachelor1', '2025-10-07 10:32:16', 0, 0),
(22, 6, 'lecture_notes', 'OPERATING SYSTEM', '6. synchronization-tools 2023/2024', 'uploads/documents/68e4ec5990963_1759833177.pdf', '6. synchronization-tools.pdf', 1071601, 'pdf', 'bachelor1', '2025-10-07 10:32:57', 0, 0),
(23, 6, 'lecture_notes', 'OPERATING SYSTEM', '7. Memory Management 2023/2024', 'uploads/documents/68e4ec87b44ca_1759833223.pdf', '7. Memory Management.pdf', 1336186, 'pdf', 'bachelor1', '2025-10-07 10:33:43', 0, 0),
(24, 6, 'lecture_notes', 'OPERATING SYSTEM', '8. Deadlocks 2023/2024', 'uploads/documents/68e4ecbcad65a_1759833276.pdf', '8. Deadlocks.pdf', 925840, 'pdf', 'bachelor1', '2025-10-07 10:34:36', 0, 0),
(25, 6, 'lecture_notes', 'WEB DESIGN', 'Module 1 - Basics of Web Design - Handout  2023/2024', 'uploads/documents/68e4ed7a548f8_1759833466.pdf', 'Module 1 - Basics of Web Design - Handout.pdf', 2002959, 'pdf', 'bachelor1', '2025-10-07 10:37:46', 0, 0),
(26, 6, 'lecture_notes', 'WEB DESIGN', 'Module 2 - HTML 2023/2024', 'uploads/documents/68e4eda5e0a7a_1759833509.pdf', 'Module 2 - HTML.pdf', 152557, 'pdf', 'bachelor1', '2025-10-07 10:38:29', 0, 0),
(27, 6, 'lecture_notes', 'WEB DESIGN', 'Module 3 - CSS 2023/2024', 'uploads/documents/68e4edd19909f_1759833553.pdf', 'Module 3 - CSS.pdf', 125367, 'pdf', 'bachelor1', '2025-10-07 10:39:13', 0, 0),
(28, 6, 'lecture_notes', 'WEB DESIGN', 'Module 4 - JavaScript 2023/2024', 'uploads/documents/68e4ee1ee7d1c_1759833630.pdf', 'Module 4 - JavaScript.pdf', 128513, 'pdf', 'bachelor1', '2025-10-07 10:40:31', 0, 0);

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
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_category`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'CBE Doc\'s Store', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(2, 'timezone', 'PST', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(3, 'date_format', 'Y-m-d', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(4, 'system_mode', 'testing', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(5, 'maintenance_message', 'Our site is currently under test mode. Please check back later.', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(6, 'theme_color', '#721818', 'appearance', '2025-09-18 09:59:03', '2025-09-18 10:00:25'),
(7, 'theme_mode', 'dark', 'appearance', '2025-09-18 09:59:03', '2025-09-18 10:00:25'),
(8, 'enable_animations', '1', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(9, 'enable_shadows', '1', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(10, 'notify_new_user', '1', 'notifications', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(11, 'notify_order', '1', 'notifications', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(12, 'notify_error', '1', 'notifications', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(13, 'notification_email', 'notifications@example.com', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(14, 'alert_sound', 'none', 'notifications', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(15, 'alert_volume', '70', 'notifications', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(16, 'desktop_notifications', '1', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(17, '2fa_enabled', '1', 'security', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(18, 'login_attempts', '1', 'security', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(19, 'max_login_attempts', '5', 'security', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(20, 'lockout_time', '30', 'security', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(21, 'password_complexity', '1', 'security', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(22, 'min_password_length', '8', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(23, 'password_expiry', '90', 'security', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(24, 'prevent_reuse', '1', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(25, 'backup_schedule', 'daily', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(26, 'backup_retention', '30', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(27, 'cache_duration', '60', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(28, 'enable_gzip', '1', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(29, 'enable_logging', '1', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(30, 'log_retention', '30', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(31, 'api_enabled', '1', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(32, 'api_rate_limit', '100', 'advanced', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(33, 'cors_enabled', '1', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03'),
(34, 'cors_origins', 'https://yourdomain.com\r\nhttp://localhost:3000', 'general', '2025-09-18 09:59:03', '2025-09-18 09:59:03');

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
(1, 'mbaruku', 'mbarukhemedy50@gmail.com', '03.2481.01.01.2023', '6feedd98b28e276cb59045d067aaddd1a31f5b8c', 'HRM', '3', 'uploads/profile_pictures/profile_1_1757671765.png', '2025-10-07 12:00:39', '2025-05-30 17:02:48', 1, 1, 1, 1, 1, 0, 1),
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

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
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `print_jobs`
--
ALTER TABLE `print_jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sem1_bachelor1_documents`
--
ALTER TABLE `sem1_bachelor1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
