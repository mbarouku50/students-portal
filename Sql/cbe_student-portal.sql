-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 19, 2025 at 07:11 AM
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
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `fullname`, `email`, `password`) VALUES
(1, 'student-portal', 'studentportal@gmail.com', 'e0da2182b62b121fd1e27906138fee2e17645b40');

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
-- Table structure for table `first_year_sem1_documents`
--

CREATE TABLE `first_year_sem1_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `first_year_sem2_documents`
--

CREATE TABLE `first_year_sem2_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fourth_year_sem1_documents`
--

CREATE TABLE `fourth_year_sem1_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fourth_year_sem2_documents`
--

CREATE TABLE `fourth_year_sem2_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 'mbarouk hemed', '0627841861', 5, NULL, 'uploads/68a3047ae0292_SeifiPlastics_Website_Proposal.doc', 1, 'black', '', 'pending', '2025-08-18 10:46:18'),
(6, 'mbarouk hemed', '0627841861', 5, NULL, 'uploads/68a3049a6d0d1_m_boy report.pdf', 1, 'black', '', 'completed', '2025-08-18 10:46:50'),
(7, 'bee', '+255716501250', 1, NULL, 'uploads/68a304b6b4d99_GROUPS 2.pdf', 1, 'black', '', 'pending', '2025-08-18 10:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `print_submissions`
--

CREATE TABLE `print_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submission_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pending',
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `station` varchar(100) DEFAULT NULL,
  `copies` int(11) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `print_submissions`
--

INSERT INTO `print_submissions` (`id`, `user_id`, `document_name`, `file_path`, `submission_date`, `status`, `name`, `phone`, `station`, `copies`, `color`, `notes`) VALUES
(1, NULL, 'SeifiPlastics_Website_Proposal.doc', '/var/www/html/students-portal/stationary/uploads/20250731190717_SeifiPlastics_Website_Proposal.doc', '2025-07-31 22:07:17', 'pending', 'mbarouk hemed', '0627841861', 'station1', 1, 'color', ''),
(2, NULL, 'SeifiPlastics_Website_Proposal.doc', '/var/www/html/students-portal/stationary/uploads/20250731190923_SeifiPlastics_Website_Proposal.doc', '2025-07-31 22:09:23', 'pending', 'mbarouk hemed', '0627841861', 'station1', 1, 'color', ''),
(3, 1, 'SeifiPlastics_Website_Proposal.doc', '/var/www/html/students-portal/stationary/uploads/20250731191359_SeifiPlastics_Website_Proposal.doc', '2025-08-04 12:32:04', 'completed', 'mbaruk', '0627841861', 'station1', 1, 'black', '');

-- --------------------------------------------------------

--
-- Table structure for table `second_year_sem1_documents`
--

CREATE TABLE `second_year_sem1_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `second_year_sem2_documents`
--

CREATE TABLE `second_year_sem2_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stationery`
--

CREATE TABLE `stationery` (
  `stationery_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stationery`
--

INSERT INTO `stationery` (`stationery_id`, `name`, `location`, `phone`, `email`, `whatsapp`, `description`, `quantity`, `price`, `password`, `created_at`) VALUES
(1, 'DIGITAL SOLUTIONS', 'CBE', '0716501250', 'digitalsolutions@gmail.com', '0716501250', 'your welcome to print with us thanks', 4, 100.00, '9e7d603f8e7dac32e54ced0eb3f9da384f912dce', '2025-07-31 21:08:17'),
(5, 'AB STATIONARY', 'morogoro road', '0627841861', 'ab@gmail.com', '0627841861', 'huduma za uhakika 24 hours', 1, 100.00, 'e0da2182b62b121fd1e27906138fee2e17645b40', '2025-08-10 09:52:49');

-- --------------------------------------------------------

--
-- Table structure for table `third_year_sem1_documents`
--

CREATE TABLE `third_year_sem1_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `third_year_sem2_documents`
--

CREATE TABLE `third_year_sem2_documents` (
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
  `uploaded_by` int(11) DEFAULT NULL
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
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `reg`, `password`, `program`, `year`, `created_at`) VALUES
(1, 'mbaruk', 'mbarukhemedy50@gmail.com', '03.2481.01.01.2023', '14ee1fd936792f7de947af879df34e216484bc9d', 'BIT', '2', '2025-05-30 17:02:48'),
(2, 'cbe', 'cbe@gmail.com', '03.0001.01.01.2023', '12d58bfadd6a377ef2d2c83c67f027764440cd01', 'BMES', '2', '2025-08-07 07:47:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

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
-- Indexes for table `first_year_sem1_documents`
--
ALTER TABLE `first_year_sem1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `first_year_sem2_documents`
--
ALTER TABLE `first_year_sem2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `fourth_year_sem1_documents`
--
ALTER TABLE `fourth_year_sem1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `fourth_year_sem2_documents`
--
ALTER TABLE `fourth_year_sem2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `print_jobs`
--
ALTER TABLE `print_jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `stationery_id` (`stationery_id`);

--
-- Indexes for table `print_submissions`
--
ALTER TABLE `print_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `second_year_sem1_documents`
--
ALTER TABLE `second_year_sem1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `second_year_sem2_documents`
--
ALTER TABLE `second_year_sem2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `stationery`
--
ALTER TABLE `stationery`
  ADD PRIMARY KEY (`stationery_id`);

--
-- Indexes for table `third_year_sem1_documents`
--
ALTER TABLE `third_year_sem1_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `third_year_sem2_documents`
--
ALTER TABLE `third_year_sem2_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `course_id` (`course_id`);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `first_year_sem1_documents`
--
ALTER TABLE `first_year_sem1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `first_year_sem2_documents`
--
ALTER TABLE `first_year_sem2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fourth_year_sem1_documents`
--
ALTER TABLE `fourth_year_sem1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fourth_year_sem2_documents`
--
ALTER TABLE `fourth_year_sem2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `print_jobs`
--
ALTER TABLE `print_jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `print_submissions`
--
ALTER TABLE `print_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `second_year_sem1_documents`
--
ALTER TABLE `second_year_sem1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `second_year_sem2_documents`
--
ALTER TABLE `second_year_sem2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stationery`
--
ALTER TABLE `stationery`
  MODIFY `stationery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `third_year_sem1_documents`
--
ALTER TABLE `third_year_sem1_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `third_year_sem2_documents`
--
ALTER TABLE `third_year_sem2_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `first_year_sem1_documents`
--
ALTER TABLE `first_year_sem1_documents`
  ADD CONSTRAINT `first_year_sem1_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `first_year_sem2_documents`
--
ALTER TABLE `first_year_sem2_documents`
  ADD CONSTRAINT `first_year_sem2_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `fourth_year_sem1_documents`
--
ALTER TABLE `fourth_year_sem1_documents`
  ADD CONSTRAINT `fourth_year_sem1_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `fourth_year_sem2_documents`
--
ALTER TABLE `fourth_year_sem2_documents`
  ADD CONSTRAINT `fourth_year_sem2_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `print_jobs`
--
ALTER TABLE `print_jobs`
  ADD CONSTRAINT `print_jobs_ibfk_1` FOREIGN KEY (`stationery_id`) REFERENCES `stationery` (`stationery_id`);

--
-- Constraints for table `second_year_sem1_documents`
--
ALTER TABLE `second_year_sem1_documents`
  ADD CONSTRAINT `second_year_sem1_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `second_year_sem2_documents`
--
ALTER TABLE `second_year_sem2_documents`
  ADD CONSTRAINT `second_year_sem2_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `third_year_sem1_documents`
--
ALTER TABLE `third_year_sem1_documents`
  ADD CONSTRAINT `third_year_sem1_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `third_year_sem2_documents`
--
ALTER TABLE `third_year_sem2_documents`
  ADD CONSTRAINT `third_year_sem2_documents_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
