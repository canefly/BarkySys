-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 09:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
(4, 'canefly@outlook.ph', '$2y$10$2ooIdtiJ1mS6Xp50Px79s.uS4oAsvtOxooNpczp1Spmn4S0LRBQcq', 'canefly', '09602235528', '2025-05-20 18:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `age_categories`
--

CREATE TABLE `age_categories` (
  `id` int(11) NOT NULL,
  `species` enum('Dog','Cat') NOT NULL,
  `label` varchar(20) DEFAULT NULL,
  `min_months` int(11) NOT NULL,
  `max_months` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `age_categories`
--

INSERT INTO `age_categories` (`id`, `species`, `label`, `min_months`, `max_months`) VALUES
(1, 'Cat', 'Kitten', 0, 11),
(2, 'Cat', 'Adult', 11, 50);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_description` text DEFAULT NULL,
  `table_affected` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_type` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action_type`, `action_description`, `table_affected`, `record_id`, `created_at`, `role_type`) VALUES
(5, 4, 'logout', 'Admin logged out', 'admin', NULL, '2025-05-20 19:11:42', 'admin'),
(6, 4, 'login', 'Admin logged in', 'admin', NULL, '2025-05-20 19:11:46', 'admin'),
(7, 4, 'update_weight', 'Updated weight category ID #10', 'weight_categories', 10, '2025-05-20 19:26:16', 'admin'),
(8, 4, 'update_age', 'Updated age category ID #2', 'age_categories', 2, '2025-05-20 19:27:07', 'admin');

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
(19, 'Full Groom - Dog', 0.00, 'canefly', 'canefly@outlook.ph', '3:00 PM', '2025-05-20 05:32:55', '2025-05-20', 'completed', 'GCash', '09304426922', 0.00),
(20, 'Face Trim', 0.00, 'canefly', 'canefly@outlook.ph', '2:00 PM', '2025-05-20 05:52:45', '2025-05-17', 'completed', 'Paymaya', '09304426922', 0.00),
(21, 'Full Groom - Cat', 0.00, 'canefly', 'canefly@outlook.ph', '11:00 AM', '2025-05-20 06:05:48', '2025-05-20', 'canceled', 'GCash', '09304426922', 0.00),
(22, 'Sanitary Trim', 0.00, 'canefly', 'canefly@outlook.ph', '3:00 PM', '2025-05-20 07:34:15', '2025-05-20', 'completed', 'GCash', '09304426922', 0.00),
(23, 'Full Groom - Cat', 0.00, 'canefly', 'canefly@outlook.ph', '3:00 PM', '2025-05-20 07:36:48', '2025-05-20', 'canceled', 'GCash', '09304426922', 0.00),
(24, 'Face Trim', 0.00, 'canefly', 'canefly@outlook.ph', '2:00 PM', '2025-05-20 08:04:19', '2025-05-20', 'approved', 'GCash', '09304426922', 0.00);

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

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `user_id`, `pet_name`, `pet_type`, `breed`, `age`, `weight`, `created_at`) VALUES
(2, 4, 'Brownie', 'Dog', 'labrador retriever', 3, 9.00, '2025-05-20 05:34:55'),
(3, 4, 'Fendi', 'Dog', 'Maltis', 4, 6.50, '2025-05-20 07:35:40');

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
(14, 18, 20.00, 6),
(15, 19, 250.00, 7);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 999,
  `service_name` varchar(255) NOT NULL,
  `service_description` text NOT NULL,
  `service_price` decimal(10,2) DEFAULT NULL,
  `service_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `service_type` varchar(255) NOT NULL,
  `mode` enum('package','individual') NOT NULL DEFAULT 'individual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `display_order`, `service_name`, `service_description`, `service_price`, `service_image`, `created_at`, `service_type`, `mode`) VALUES
(17, 1, 'Full Groom - Cat', 'Full Body Hair-Cut, Warm Bath & Blow Dry, Sanitary Trim, Face Trim, Nail Clipping, Ear Cleaning, Pet Powder / cologne', NULL, 'uploads/1747714856_cat_comb.png', '2025-05-20 04:20:56', 'CatGrooming', 'package'),
(18, 3, 'Full Groom - Dog', 'Executive Full Body Hair-Cut, Warm Bath & Blow Dry,Sanitary Trim, Paw Hair Trim, Nail Clipping, Ear Cleaning, Teeth Brushing, Pet Powder/ Cologne', NULL, 'uploads/1747715047_dog_comb.png', '2025-05-20 04:24:07', 'DogGrooming', 'package'),
(19, 4, 'Warm Bath & Blow Dry', 'A gentle cleanse with warm water and pet-safe shampoo, finished with a soft blow dry for a fresh, fluffy, and pampered coat.', NULL, 'uploads/1747715567_Bath.png', '2025-05-20 04:32:47', 'DogGrooming', 'package'),
(20, 5, 'Face Trim', 'Precise grooming around the eyes, muzzle, and ears to keep your pet’s face clean, neat, and comfortably tidy without removing too much fluff.', NULL, 'uploads/1747715828_dog_face_trim.png', '2025-05-20 04:37:08', 'DogGrooming', 'package'),
(21, 6, 'Sanitary Trim', 'A gentle, hygienic trim around the rear and private areas to keep your pet clean, fresh, and comfortable. Helps prevent matting, odor, and unwanted mess.', NULL, 'uploads/1747716677_dog_sanitary_trim.png', '2025-05-20 04:51:17', 'DogGrooming', 'package'),
(24, 7, 'Paw Hair Trim', 'Neatly trims excess fur around the paw pads to prevent slipping, reduce dirt buildup, and keep your pet’s steps soft, clean, and comfy. 🐾✂️', NULL, 'uploads/1747737466_ChatGPT_Image_May_20,_2025,_06_36_49_PM.png', '2025-05-20 10:37:46', 'DogGrooming', 'package'),
(25, 8, 'Nail Clipping', 'A quick, stress-free trim using pet-safe clippers to keep nails short, smooth, and healthy—preventing discomfort, splitting, and scratching', NULL, 'uploads/1747738100_Nail_clipping.png', '2025-05-20 10:48:20', 'DogGrooming', 'package'),
(26, 10, 'Ear Cleaning', 'A gentle cleanse of your pet’s ears using safe, vet-approved solutions to remove wax, dirt, and buildup—keeping their furry ears fresh, healthy, and irritation-free. 🧼🐾', NULL, 'uploads/1747738420_Ear_Cleaning.png', '2025-05-20 10:53:40', 'DogGrooming', 'package'),
(27, 9, 'Anal Sac Expression', 'A gentle, hygienic procedure that relieves pressure by expressing your pet’s anal glands—keeping them clean, odor-free, and wagging with comfort.', NULL, 'uploads/1747739025_butt_cheeks_clean.png', '2025-05-20 11:03:45', 'DogGrooming', 'package'),
(30, 2, 'dadadad', 'grhrh', NULL, 'uploads/1747747934_gorou.png', '2025-05-20 13:32:14', 'CatGrooming', 'individual');

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
(4, 'canefly@outlook.ph', 'Asdf1234', 'canefly', '09602235528', '2025-04-23 07:45:00'),
(5, 'michelleli@gmail.com', '', 'Mitch Li', '09304466992', '2025-05-18 12:36:22');

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
(6, 'Medium', 5.00, 9.00),
(7, 'Large', 9.00, 15.00),
(10, 'XL', 15.00, 90.00);

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
-- Indexes for table `age_categories`
--
ALTER TABLE `age_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `age_categories`
--
ALTER TABLE `age_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `weight_categories`
--
ALTER TABLE `weight_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
