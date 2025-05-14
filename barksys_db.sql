-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 14, 2025 at 11:31 AM
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
-- Database: `barksys_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `full_name`, `contact_number`, `created_at`) VALUES
(1, 'marclloyd@gmail.com', 'try123', 'Xample1', '0912345678', '2025-03-08 07:09:27'),
(2, 'canefly', 'Asdf1234', 'SugaryCane02', '09602235528', '2025-04-23 08:35:58'),
(3, 'canefly@outlook.ph', 'Asdf1234', 'SugaryCane', '09602235527', '2025-04-23 08:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_price` decimal(10,2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `booking_time` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` date DEFAULT NULL,
  `status` enum('pending','completed','canceled','approved') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_number` varchar(20) DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `service_name`, `service_price`, `name`, `email`, `booking_time`, `created_at`, `date`, `status`, `payment_method`, `payment_number`, `balance`) VALUES
(14, 'asdad', 234.00, 'canefly', 'canefly@outlook.ph', '11:00 AM', '2025-05-04 10:13:38', '2025-05-04', 'completed', 'GCash', '09602235528', 175.50),
(15, 'asdad', 234.00, 'SugaryCane02', 'canefly@outlook.ph', '10:00 AM', '2025-05-04 10:22:52', '2025-05-04', 'completed', 'GCash', '09602235528', 175.50),
(16, 'asdad', 234.00, 'canefly', 'canefly@outlook.ph', '10:00 AM', '2025-05-04 15:33:51', '2025-05-01', 'canceled', 'GCash', '09000000000', 175.50),
(17, 'pussy grooming', 900.00, 'canefly', 'canefly@outlook.ph', '11:00 AM', '2025-05-14 05:51:22', '2025-05-14', 'approved', 'GCash', '09000000000', 675.00),
(18, 'posi posi', 899.00, 'canefly', 'canefly@outlook.ph', '10:00 AM', '2025-05-14 05:51:41', '2025-05-08', 'approved', 'GCash', '09000000000', 674.25);

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_name` varchar(100) NOT NULL,
  `pet_type` enum('Dog','Cat') NOT NULL,
  `breed` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `service_id`, `price`, `category_id`) VALUES
(7, 13, 213.00, 7),
(8, 13, 231.00, 6),
(9, 15, 453.00, 5);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text NOT NULL,
  `service_price` decimal(10,2) NOT NULL,
  `service_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `service_type` varchar(255) NOT NULL,
  `mode` enum('package','individual') NOT NULL DEFAULT 'individual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_description`, `service_price`, `service_image`, `created_at`, `service_type`, `mode`) VALUES
(12, 'asdad', 'asdarftedrft', 234.00, 'uploads/1746353585_95609911_p0_master1200.jpg', '2025-05-04 10:13:05', 'DogGrooming', 'individual'),
(13, 'Full Groom - Cat', 'asdada', 900.00, 'uploads/1746355249_95609911_p2.jpg', '2025-05-04 10:40:49', 'CatGrooming', 'individual'),
(15, 'pussy grooming', 'posay?', 900.00, 'uploads/1746375090_983bc2b504ff8cb23dbc44b2a877e918.jpg', '2025-05-04 16:11:30', 'CatGrooming', 'individual');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `contact`, `created_at`) VALUES
(1, 'padre@gmail.com', 'try123', 'padre', '0912345678', '2025-03-09 15:04:55'),
(2, 'marclloyd@gmail.com', 'try123', 'marc', '0912345678', '2025-03-09 15:22:36'),
(3, 'razoz@gmail.com', 'try123', 'razoz', '0912345678', '2025-03-09 16:51:39'),
(4, 'canefly@outlook.ph', 'Asdf1234', 'canefly', '09602235528', '2025-04-23 07:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `weight_categories`
--

CREATE TABLE `weight_categories` (
  `id` int(11) NOT NULL,
  `category_name` enum('Small','Medium','Large','XL') NOT NULL,
  `min_kg` decimal(5,2) NOT NULL,
  `max_kg` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weight_categories`
--

INSERT INTO `weight_categories` (`id`, `category_name`, `min_kg`, `max_kg`) VALUES
(5, 'Small', 0.00, 5.00),
(6, 'Medium', 9.00, 99.00),
(7, 'Large', 10.00, 30.00),
(10, 'XL', 34.00, 234.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`);

--
-- Indexes for table `weight_categories`
--
ALTER TABLE `weight_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `weight_categories`
--
ALTER TABLE `weight_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pricing`
--
ALTER TABLE `pricing`
  ADD CONSTRAINT `pricing_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pricing_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `weight_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
