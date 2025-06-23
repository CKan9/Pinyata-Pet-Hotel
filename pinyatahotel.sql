-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Jun 23, 2025 at 05:29 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pinyatahotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `full_name`, `email`, `password`) VALUES
(2, 'Admin User', 'admin@example.com', '$2y$10$fDaA2Q.6YUN8dwq/Ebg3IuhqDepfZ0pt8ycXLfw8pPn18WJzS7D/G'),
(3, 'admin2', 'admin2@example.com', '$2y$10$31MxGSaLLKdHij3nfIPFi.9tPIIZ0Q.sU58Puj91vtWvMleM2SvDC'),
(4, 'admin3', 'admin3@example.com', '$2y$10$p2ux7D3w6VFo8BQz4gHbEeO3Yr0zruzM6oP1WMlZV8MgU6WJBRwha'),
(5, 'Julie', 'julie3@example.com', '$2y$10$WqXXJtw/Z0eeIFP4IlzG3.gbvTytCTwTUPhkX9rH8R/MI6PuhPsHy'),
(6, 'Pinky', 'pinky5@example.com', '$2y$10$UOgPRwnvPq/GK1qih2BeUOg0zgnMqQVO3SZBh6.lMpXt/15pNjJVe'),
(7, 'DaJi', 'daji6@example.com', '$2y$10$0esJv60GfY6hRsg3gbrmtO1y8cfbuGH63MfD1RWCE/pKW1OAnCJo2');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `pet_id` int NOT NULL,
  `room_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`booking_id`),
  KEY `fk_bookings_users` (`user_id`),
  KEY `fk_bookings_pets` (`pet_id`),
  KEY `fk_bookings_rooms` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `pet_id`, `room_id`, `start_date`, `end_date`, `total_amount`, `booking_status`) VALUES
(38, 2, 16, 2, '2025-04-28', '2025-04-29', 100.00, 'Completed'),
(37, 2, 5, 1, '2025-04-27', '2025-04-28', 100.00, 'Completed'),
(43, 2, 5, 1, '2025-05-13', '2025-05-14', 100.00, 'Completed'),
(44, 2, 16, 2, '2025-05-14', '2025-05-15', 100.00, 'Completed'),
(45, 6, 21, 29, '2025-06-03', '2025-06-04', 90.00, 'Completed'),
(46, 3, 22, 25, '2025-06-05', '2025-06-06', 60.00, 'Completed'),
(47, 6, 23, 2, '2025-06-11', '2025-06-13', 200.00, 'Completed'),
(48, 7, 24, 15, '2025-06-13', '2025-06-16', 600.00, 'Completed'),
(49, 7, 24, 1, '2025-06-29', '2025-07-01', 200.00, 'Paid'),
(50, 13, 30, 29, '2025-06-20', '2025-06-22', 180.00, 'Paid'),
(51, 15, 34, 30, '2025-06-30', '2025-07-01', 90.00, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 'pinyatakliklaklikla@gmail.com', '6f20b78fc2a436a448ead751d63cd044', '2025-06-08 03:06:48', 0, '2025-06-08 02:51:48'),
(2, 'pinyatakliklaklikla@gmail.com', '7fb70e647e3455f28b6d744b95fd9eba', '2025-06-08 03:10:25', 0, '2025-06-08 02:55:25'),
(3, 'pinyatakliklaklikla@gmail.com', '54eb3cf21af7a45a1c3661dd0061b378', '2025-06-08 03:17:29', 0, '2025-06-08 03:02:29'),
(4, 'pinyatakliklaklikla@gmail.com', 'd4f38733cd06c34342a559ca9776560c', '2025-06-08 03:31:59', 0, '2025-06-08 03:16:59'),
(5, 'pinyatakliklaklikla@gmail.com', 'eb5ff4ecfbfd7efd7b8516639b86bae65fbacc2dac9dbd27e16f83d71e5c10a4', '2025-06-08 04:08:53', 0, '2025-06-08 03:53:53'),
(6, 'pinyatakliklaklikla@gmail.com', 'e2d8e2efe3972acfd1d0d849a1983950738299135b4d79c49f24275738203383', '2025-06-08 04:12:19', 0, '2025-06-08 03:57:19'),
(7, 'pinyatakliklaklikla@gmail.com', 'a98c19af4c5f8f06f00ca4def5d6f3933b75ff018546a57114d2f3a61b0bd2ec', '2025-06-08 04:21:58', 0, '2025-06-08 04:06:58'),
(8, 'pinyatakliklaklikla@gmail.com', '4cc1455a14e0791e9a3199ca4cbc7dc8b52b5dd1e2a9926ac4e90649e8a2533a', '2025-06-08 09:08:09', 0, '2025-06-08 08:53:09'),
(9, 'pinyatakliklaklikla@gmail.com', '06628ef7b6fce8435b4203becbba52d48b2672f71e3d0e34a426dab7edb4dcea', '2025-06-08 11:24:02', 1, '2025-06-08 11:09:02'),
(10, 'pinyatakliklaklikla@gmail.com', 'b80d2468f7b69a7110b0a15e169276d75c41c34f01a90ee22dc127ca2af5024e', '2025-06-08 13:05:03', 0, '2025-06-08 12:50:03'),
(11, 'pinyatakliklaklikla@gmail.com', '66534531643b14504dd2850be1b5257328ef0dbdc4b702f0f6ab97010cca7d93', '2025-06-08 13:32:16', 1, '2025-06-08 13:17:16'),
(12, 'pinyatakliklaklikla@gmail.com', '90c38530b8725782f96c7bd45e337aa9d32af4ca651630fd08b27b90e3d2a28b', '2025-06-08 13:45:13', 0, '2025-06-08 13:30:13'),
(13, 'pinyatakliklaklikla@gmail.com', 'c25f2b1de94a0814c75cc3c12334cb3637e3097e06e0f8211e46401a6535bc04', '2025-06-08 15:56:46', 0, '2025-06-08 15:41:46'),
(14, 'james@gmail.com', 'c2918b9d2c64f9de4a521fe456578c3aeda80038e63edeb432f6a95dd9bb463a', '2025-06-10 14:33:02', 1, '2025-06-10 14:18:02'),
(15, 'james@gmail.com', '8c7f44af08d948b91c826da70ecb0ad794bfc088204f2994219593ae7ee0f3b8', '2025-06-10 14:35:15', 1, '2025-06-10 14:20:15'),
(16, 'james@gmail.com', 'c2de2786cfc862bab2d4a9471f630692d3828095658529436100dbcda65751ef', '2025-06-10 15:44:06', 1, '2025-06-10 15:29:06'),
(17, 'james@gmail.com', '75ca6c7eefd8fdc0dbe9218947d69763e7c82107a01e32f6d63bc35dd49fbc33', '2025-06-10 16:30:01', 1, '2025-06-10 16:15:01'),
(18, 'james@gmail.com', '99e132f3a4e0235cf4fec68435a76cf7caac22e4caa8294b1e01aba51329df34', '2025-06-10 16:31:47', 1, '2025-06-10 16:16:47'),
(19, 'james@gmail.com', '5875c42229b6f80d8d1f37cd48f4715993eba3aa063d7b41a3de304b3b1ccae5', '2025-06-10 16:43:49', 1, '2025-06-10 16:28:49'),
(20, 'james@gmail.com', '6bf0902d3e540f470a17ed704981236aa57e87cd103a53be7bcfe8d0903c9559', '2025-06-10 16:56:40', 1, '2025-06-10 16:41:40'),
(21, 'james@gmail.com', '1599959a33f5e5cfeda1dec0da8a4f8952a1137d865d714b2ec35f1572caba83', '2025-06-10 16:57:55', 1, '2025-06-10 16:42:55'),
(22, 'Harry123@gmail.com', 'd3c737adc7bf0591ffed4c55fd71e5deb8c7a9f16a6ca906bf7255462efd4e6c', '2025-06-19 08:01:53', 1, '2025-06-19 07:46:53'),
(23, 'Henry303@gmail.com', '309a1deef4d9a644dca45e9a849789ca4707c9b0942c7db79b81a629de9f36cc', '2025-06-22 08:55:07', 1, '2025-06-22 08:40:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `user_id` int NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `payment_date`, `amount`, `payment_method`, `payment_status`, `user_id`) VALUES
(1, 37, '2025-04-26 05:14:14', 100.00, 'PayPal', 'Completed', 2),
(2, 38, '2025-04-26 08:11:07', 100.00, 'PayPal', 'Completed', 2),
(3, 43, '2025-05-12 08:42:08', 100.00, 'PayPal', 'Completed', 2),
(4, 0, '2025-05-12 09:47:56', 100.00, 'PayPal', 'Completed', 2),
(5, 0, '2025-06-02 00:25:47', 90.00, 'PayPal', 'Completed', 6),
(6, 0, '2025-06-04 06:41:14', 60.00, 'PayPal', 'Completed', 3),
(7, 0, '2025-06-09 11:23:54', 200.00, 'PayPal', 'Completed', 6),
(8, 0, '2025-06-10 00:37:25', 600.00, 'PayPal', 'Completed', 7),
(9, 0, '2025-06-11 02:02:07', 200.00, 'PayPal', 'Completed', 7),
(10, 0, '2025-06-18 23:45:59', 180.00, 'PayPal', 'Completed', 13),
(11, 0, '2025-06-22 00:39:12', 90.00, 'PayPal', 'Completed', 15);

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

DROP TABLE IF EXISTS `pets`;
CREATE TABLE IF NOT EXISTS `pets` (
  `pet_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `pet_name` varchar(50) NOT NULL,
  `pet_type` varchar(30) NOT NULL,
  `breed` varchar(50) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `picture_url` varchar(255) DEFAULT NULL,
  `special_notes` text,
  PRIMARY KEY (`pet_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`pet_id`, `user_id`, `pet_name`, `pet_type`, `breed`, `age`, `picture_url`, `special_notes`) VALUES
(5, 2, 'Xiao huang', 'Dog', 'Golden Hound', 4, '../IMG/uploads/67f9496aee981_OIP (1).jpeg', 'can not eat mango'),
(13, 2, 'Hua Hua', 'Cat', 'Ragdoll', 3, '../IMG/uploads/67fa072be729e_OIP (2).jpeg', 'yahaha'),
(16, 2, 'Mi Mi', 'Cat', 'golden british shorthair', 2, '../IMG/uploads/MiMi.jpg', 'scare dark'),
(17, 2, '娜诺', 'Dog', 'Golden Hound', 3, '../IMG/uploads/682e0418e0f52_MiMi.jpg', '666'),
(18, 3, 'qiuwey', 'Bird', 'sparrow', 2, '../IMG/uploads/restaurant.png', 'haigongniu'),
(21, 6, 'nono', 'Reptile', 'corn snake', 4, '../uploads/../IMG/uploads/nono.jpg', 'this is a cute corn snake'),
(22, 3, 'BoBo', 'Hamster', 'goldern hamster', 1, '../IMG/uploads/syrische-hamster-goudhamster.png', ''),
(23, 6, 'Luna', 'Cat', 'Three-colored cat', 2, '../IMG/uploads/pet_6847205d4847a3.76757265.jpg', 'cannot eat chicken'),
(24, 7, 'Amilia', 'Dog', 'corgi', 2, '../uploads/../IMG/uploads/amilia.jpg', 'can not drink any milk'),
(30, 13, 'Roan', 'Reptile', 'Dwarf Crocodile', 5, '../IMG/uploads/pet_6853bfeea48bd9.73357519.jpeg', 'He cannot eat beef'),
(34, 15, 'bilibili', 'Reptile', 'Dwarf Crocodile', 6, '../IMG/uploads/pet_6857c0c765a1d8.83382309.jpg', 'This is a cute crocodile');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `room_type` varchar(100) NOT NULL,
  `description` text,
  `capacity` int NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT '1',
  `pet_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_type`, `description`, `capacity`, `price_per_day`, `availability`, `pet_type`) VALUES
(1, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Dog'),
(2, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Cat'),
(3, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Dog'),
(4, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Cat'),
(5, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Dog'),
(6, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Cat'),
(7, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Dog'),
(8, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Cat'),
(9, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Dog'),
(10, 'Standard Suite', 'Comfortable suite for dogs and cats', 1, 100.00, 1, 'Cat'),
(11, 'Deluxe Suite', 'Luxury suite for dogs and cats', 2, 200.00, 1, 'Dog'),
(12, 'Deluxe Suite', 'Luxury suite for dogs and cats', 2, 200.00, 1, 'Cat'),
(13, 'Deluxe Suite', 'Luxury suite for dogs and cats', 2, 200.00, 1, 'Dog'),
(14, 'Deluxe Suite', 'Luxury suite for dogs and cats', 2, 200.00, 1, 'Cat'),
(15, 'Deluxe Suite', 'Luxury suite for dogs and cats', 2, 200.00, 1, 'Dog'),
(16, 'Deluxe Suite', 'Luxury suite for dogs and cats', 2, 200.00, 1, 'Cat'),
(17, 'Bird Room', 'Specialized space for birds', 1, 80.00, 1, 'Bird'),
(18, 'Bird Room', 'Specialized space for birds', 1, 80.00, 1, 'Bird'),
(19, 'Bird Room', 'Specialized space for birds', 1, 80.00, 1, 'Bird'),
(20, 'Bird Room', 'Specialized space for birds', 1, 80.00, 1, 'Bird'),
(21, 'Rabbit Room', 'Cozy space for rabbits', 1, 75.00, 1, 'Rabbit'),
(22, 'Rabbit Room', 'Cozy space for rabbits', 1, 75.00, 1, 'Rabbit'),
(23, 'Rabbit Room', 'Cozy space for rabbits', 1, 75.00, 1, 'Rabbit'),
(24, 'Rabbit Room', 'Cozy space for rabbits', 1, 75.00, 1, 'Rabbit'),
(25, 'Hamster Room', 'Safe environment for hamsters', 1, 60.00, 1, 'Hamster'),
(26, 'Hamster Room', 'Safe environment for hamsters', 1, 60.00, 1, 'Hamster'),
(27, 'Hamster Room', 'Safe environment for hamsters', 1, 60.00, 1, 'Hamster'),
(28, 'Hamster Room', 'Safe environment for hamsters', 1, 60.00, 1, 'Hamster'),
(29, 'Reptile Room', 'Climate-controlled space for reptiles', 1, 90.00, 1, 'Reptile'),
(30, 'Reptile Room', 'Climate-controlled space for reptiles', 1, 90.00, 1, 'Reptile'),
(31, 'Reptile Room', 'Climate-controlled space for reptiles', 1, 90.00, 1, 'Reptile'),
(32, 'Reptile Room', 'Climate-controlled space for reptiles', 1, 90.00, 1, 'Reptile');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `phone`) VALUES
(1, 'testuserss', 'test@example.com', '$2y$10$1iRJsvRkG7ZSNcokNLHxHO4EHzIPSuctvRGsJA76v2YuofnWsYzku', '1234567890'),
(2, 'root', 'PPTan@gmail.com', '$2y$10$AzPmOygeHB.COAXDVI9eZOko8m6bs.gBOKj2EiZE7zTBWzQPzfRUi', '1234567890'),
(3, 'Pinyata', 'pinyatakliklaklikla@gmail.com', '$2y$10$xn6BYAUR6.ticO63VDKzBuNbS7nlXOrRJ/rYhsOCtn5s2RCW30HQG', '0186665544'),
(6, 'user1', 'user1@gmail.com', '$2y$10$pNOYQd/loXihmSqHUTPdRePCbpFJGC4FixNAePP6ckrxbH3BUTEsW', '0187776459'),
(7, 'James', 'james@gmail.com', '$2y$10$Ew.8SvIC3PBFxHu5BpeCKu0bH.IBqVO8A1FuFYVrKVDrf.SlywTjS', '0119877789'),
(13, 'Harry', 'Harry123@gmail.com', '$2y$10$raEcUvwCHuEHpCZGH1cyLOOjxnQYOvmW8YddJr4dwvCK7KzKJQbJ2', '0183342377'),
(15, 'Henry', 'Henry303@gmail.com', '$2y$10$956BSZTvRtzd5DtRxhizb.ndEwyHpBVxSGEQkuJXTXWmMDfS5wa7O', '0187690563');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
