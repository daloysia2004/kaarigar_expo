-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2026 at 09:06 AM
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
-- Database: `kaarigar_expo`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kaarigar_profiles`
--

CREATE TABLE `kaarigar_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `craft_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kaarigar_profiles`
--

INSERT INTO `kaarigar_profiles` (`id`, `user_id`, `craft_type`, `description`, `photo`) VALUES
(1, 3, 'music', 'through the souls', ''),
(2, 4, 'music', 'through the souls', ''),
(3, 6, 'pottery', 'I don\'t just force the earth into a shape; I feel its rhythm on the wheel, working with patience and steady hands to turn raw, messy mud into something beautiful, balanced, and useful. My hands would be constantly stained with slip, my apron dusted with dry clay, and my heart fulfilled by the quiet magic of turning a simple lump of earth into a vessel that holds life, warmth, and art.', 'uploads/1789147039_20210712_130508.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `melas`
--

CREATE TABLE `melas` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `location` varchar(200) NOT NULL,
  `event_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `melas`
--

INSERT INTO `melas` (`id`, `title`, `location`, `event_date`, `description`, `image_url`, `created_by`, `created_at`) VALUES
(1, 'Utsav', 'goa', '2026-09-18', '🎉 Step into a World of Joy, Culture, and Celebration! 🎉Get ready to experience the ultimate celebration of tradition and togetherness at Utsav Mela! This year, we are bringing together the very best of local culture, creativity, and community under one vibrant roof.', 'uploads/1789145449_nature.png', NULL, '2026-09-11 16:50:49'),
(2, 'utsav', 'goa', '2026-09-19', '🎉 Step into a World of Joy, Culture, and Celebration! 🎉Get ready to experience the ultimate celebration of tradition and togetherness at Utsav Mela! This year, we are bringing together the very best of local culture, creativity, and community under one vibrant roof.', 'uploads/1789145488_download1.jpg', NULL, '2026-09-11 16:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `mela_applications`
--

CREATE TABLE `mela_applications` (
  `id` int(11) NOT NULL,
  `mela_id` int(11) NOT NULL,
  `kaarigar_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mela_applications`
--

INSERT INTO `mela_applications` (`id`, `mela_id`, `kaarigar_id`, `status`, `applied_at`) VALUES
(1, 1, 4, 'pending', '2026-09-11 16:52:20'),
(2, 1, 6, 'approved', '2026-09-11 17:18:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kaarigar','visitor') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'aloysia', 'aloysia@gmail.com', '$2y$10$51Ie0qCr5jc9NkFto8x3D.8M1wzkZR7e6WehO9jqOQ17cHbD.I./6', 'admin', '2026-09-10 11:06:18'),
(3, 'aloysiadsouza', 'aloy@gmail.com', '$2y$10$vgo0NHVbscWUv00Dp0odleyiljoh55PK1SABDIkHWXhUtTlxt/SMy', 'kaarigar', '2026-09-10 11:43:11'),
(4, 'aloysiadsouza', 'aloysiadsouza@gmail.com', '$2y$10$TlXYDk9fPaErN77.5aWPt.ec75RxzUdF1WteeiaBqV7CRxt3gljXe', 'kaarigar', '2026-09-11 16:16:47'),
(5, 'aloysiad', 'aloysiad@gmail.com', '$2y$10$ltR5eiOwD3j88N03GnkkMeOfZBIw/U9J4QfpfIlhHgOjYnPOvtWQO', 'visitor', '2026-09-11 16:56:30'),
(6, 'Jacky D\'souza', 'jacky@gmail.com', '$2y$10$Pg8qbk1ppd3fTzCzE/uJ2enDC7.gaqjbrjDmTcskjBKMexVYQIFXC', 'kaarigar', '2026-09-11 17:17:19');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_rsvps`
--

CREATE TABLE `visitor_rsvps` (
  `id` int(11) NOT NULL,
  `mela_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kaarigar_profiles`
--
ALTER TABLE `kaarigar_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `melas`
--
ALTER TABLE `melas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `mela_applications`
--
ALTER TABLE `mela_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`mela_id`,`kaarigar_id`),
  ADD KEY `kaarigar_id` (`kaarigar_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitor_rsvps`
--
ALTER TABLE `visitor_rsvps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rsvp` (`mela_id`,`visitor_id`),
  ADD KEY `visitor_id` (`visitor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kaarigar_profiles`
--
ALTER TABLE `kaarigar_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `melas`
--
ALTER TABLE `melas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mela_applications`
--
ALTER TABLE `mela_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visitor_rsvps`
--
ALTER TABLE `visitor_rsvps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kaarigar_profiles`
--
ALTER TABLE `kaarigar_profiles`
  ADD CONSTRAINT `kaarigar_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `melas`
--
ALTER TABLE `melas`
  ADD CONSTRAINT `melas_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mela_applications`
--
ALTER TABLE `mela_applications`
  ADD CONSTRAINT `mela_applications_ibfk_1` FOREIGN KEY (`mela_id`) REFERENCES `melas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mela_applications_ibfk_2` FOREIGN KEY (`kaarigar_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visitor_rsvps`
--
ALTER TABLE `visitor_rsvps`
  ADD CONSTRAINT `visitor_rsvps_ibfk_1` FOREIGN KEY (`mela_id`) REFERENCES `melas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_rsvps_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
