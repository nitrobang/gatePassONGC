-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2023 at 10:57 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gate_pass_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `cpfno` int(11) NOT NULL,
  `empname` varchar(100) NOT NULL,
  `designation` enum('E','S') NOT NULL,
  `department` enum('I','M','P','') DEFAULT NULL,
  `venue` enum('N','V','H') NOT NULL,
  `signatory` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`cpfno`, `empname`, `designation`, `department`, `venue`, `signatory`) VALUES
(12344, 'Vishvesh', 'E', 'I', 'H', 0),
(12345, 'Arvind', 'E', 'I', 'N', 0),
(12346, 'Chirag', 'S', NULL, 'N', 0),
(12347, 'Kartik', 'S', NULL, 'H', 0),
(12348, 'Ayan', 'E', 'I', 'V', 0),
(12349, 'Cousins', 'S', NULL, 'V', 0),
(123444, 'PROD 11H', 'E', 'P', 'H', 0),
(123445, 'MANAGE 11H', 'E', 'M', 'H', 0),
(123455, 'MANAGE NBP', 'E', 'M', 'N', 0),
(123456, 'PROD NBP', 'E', 'P', 'N', 0),
(123488, 'PROD VB', 'E', 'P', 'V', 0),
(123489, 'MANAGE VB', 'E', 'M', 'V', 0),
(1234444, 'Info 11H', 'E', 'I', 'H', 1),
(1234445, 'Info 11H 2', 'E', 'I', 'H', 1),
(1234555, 'Info NBP', 'E', 'I', 'N', 1),
(1234556, 'Info NBP2', 'E', 'I', 'N', 1),
(1234888, 'Info VB', 'E', 'I', 'V', 1),
(1234889, 'Info VB2', 'E', 'I', 'V', 1);

--
-- Triggers `employee`
--
DELIMITER $$
CREATE TRIGGER `set_department_null` BEFORE INSERT ON `employee` FOR EACH ROW BEGIN
    IF NEW.designation = 'S' THEN
        SET NEW.Department = NULL;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `descrip` varchar(1000) NOT NULL,
  `nop` int(11) NOT NULL,
  `deliverynote` varchar(1000) DEFAULT NULL,
  `remark` varchar(1000) DEFAULT NULL,
  `orderno` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`descrip`, `nop`, `deliverynote`, `remark`, `orderno`, `created_at`) VALUES
