-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2026 at 03:57 PM
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
-- Database: `soe`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `event_id`, `admin_id`, `title`, `message`, `created_at`) VALUES
(1, 30, 18, 'nak start sudah', 'jangan lupa bawa bahan', '2026-01-14 09:37:43');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `registration_close_date` date DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mode` enum('Physical','Online','Hybrid') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `poster_path` varchar(100) DEFAULT NULL,
  `poster2_path` varchar(255) DEFAULT NULL,
  `poster3_path` varchar(255) DEFAULT NULL,
  `poster4_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `max_participants` int(11) DEFAULT 0,
  `visibility` enum('Public','University') DEFAULT 'Public',
  `admin_status` enum('Pending','Approved','Archived') DEFAULT 'Pending',
  `is_highlighted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `description`, `category_id`, `venue`, `event_date`, `event_time`, `registration_close_date`, `contact_person`, `contact_number`, `fee`, `mode`, `remarks`, `poster_path`, `poster2_path`, `poster3_path`, `poster4_path`, `created_by`, `created_at`, `max_participants`, `visibility`, `admin_status`, `is_highlighted`) VALUES
(30, 'masak masak', 'masak ikan dan ayam sesuka kamu', 4, 'ua 1', '2026-01-16', NULL, NULL, NULL, NULL, 0.00, 'Physical', 'bawa bahan masakan sendiri', '1768312681_Screenshot 2025-12-12 203423.png', NULL, NULL, NULL, 19, '2026-01-13 13:58:01', 0, 'Public', 'Pending', 0),
(31, 'makan ikan', 'wertyui', 2, 'rumah saya', '2026-01-16', NULL, NULL, NULL, NULL, 0.00, 'Hybrid', 'qwerty', '1768315477_Screenshot 2025-11-12 150810.png', NULL, NULL, NULL, 19, '2026-01-13 14:44:37', 3, 'Public', 'Approved', 0),
(32, 'Lukisan', 'wert', 6, 'Fakulti Seni', '2026-01-14', NULL, NULL, NULL, NULL, 0.00, 'Physical', 'ergh', '1768316565_Screenshot 2025-11-16 213837.png', NULL, NULL, NULL, 19, '2026-01-13 15:02:45', 5, 'University', 'Pending', 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_category`
--

