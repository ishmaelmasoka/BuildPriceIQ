-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2026 at 07:55 PM
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
-- Database: `buildpriceiq`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES
(5, 'maoska', 'iiii@gmailcom', 'services', 'maone aya', 0, '2026-06-05 01:11:43'),
(6, 'maoska', 'iiii@gmailcom', 'services', 'wasssup', 0, '2026-06-05 17:50:07');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `category`, `unit`) VALUES
(1, 'Superset Cement 50kg', 'cement', 'bag'),
(2, 'Common Bricks (per 1000)', 'bricks', 'per 1000'),
(3, '12mm Steel Rebar', 'steel', 'length'),
(4, 'River Sand', 'sand', 'cubic meter'),
(5, 'Timber Pine 4x2', 'timber', 'piece'),
(6, 'Portland Cement 50kg', 'cement', 'bag'),
(7, 'Common Bricks (per 1000)', 'bricks', 'per 1000'),
(8, '12mm Steel Rebar', 'steel', 'length'),
(9, 'River Sand', 'sand', 'cubic meter'),
(10, 'Timber Pine 4x2', 'timber', 'piece'),
(11, 'Portland Cement 50kg', 'cement', 'bag'),
(12, 'Common Bricks', 'bricks', 'per 1000'),
(13, '12mm Steel Rebar', 'steel', 'length'),
(14, 'Portland Cement 50kg', 'cement', 'bag'),
(15, 'Common Bricks', 'bricks', 'per 1000'),
(16, '12mm Steel Rebar', 'steel', 'length'),
(17, 'Portland Cement 50kg', 'cement', 'bag'),
(18, 'Common Bricks', 'bricks', 'per 1000'),
(19, '12mm Steel Rebar', 'steel', 'length'),
(20, 'cemment', 'cement', 'piece'),
(21, 'Supaset', 'cement', 'piece'),
(22, 'Pinewood', 'timber', 'piece'),
(23, 'Angle bar', 'steel', 'piece');

-- --------------------------------------------------------

--
-- Table structure for table `price_history`
--

CREATE TABLE `price_history` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `date_recorded` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `price_history`
--

INSERT INTO `price_history` (`id`, `supplier_id`, `material_id`, `price`, `date_recorded`) VALUES
(15, 8, 20, 11.00, '2026-06-03 09:02:32'),
(16, 8, 21, 11.00, '2026-06-03 09:04:19'),
(17, 8, 12, 10.00, '2026-06-05 01:13:48'),
(19, 8, 23, 6.00, '2026-06-05 01:15:38'),
(21, 10, 22, 5.00, '2026-06-05 17:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_items` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `user_id`, `business_name`, `location`, `phone`, `is_approved`, `created_at`, `stock_items`) VALUES
(8, 12, 'bricks', 'gweru', '0775345828', 1, '2026-06-03 08:31:26', 'Common Bricks: 1000\nRiver sand: 20 tones'),
(10, 14, 'sandpro', 'mutare', '0775345828', 1, '2026-06-03 10:08:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','supplier','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Pikamapise Hardware', 'Pikamapise@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supplier', '2026-05-31 20:40:23'),
(4, 'ishmael', 'ishmaelmasoka@gmail.com', '$2y$10$CcEr1zy5XUxeevr.xVwoUeeMHkd8PwrSBNRLmRAWRDlpuQw7VxULO', 'supplier', '2026-06-01 04:32:46'),
(6, 'Administrator', 'admin@buildpriceiq.com', '$2y$10$OQOjv15.FBryBDA9xxRLNeOctqfTsjCl4UGuRr.qEp9NDIRgXRFmW', 'admin', '2026-06-02 14:48:51'),
(7, 'ishmael', 'iiiii@gmail.com', '$2y$10$54DMzlGU5DWSW1S53FD5IeQWAUn4oNQG4ttWxX.ruRfEY3ZbXtgX.', 'supplier', '2026-06-02 18:08:44'),
(12, 'ishmael', 'bricks@gmail.com', '$2y$10$9iIutvqnO3om652HjnTT1OAW4rnT59uW.ea1RnNgjl/6KuDbYJ6/K', 'supplier', '2026-06-03 08:31:26'),
(14, 'Tadiwa', 'tadiwa@gmail.com', '$2y$10$gbQmR3lw7IiM.fcQ3E4T0.3DhbB7krG1T29hBlNRzd/7cmkhZB1yu', 'supplier', '2026-06-03 10:08:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `price_history`
--
ALTER TABLE `price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `idx_supplier_material` (`supplier_id`,`material_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `price_history`
--
ALTER TABLE `price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `price_history`
--
ALTER TABLE `price_history`
  ADD CONSTRAINT `price_history_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `price_history_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
