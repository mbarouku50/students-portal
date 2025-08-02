-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 02, 2025 at 08:46 PM
-- Server version: 10.11.13-MariaDB-0+deb12u1
-- PHP Version: 8.2.28

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
(3, 1, 'SeifiPlastics_Website_Proposal.doc', '/var/www/html/students-portal/stationary/uploads/20250731191359_SeifiPlastics_Website_Proposal.doc', '2025-07-31 22:13:59', 'pending', 'mbaruk', '0627841861', 'station1', 1, 'black', '');

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
(1, 'DIGITAL SOLUTIONS', 'CBE', '0716501250', 'digitalsolutions@gmail.com', '0716501250', 'your welcome to print with us thanks', 4, 100.00, '9e7d603f8e7dac32e54ced0eb3f9da384f912dce', '2025-07-31 21:08:17');

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
(1, 'mbaruk', 'mbarukhemedy50@gmail.com', '03.2481.01.01.2023', '14ee1fd936792f7de947af879df34e216484bc9d', 'BIT', '2', '2025-05-30 17:02:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `print_submissions`
--
ALTER TABLE `print_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stationery`
--
ALTER TABLE `stationery`
  ADD PRIMARY KEY (`stationery_id`);

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
-- AUTO_INCREMENT for table `print_submissions`
--
ALTER TABLE `print_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stationery`
--
ALTER TABLE `stationery`
  MODIFY `stationery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