CREATE TABLE `event_category` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `categoryName` varchar(100) NOT NULL,
  `categoryDesc` varchar(100) NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_category`
--

INSERT INTO `event_category` (`category_id`, `categoryName`, `categoryDesc`, `createDate`) VALUES
(1, 'Workshop', 'F2F/Online course or hands-on as workshop', '2025-10-14 23:08:40'),
(2, 'Seminar', 'F2F/Online seminar, presentation, etc.', '2025-10-14 23:09:33'),
(3, 'Competition', 'Competition event', '2025-10-14 23:09:33'),
(4, 'Festival', 'All festival event', '2025-10-14 23:09:33'),
(5, 'Sport', 'All types of sport events', '2025-10-14 23:09:33'),
(6, 'Course', 'All types of educational course events', '2025-10-14 23:09:33');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `event_id`, `user_id`, `rating`, `comments`, `submitted_at`) VALUES
(1, 30, 20, 3, 'good sikit sikiy', '2026-01-14 01:49:45'),
(2, 31, 20, 4, 'kontlo', '2026-01-15 21:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 20, 'Your registration for the event \'masak masak\' has been Approved.', 1, '2026-01-13 23:25:23'),
(2, 19, 'New feedback received for your event: ', 1, '2026-01-14 01:49:45'),
(3, 20, 'Attendance has been marked as Absent for event \'masak masak\'.', 0, '2026-01-15 21:10:18'),
(4, 20, 'Attendance has been marked as Present for event \'masak masak\'.', 0, '2026-01-15 21:10:19'),
(5, 21, 'Your registration for the event \'masak masak\' has been Approved.', 0, '2026-01-15 21:10:25'),
(6, 21, 'Attendance has been marked as Present for event \'masak masak\'.', 0, '2026-01-15 21:10:29'),
(7, 20, 'Your registration for the event \'makan ikan\' has been Approved.', 0, '2026-01-15 21:11:43'),
(8, 20, 'Attendance has been marked as Present for event \'makan ikan\'.', 0, '2026-01-15 21:11:45'),
(9, 19, 'New feedback received for your event: makan ikan', 0, '2026-01-15 21:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `attendance` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `event_id`, `user_id`, `registration_date`, `status`, `attendance`) VALUES
(1, 30, 20, '2026-01-13 21:59:20', 'Approved', 1),
(2, 31, 20, '2026-01-13 23:47:44', 'Approved', 1),
(7, 30, 21, '2026-01-14 16:46:09', 'Approved', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `category` enum('Staff','Student','Public') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `organization` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff','Student','Public') DEFAULT 'Public',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Suspended') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `category`, `name`, `email`, `phone`, `organization`, `password`, `role`, `created_at`, `status`) VALUES
(18, 'Staff', 'admin', 'admin@gmail.com', '1237658798', 'ums', '$2y$10$/LvBI6fLOdDROp..1.fKx.a0YObdTS9jsLOmV3A6VjvuwG0kC3ysm', 'Admin', '2026-01-11 18:04:06', 'Active'),
(19, 'Student', 'Maxmarrio Maxlev', 'maxmarriomaxlev@gmail.com', '+601131820544', 'ums', '$2y$10$8vsmUPyaCGJZffYDqi67ve0JFBQizKDO1chCMpepo1R7.SCGL3paS', '', '2026-01-13 13:56:16', 'Active'),
(20, 'Student', 'Maxzuko', 'maxzuko@gmail.com', '1234567899', 'ums', '$2y$10$CHK0G7p40fAiT8axZwev5OfcjltoeejXKjjisKcJ9ANO3c9H2HHlK', '', '2026-01-13 13:58:57', 'Active'),
(21, 'Student', 'Rulz', 'rulz@gmail.com', '4656757', 'UA1', '$2y$10$WQOAg9/70jr6cuvXaY/2zeeFLhemAilQNL5VYi8IVhILQrEUAJKaq', '', '2026-01-14 08:43:19', 'Active'),
(23, 'Student', 'MUHAMMAD UWAIS DARWISH BIN MOHD PARID', 'uwaisdarwish0406@gmail.com', '0132938759', 'UMS', '$2y$10$2CR5P6tghvSyYzUXb9IYz.UNnqaBQPlMM7C1lX3.vg3ejBmUhUTZ2', '', '2026-01-15 03:01:58', 'Active'),
(24, 'Staff', 'shahira', 'shahira04chen@email.com', '0198765213', 'UMS', '$2y$10$2oEO6QywEln2BUZOckxGP.dGQJqhkTieVPuw/.e3V7MJ5ybtX1ONa', '', '2026-01-15 12:56:14', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `user_event_recommend`
--

CREATE TABLE `user_event_recommend` (
  `recommend_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_event_recommend`
--

INSERT INTO `user_event_recommend` (`recommend_id`, `user_id`, `name`) VALUES
(10, 19, 'workshop'),
(11, 19, 'competition'),
(12, 20, 'workshop'),
(13, 20, 'competition'),
(14, 20, 'festival'),
(15, 21, 'seminar'),
(16, 23, 'workshop'),
(17, 23, 'competition'),
(18, 23, 'festival'),
(19, 23, 'sport'),
(20, 23, 'course'),
(21, 24, 'workshop'),
(22, 24, 'competition'),
(23, 24, 'sport');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_events_createdby` (`created_by`),
  ADD KEY `idx_event_created_by` (`created_by`),
  ADD KEY `idx_event_status_visibility` (`admin_status`,`visibility`);

--
-- Indexes for table `event_category`
--
ALTER TABLE `event_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_feedback_event_user` (`event_id`,`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_notif_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_reg_event` (`event_id`),
  ADD KEY `idx_reg_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_event_recommend`
--
ALTER TABLE `user_event_recommend`
  ADD PRIMARY KEY (`recommend_id`),
  ADD KEY `fk_user_recommend` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `event_category`
--
ALTER TABLE `event_category`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_event_recommend`
--
ALTER TABLE `user_event_recommend`
  MODIFY `recommend_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `event_category` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_events_createdby` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_event_recommend`
--
ALTER TABLE `user_event_recommend`
  ADD CONSTRAINT `fk_user_recommend` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