('qew', 1, 'asds', 'asd', 20230710001, '2023-07-10 06:32:53'),
('asd', 2, 'saad', 'asds', 20230710002, '2023-07-10 07:57:53'),
('asd', 2, 'asd', 'sd', 20230710003, '2023-07-10 08:26:23'),
('zx', 2, 'asd', 'asd', 20230710004, '2023-07-10 08:50:32'),
('asd', 3, 'sad', 'asds', 20230710005, '2023-07-10 08:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `order_no`
--

CREATE TABLE `order_no` (
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `orderno` bigint(20) NOT NULL,
  `order_dest` varchar(100) NOT NULL,
  `issue_dep` enum('I','M','P','') NOT NULL,
  `placeoi` enum('N','V','H') NOT NULL,
  `issueto` varchar(150) NOT NULL,
  `securityn` varchar(300) DEFAULT NULL,
  `guard_name` varchar(300) DEFAULT NULL,
  `collector_name` varchar(300) DEFAULT NULL,
  `returnable` tinyint(1) NOT NULL,
  `returndate` date DEFAULT NULL,
  `coll_approval` tinyint(1) NOT NULL DEFAULT 0,
  `sign_approval` tinyint(1) NOT NULL DEFAULT 0,
  `guard_approval` tinyint(1) NOT NULL DEFAULT 0,
  `security_approval` tinyint(1) NOT NULL DEFAULT 0,
  `comp_approval` tinyint(1) NOT NULL DEFAULT 0,
  `forwarded_to` int(11) NOT NULL,
  `signatory` int(30) NOT NULL,
  `new_remarks` varchar(255) DEFAULT NULL,
  `moc` varchar(100) DEFAULT NULL,
  `vehno` varchar(10) DEFAULT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_no`
--

INSERT INTO `order_no` (`created_at`, `orderno`, `order_dest`, `issue_dep`, `placeoi`, `issueto`, `securityn`, `guard_name`, `collector_name`, `returnable`, `returndate`, `coll_approval`, `sign_approval`, `guard_approval`, `security_approval`, `comp_approval`, `forwarded_to`, `signatory`, `new_remarks`, `moc`, `vehno`, `created_by`) VALUES
('2023-07-10 06:32:53', 20230710001, 'NBP Green Heights', 'I', 'N', 'asd', '12346', '8754000541', '', 1, '0000-00-00', 1, 1, 1, 1, 3, 12345, 1234555, '', 'Self', '', 12345),
('2023-07-10 07:57:53', 20230710002, 'NBP Green Heights', 'I', 'N', 'ds', '', NULL, NULL, 1, '0000-00-00', -1, 0, 0, 0, 0, 12345, 1234555, 'dsaa', NULL, NULL, 12345),
('2023-07-10 08:26:23', 20230710003, 'NBP Green Heights', 'I', 'N', 'asd', '', NULL, NULL, 1, '0000-00-00', 0, 0, 0, 0, 0, 12345, 1234555, NULL, NULL, NULL, 12345),
('2023-07-10 08:50:32', 20230710004, 'NBP Green Heights', 'I', 'N', 'asd', '', NULL, NULL, 1, '0000-00-00', 0, 0, 0, 0, 0, 12345, 1234555, NULL, NULL, NULL, 12345),
('2023-07-10 08:53:41', 20230710005, 'NBP Green Heights', 'I', 'N', 'asd', '', NULL, NULL, 1, '2023-07-12', 0, 0, 0, 0, 0, 12345, 1234555, NULL, NULL, NULL, 12345);

-- --------------------------------------------------------

--
-- Table structure for table `security_guard`
--

CREATE TABLE `security_guard` (
  `id` int(11) NOT NULL,
  `guard_name` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(10) NOT NULL,
  `venue` enum('N','V','H') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_guard`
--

INSERT INTO `security_guard` (`id`, `guard_name`, `password`, `phone_no`, `venue`, `created_at`) VALUES
(1, 'Guard', '$2y$10$shBBOFqr3mh28IwjetrEJ.rV4tMjQLNhS65xdvAVtIoOn.9f6J0LG', '8754000541', 'N', '2023-06-22 08:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `cpfno` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `cpfno`, `username`, `password`, `email`, `created_at`) VALUES
(1, 12345, 'Arvind', '$2y$10$2.F3qi9waQ5Y9o03SmXF7OQ/mN2lGDNeR89pBcBiKwOr8qNDUnPB2', 'arvindk210902@gmail.com', '2023-06-22 05:20:38'),
(2, 12344, 'Vishvesh', '$2y$10$A/B2O8nb6RaK3atB953SeulJY5Q/w5CfS9sJVHfQzNYPdWG9twCsy', 'vm@gmail.com', '2023-06-22 05:21:36'),
(3, 12348, 'Ayan', '$2y$10$3/EOHJr33pWfGwtt.i6Q5e80UZq3srOAAWNHLg1BIvV7lzYVJpohG', 'ag@gmail.com', '2023-06-22 05:21:57'),
(4, 12347, 'Kartik', '$2y$10$1RilCRS0M6oSTR/ih6iDnODhzGGZ6NLTd864JgIyxhB.rZnyzQc.a', 'kg@gmail.com', '2023-06-22 05:22:17'),
(5, 12346, 'Chirag', '$2y$10$kegYN7AjzEWhllsmkO8BC.D2ytuFvV.saYXOOl6K3mC3B79fHcH3y', 'cs@gmail.com', '2023-06-22 05:22:40'),
(6, 12349, 'Cousins', '$2y$10$qMYCbZWRCYBF8GLxdpjHxOZID4GLI5GsnPRD4cNU87QvC9w3vi3qa', 'cousins@gmail.com', '2023-06-22 05:23:47'),
(7, 123455, 'MANAGE NBP', '$2y$10$cGsGUtytU8oY9MeOCYk6Ye60IhOEzfJu.3U3.FB5V8Jh2FY5wfc46', 'ex@gmail.com', '2023-07-08 11:36:02'),
(8, 123489, 'MANAGE VB', '$2y$10$zV/Li.T5UwmmbIw/i/Bone.wzkIoypFYFhXJAWkHhwu25xFVgrse.', 'ex1@gmail.com', '2023-07-08 11:39:01'),
(9, 123488, 'PROD VB', '$2y$10$2S/2XJj4B1iMTfvj6YZJqOxLNv2u4sjPoDMHVRyWeggLTy2dH4YnS', 'ex3@gmail.com', '2023-07-08 11:39:18'),
(10, 123456, 'PROD NBP', '$2y$10$p2QTKcCwF4gY9Zm0awNlL.td8eMJBczQ9o0IsB8Ee.Booi9tQFW0i', 'ex2@gmail.com', '2023-07-08 11:40:02'),
(11, 123445, 'MANAGE 11H', '$2y$10$WUW.FYmXl.gnWsrjs7SS5uRrzDKYm/9XlCzr5j5eZu9FN7sQmLeSS', 'ex4@gmail.com', '2023-07-08 11:40:31'),
(12, 123444, 'PROD 11H', '$2y$10$eGMyxoXGcsoTr9CsjZxG4eztU5duBICuCr8qVlinAr7TmkMRciFE2', 'ex5@gmail.com', '2023-07-08 11:41:21'),
(13, 1234555, 'Info NBP', '$2y$10$WGx5dxHApB2T/EJe75tToea/QQhgEaDiGKXD4pDLARoEdPxFxVx0m', 'ex6@gmail.com', '2023-07-08 11:52:35'),
(17, 1234556, 'Info NBP2', '$2y$10$gqIGpzgHECnSwDZXfMMZEOFODbCvJ0Yl1BVcZQfB9919OvK9EKJmi', 'ex7@gmail.com', '2023-07-08 11:57:18'),
(18, 1234444, 'Info 11H', '$2y$10$mZf2Hnb2IaU2PgtXpCXNFewcZ7Q8Z9FHOXKZpv5vXn77VNiv7VxoC', 'ex8@gmail.com', '2023-07-08 11:58:08'),
(19, 1234445, 'Info 11H 2', '$2y$10$OBmbsrLbfe/EZFPoSQ.Yw.S8pYG0M6wynPUEy2VhLeUAYeF/LXjJW', 'ex9@gmail.com', '2023-07-08 11:59:39'),
(20, 1234888, 'Info VB', '$2y$10$YF2hR7wtGJ0h7yUVGLnCnO/RhRIKKKH9TV9cF69VEb/LNcj8jFnwW', 'ex10@gmail.com', '2023-07-08 12:00:13'),
(21, 1234889, 'Info VB 2', '$2y$10$cQkDWjCLpxDFoUnN7V2sleoh8CK3UmSKEVXHfeVTFsdQqSemsgjCC', 'ex11@gmail.com', '2023-07-08 12:00:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`cpfno`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD KEY `fk_order_no` (`orderno`);

--
-- Indexes for table `order_no`
--
ALTER TABLE `order_no`
  ADD PRIMARY KEY (`orderno`);

--
-- Indexes for table `security_guard`
--
ALTER TABLE `security_guard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee_cpfno` (`cpfno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `security_guard`
--
ALTER TABLE `security_guard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_no` FOREIGN KEY (`orderno`) REFERENCES `order_no` (`orderno`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_employee_cpfno` FOREIGN KEY (`cpfno`) REFERENCES `employee` (`cpfno`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
