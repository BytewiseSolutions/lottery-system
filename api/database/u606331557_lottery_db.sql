-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 08, 2026 at 11:38 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u606331557_lottery_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 12, 'register', 'New user registered: Lebohang Monamane', '197.189.129.245', '2026-02-22 16:46:27'),
(2, 12, 'login', 'User logged in successfully', '197.189.129.245', '2026-02-22 16:46:46'),
(3, 12, 'login', 'User logged in successfully', '197.189.129.245', '2026-02-22 16:47:56'),
(4, 12, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '197.189.129.245', '2026-02-22 16:48:13'),
(5, 3, 'login', 'Admin logged in successfully', '197.189.129.245', '2026-02-22 17:23:05'),
(6, 12, 'login', 'User logged in successfully', '197.189.129.245', '2026-02-22 17:27:28'),
(7, 4, 'login', 'User logged in successfully', '41.56.221.128', '2026-02-22 17:57:03'),
(8, 4, 'login', 'User logged in successfully', '41.56.221.128', '2026-02-22 18:03:09'),
(9, 12, 'login', 'User logged in successfully', '197.189.129.245', '2026-02-22 18:08:22'),
(10, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '41.56.221.128', '2026-02-22 18:09:01'),
(11, 3, 'login', 'Admin logged in successfully', '197.189.129.245', '2026-02-22 21:18:52'),
(12, 3, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '197.189.129.245', '2026-02-22 21:22:35'),
(13, 3, 'login', 'Admin logged in successfully', '197.254.139.188', '2026-02-23 14:26:43'),
(14, 12, 'login', 'User logged in successfully', '197.254.139.188', '2026-02-23 14:27:24'),
(15, 12, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '197.254.139.188', '2026-02-23 14:27:32'),
(16, 12, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '197.254.139.188', '2026-02-23 14:27:44'),
(17, 9, 'login', 'User logged in successfully', '129.232.84.151', '2026-02-23 14:27:50'),
(18, 3, 'login', 'Admin logged in successfully', '197.254.139.188', '2026-02-23 14:27:59'),
(19, 9, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '129.232.84.151', '2026-02-23 14:28:23'),
(20, 9, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '129.232.84.151', '2026-02-23 14:28:56'),
(21, 9, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '129.232.84.151', '2026-02-23 14:29:16'),
(22, 13, 'register', 'New user registered: Khauhelo Mosehle', '129.232.113.207', '2026-02-23 16:00:18'),
(23, 13, 'login', 'User logged in successfully', '129.232.113.207', '2026-02-23 16:00:59'),
(24, 13, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-02-23 19:00:00', '129.232.113.207', '2026-02-23 16:02:22'),
(25, 3, 'login_failed', 'Failed login attempt - Invalid password', '41.56.221.128', '2026-02-23 18:43:10'),
(26, 3, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-02-23 18:43:55'),
(27, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-02-23T19:15 - 0 winner(s)', '41.56.221.128', '2026-02-23 18:45:57'),
(28, 4, 'login', 'User logged in successfully', '41.56.221.128', '2026-02-23 18:50:53'),
(29, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-02-25 19:00:00', '41.56.221.128', '2026-02-23 18:51:32'),
(30, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-02 19:00:00', '41.56.221.128', '2026-02-23 18:51:57'),
(31, 11, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-02-23 18:53:16'),
(32, 11, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-02-23 18:54:32'),
(33, 3, 'login', 'Admin logged in successfully', '197.254.139.188', '2026-02-24 08:28:48'),
(34, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-02-25 19:00:00', '41.56.221.128', '2026-02-24 15:17:53'),
(35, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '41.56.221.128', '2026-02-24 15:18:24'),
(36, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-02-25 19:00:00', '41.56.221.128', '2026-02-24 15:19:29'),
(37, 14, 'register', 'New user registered: Relebohile  Kometsi', '143.105.152.159', '2026-02-24 20:31:31'),
(38, 14, 'login', 'User logged in successfully', '143.105.152.159', '2026-02-24 20:32:08'),
(39, 14, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-02-25 19:00:00', '143.105.152.159', '2026-02-24 20:33:58'),
(40, 3, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-02-25 20:35:51'),
(41, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-02-25T19:15 - 0 winner(s)', '41.56.221.128', '2026-02-25 20:36:55'),
(42, 3, 'login', 'Admin logged in successfully', '197.254.141.195', '2026-02-25 20:50:53'),
(43, 12, 'login', 'User logged in successfully', '197.254.141.195', '2026-02-25 20:55:32'),
(44, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.254.141.195', '2026-02-25 20:55:49'),
(45, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.254.141.195', '2026-02-25 20:56:32'),
(46, 3, 'login', 'Admin logged in successfully', '197.254.141.195', '2026-02-26 07:21:38'),
(47, 12, 'login', 'User logged in successfully', '197.189.145.182', '2026-02-27 13:52:07'),
(48, 3, 'login', 'Admin logged in successfully', '197.189.145.182', '2026-02-27 13:52:44'),
(49, 12, 'login', 'User logged in successfully', '197.189.145.182', '2026-02-27 13:53:39'),
(50, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.189.145.182', '2026-02-27 13:53:43'),
(51, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.189.145.182', '2026-02-27 13:53:59'),
(52, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.189.145.182', '2026-02-27 13:54:14'),
(53, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.189.145.182', '2026-02-27 13:54:29'),
(54, 3, 'login', 'Admin logged in successfully', '197.189.145.182', '2026-02-27 13:54:52'),
(55, 12, 'login', 'User logged in successfully', '197.189.145.182', '2026-02-27 13:55:24'),
(56, 12, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-02-27 19:00:00', '197.189.145.182', '2026-02-27 13:55:26'),
(57, 3, 'login', 'Admin logged in successfully', '197.189.145.182', '2026-02-27 13:55:42'),
(58, 3, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-02-27 17:06:59'),
(59, 4, 'login', 'User logged in successfully', '41.56.221.128', '2026-02-27 17:08:46'),
(60, 3, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-02-27 17:10:10'),
(61, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-02-27T19:15 - 1 winner(s)', '41.56.221.128', '2026-02-27 17:11:34'),
(62, 12, 'login_failed', 'Failed login attempt - Invalid password', '197.189.139.247', '2026-02-27 18:21:21'),
(63, 12, 'login', 'User logged in successfully', '197.189.139.247', '2026-02-27 18:21:33'),
(64, 4, 'login', 'User logged in successfully', '41.56.221.128', '2026-02-27 18:30:04'),
(65, 3, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-03-02 21:22:08'),
(66, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-03-02T00:00 - 0 winner(s)', '41.56.221.128', '2026-03-02 21:23:01'),
(67, 15, 'register', 'New user registered: Piotr Sudy', '95.49.76.58', '2026-03-03 12:27:11'),
(68, 15, 'login', 'User logged in successfully', '95.49.76.58', '2026-03-03 12:28:07'),
(69, 15, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '95.49.76.58', '2026-03-03 12:30:03'),
(70, 15, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '95.49.76.58', '2026-03-03 12:31:41'),
(71, 15, 'login', 'User logged in successfully', '95.49.76.58', '2026-03-04 07:45:07'),
(72, 15, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-09 19:00:00', '95.49.76.58', '2026-03-04 07:47:42'),
(73, 15, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-09 19:00:00', '95.49.76.58', '2026-03-04 07:48:16'),
(74, 12, 'login', 'User logged in successfully', '129.232.88.90', '2026-03-04 15:31:17'),
(75, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:31:34'),
(76, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:31:46'),
(77, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:31:57'),
(78, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:32:11'),
(79, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:32:58'),
(80, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:33:08'),
(81, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:33:20'),
(82, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-04 19:00:00', '129.232.88.90', '2026-03-04 15:33:35'),
(83, 3, 'login', 'Admin logged in successfully', '129.232.88.90', '2026-03-04 15:34:28'),
(84, 3, 'login', 'Admin logged in successfully', '41.56.221.128', '2026-03-04 17:09:33'),
(85, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-03-04T00:00 - 0 winner(s)', '41.56.221.128', '2026-03-04 17:11:11'),
(86, 15, 'login', 'User logged in successfully', '95.49.76.58', '2026-03-05 07:56:53'),
(87, 15, 'account_deleted', '{\"reason\":\"user_requested\"}', '95.49.76.58', '2026-03-05 08:02:02'),
(88, 4, 'login', 'User logged in successfully', '41.56.197.232', '2026-03-05 20:03:50'),
(89, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-06 19:00:00', '41.56.197.232', '2026-03-05 20:04:17'),
(90, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-06 19:00:00', '41.56.197.232', '2026-03-05 20:04:52'),
(91, 4, 'login', 'User logged in successfully', '41.56.197.232', '2026-03-05 20:11:56'),
(92, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-06 19:00:00', '41.56.197.232', '2026-03-05 20:12:20'),
(93, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-09 19:00:00', '41.56.197.232', '2026-03-05 20:12:39'),
(94, 12, 'login', 'User logged in successfully', '197.189.142.78', '2026-03-06 05:33:59'),
(95, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-06 19:47:37'),
(96, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-03-06T00:00 - 0 winner(s)', '41.56.197.232', '2026-03-06 19:49:58'),
(97, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-09 09:24:49'),
(98, 3, 'login', 'Admin logged in successfully', '129.232.72.157', '2026-03-09 10:10:07'),
(99, 3, 'login', 'Admin logged in successfully', '129.232.75.170', '2026-03-09 17:30:22'),
(100, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-10 08:03:03'),
(101, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-11 19:00:00', '41.56.197.232', '2026-03-10 08:03:42'),
(102, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-03-09T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-10 08:04:57'),
(103, 12, 'login', 'User logged in successfully', '129.232.88.116', '2026-03-11 13:40:18'),
(104, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-11 19:00:00', '129.232.88.116', '2026-03-11 13:41:39'),
(105, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-11 19:00:00', '129.232.88.116', '2026-03-11 13:42:05'),
(106, 3, 'login', 'Admin logged in successfully', '129.232.88.116', '2026-03-11 13:43:18'),
(107, 12, 'login', 'User logged in successfully', '129.232.88.116', '2026-03-11 13:45:14'),
(108, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-11 15:07:56'),
(109, 3, 'login', 'Admin logged in successfully', '129.232.88.116', '2026-03-11 15:39:42'),
(110, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-03-11T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-12 06:19:13'),
(111, 3, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-13 19:00:00', '41.56.197.232', '2026-03-12 11:53:03'),
(112, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-14 16:03:10'),
(113, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-03-13T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-14 16:04:00'),
(114, 12, 'login', 'User logged in successfully', '129.232.90.116', '2026-03-16 17:03:26'),
(115, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-16 18:08:05'),
(116, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-03-16T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-16 18:09:11'),
(117, 3, 'login_failed', 'Failed login attempt - Invalid password', '41.56.197.232', '2026-03-18 18:07:05'),
(118, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-18 18:07:15'),
(119, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-03-18T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-18 18:08:33'),
(120, 4, 'login', 'User logged in successfully', '41.56.197.232', '2026-03-21 05:48:11'),
(121, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-23 19:00:00', '41.56.197.232', '2026-03-21 05:50:29'),
(122, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-25 19:00:00', '41.56.197.232', '2026-03-21 05:50:54'),
(123, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-21 06:12:22'),
(124, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-03-20T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-21 06:13:32'),
(125, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-23 19:00:00', '41.56.197.232', '2026-03-21 20:45:41'),
(126, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-03-23 19:00:00', '41.56.197.232', '2026-03-21 20:46:01'),
(127, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-25 19:00:00', '41.56.197.232', '2026-03-21 20:46:28'),
(128, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-24 15:32:32'),
(129, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-03-23T20:00 - 0 winner(s)', '41.56.197.232', '2026-03-24 15:33:25'),
(130, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-25 19:00:00', '41.56.197.232', '2026-03-24 17:40:51'),
(131, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-03-25 19:00:00', '41.56.197.232', '2026-03-24 17:41:07'),
(132, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-27 19:00:00', '41.56.197.232', '2026-03-24 17:41:27'),
(133, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-27 19:00:00', '41.56.197.232', '2026-03-24 17:41:46'),
(134, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-03-27 19:00:00', '41.56.197.232', '2026-03-24 17:42:05'),
(135, 3, 'login', 'Admin logged in successfully', '41.56.197.232', '2026-03-25 20:05:52'),
(136, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-03-25T00:00 - 0 winner(s)', '41.56.197.232', '2026-03-25 20:06:48'),
(137, 16, 'register', 'New user registered: Jermaine Mattis', '67.230.79.29', '2026-03-29 02:07:55'),
(138, 16, 'login', 'User logged in successfully', '67.230.79.29', '2026-03-29 02:08:02'),
(139, 16, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-01 19:00:00', '67.230.79.29', '2026-03-29 02:08:29'),
(140, 3, 'login', 'Admin logged in successfully', '197.189.143.127', '2026-03-29 12:37:21'),
(141, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-03-27T00:00 - 0 winner(s)', '197.189.143.127', '2026-03-29 12:37:56'),
(142, 17, 'register', 'New user registered: ezequiel mansilla', '45.166.177.244', '2026-03-30 18:57:18'),
(143, 17, 'login', 'User logged in successfully', '45.166.177.244', '2026-03-30 18:58:27'),
(144, 17, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-01 19:00:00', '45.166.177.244', '2026-03-30 18:59:36'),
(145, 17, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '45.166.177.244', '2026-03-30 19:01:00'),
(146, 3, 'login', 'Admin logged in successfully', '197.189.138.137', '2026-03-31 06:46:13'),
(147, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-03-30T00:00 - 0 winner(s)', '197.189.138.137', '2026-03-31 06:46:54'),
(148, 3, 'login', 'Admin logged in successfully', '129.232.75.153', '2026-04-01 16:37:36'),
(149, 12, 'login', 'User logged in successfully', '129.232.75.153', '2026-04-01 16:39:15'),
(150, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-01 19:00:00', '129.232.75.153', '2026-04-01 16:39:18'),
(151, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-01 19:00:00', '129.232.75.153', '2026-04-01 16:39:32'),
(152, 3, 'login', 'Admin logged in successfully', '129.232.75.153', '2026-04-01 18:35:15'),
(153, 12, 'login', 'User logged in successfully', '129.232.75.153', '2026-04-01 18:37:41'),
(154, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-04-01T00:00 - 1 winner(s)', '129.232.75.153', '2026-04-01 18:38:36'),
(155, 18, 'register', 'New user registered: ali kemal ulusal', '94.123.192.80', '2026-04-02 19:04:36'),
(156, 18, 'login', 'User logged in successfully', '94.123.192.80', '2026-04-02 19:05:20'),
(157, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '94.123.192.80', '2026-04-02 19:06:51'),
(158, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '94.123.192.80', '2026-04-02 19:07:42'),
(159, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.192.80', '2026-04-02 19:08:15'),
(160, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.192.80', '2026-04-02 19:08:41'),
(161, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.192.80', '2026-04-02 19:09:05'),
(162, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.192.187', '2026-04-03 09:45:21'),
(163, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.192.187', '2026-04-03 09:45:52'),
(164, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.192.187', '2026-04-03 09:46:21'),
(165, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '94.123.192.187', '2026-04-03 09:46:52'),
(166, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '94.123.192.187', '2026-04-03 09:47:22'),
(167, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '94.123.192.187', '2026-04-03 09:47:46'),
(168, 19, 'register', 'New user registered: Md.Monzurul Islam', '103.83.240.172', '2026-04-03 11:41:59'),
(169, 19, 'login', 'User logged in successfully', '103.83.240.172', '2026-04-03 11:43:21'),
(170, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '103.83.240.172', '2026-04-03 11:44:58'),
(171, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-03 11:48:43'),
(172, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '103.83.240.172', '2026-04-03 11:50:27'),
(173, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-03 19:00:00', '103.83.240.172', '2026-04-03 11:51:34'),
(174, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-03 11:58:53'),
(175, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '103.83.240.172', '2026-04-03 11:59:36'),
(176, 12, 'login', 'User logged in successfully', '197.189.135.52', '2026-04-04 01:14:50'),
(177, 3, 'login', 'Admin logged in successfully', '197.189.135.52', '2026-04-04 01:17:23'),
(178, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-04-03T00:00 - 0 winner(s)', '197.189.135.52', '2026-04-04 01:18:12'),
(179, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.205.115', '2026-04-04 08:47:43'),
(180, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.205.115', '2026-04-04 08:48:11'),
(181, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '94.123.205.115', '2026-04-04 08:48:35'),
(182, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.205.115', '2026-04-04 08:49:03'),
(183, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '94.123.205.115', '2026-04-04 08:49:29'),
(184, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.205.115', '2026-04-04 08:49:57'),
(185, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.205.115', '2026-04-04 08:50:31'),
(186, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.205.115', '2026-04-04 08:50:58'),
(187, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.205.115', '2026-04-04 08:53:54'),
(188, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:08:38'),
(189, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:09:21'),
(190, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-04 09:09:59'),
(191, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-04 09:10:36'),
(192, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:12:49'),
(193, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:13:24'),
(194, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-04 09:14:02'),
(195, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-04 09:15:26'),
(196, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:16:24'),
(197, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:17:13'),
(198, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:18:12'),
(199, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:19:39'),
(200, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:21:43'),
(201, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:22:27'),
(202, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:23:08'),
(203, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-04 09:24:57'),
(204, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:25:21'),
(205, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:26:28'),
(206, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-04 09:28:07'),
(207, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 17:56:41'),
(208, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 17:57:11'),
(209, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 17:58:55'),
(210, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 17:59:17'),
(211, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 17:59:43'),
(212, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 18:00:08'),
(213, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 18:00:31'),
(214, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 18:00:55'),
(215, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 18:01:19'),
(216, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-04 18:06:37'),
(217, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:54:56'),
(218, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:55:59'),
(219, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:56:04'),
(220, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:56:45'),
(221, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:57:21'),
(222, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.200.180', '2026-04-05 08:58:03'),
(223, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:58:15'),
(224, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.200.180', '2026-04-05 08:58:35'),
(225, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:58:36'),
(226, 18, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '94.123.200.180', '2026-04-05 08:59:08'),
(227, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 08:59:23'),
(228, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '94.123.200.180', '2026-04-05 08:59:34'),
(229, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.200.180', '2026-04-05 08:59:56'),
(230, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:00:15'),
(231, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.200.180', '2026-04-05 09:00:47'),
(232, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:01:03'),
(233, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:01:51'),
(234, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:02:18'),
(235, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:02:58'),
(236, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:03:33'),
(237, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:04:21'),
(238, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:04:45'),
(239, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:05:06'),
(240, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:05:16'),
(241, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:05:44'),
(242, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:06:59'),
(243, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:07:27'),
(244, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:08:25'),
(245, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:08:49'),
(246, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:09:26'),
(247, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:09:57'),
(248, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:10:25'),
(249, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:10:52'),
(250, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:11:30'),
(251, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:12:09'),
(252, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:12:34'),
(253, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:13:20'),
(254, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:14:03'),
(255, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:14:42'),
(256, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:15:12'),
(257, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 09:15:49'),
(258, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:01:00'),
(259, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:01:19'),
(260, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:01:39'),
(261, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:01:59'),
(262, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:02:31'),
(263, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:03:19'),
(264, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:03:39'),
(265, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:04:03'),
(266, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:04:24'),
(267, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:04:45'),
(268, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:05:08'),
(269, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:05:27'),
(270, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:05:48'),
(271, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:06:12'),
(272, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:06:33'),
(273, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:06:56'),
(274, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:07:30'),
(275, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:07:56'),
(276, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:08:32'),
(277, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:08:56'),
(278, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:09:18'),
(279, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 10:09:50'),
(280, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:57:33'),
(281, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:57:58'),
(282, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:58:22'),
(283, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:58:41'),
(284, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:59:10'),
(285, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:59:31'),
(286, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 13:59:49'),
(287, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-05 14:00:13'),
(288, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '103.83.240.172', '2026-04-06 03:41:41'),
(289, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:42:21'),
(290, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:42:54'),
(291, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:43:21'),
(292, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:44:00'),
(293, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:44:29'),
(294, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:44:53'),
(295, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:45:41'),
(296, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 03:45:55'),
(297, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:55:02'),
(298, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:55:26'),
(299, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:55:48'),
(300, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:56:17'),
(301, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:56:40'),
(302, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:56:59'),
(303, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:57:38'),
(304, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:58:13'),
(305, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:59:00'),
(306, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:59:04'),
(307, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '103.83.240.172', '2026-04-06 04:59:31'),
(308, 18, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '94.123.205.150', '2026-04-06 08:33:29'),
(309, 18, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '94.123.205.150', '2026-04-06 08:33:57'),
(310, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:02:59'),
(311, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:03:28'),
(312, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:04:07'),
(313, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:04:32'),
(314, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:05:01'),
(315, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:05:35'),
(316, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:15:20'),
(317, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:19:00'),
(318, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:19:41'),
(319, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:20:10'),
(320, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:20:45'),
(321, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:21:08'),
(322, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:21:30'),
(323, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:22:30'),
(324, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:24:18'),
(325, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:25:09'),
(326, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:25:38'),
(327, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:26:15'),
(328, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:26:37'),
(329, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-06 09:27:32'),
(330, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:27:56'),
(331, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:28:40'),
(332, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:29:19'),
(333, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:29:47'),
(334, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:31:02'),
(335, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:34:14'),
(336, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:35:02'),
(337, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:35:29'),
(338, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:35:54'),
(339, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:36:20'),
(340, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-06 19:00:00', '106.0.61.241', '2026-04-06 09:36:52'),
(341, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-07 08:45:28'),
(342, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-07 08:50:54'),
(343, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-07 08:51:07'),
(344, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-07 08:51:41'),
(345, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-07 08:52:20'),
(346, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-07 08:52:52'),
(347, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:37:11'),
(348, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:37:49'),
(349, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:38:19'),
(350, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:38:51'),
(351, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:39:46'),
(352, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:40:28'),
(353, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:40:54'),
(354, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:41:49'),
(355, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:42:31'),
(356, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:42:55'),
(357, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:43:20'),
(358, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:43:47'),
(359, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:44:18'),
(360, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:44:46'),
(361, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:45:18'),
(362, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:50:56'),
(363, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:51:21'),
(364, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:51:52'),
(365, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:52:52'),
(366, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:53:27'),
(367, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:54:07'),
(368, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:54:31'),
(369, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:54:55'),
(370, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:55:24'),
(371, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:56:15'),
(372, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:57:06'),
(373, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:57:41'),
(374, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:58:08'),
(375, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 09:58:37'),
(376, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 10:00:21'),
(377, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 10:00:49'),
(378, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 10:01:17'),
(379, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 10:02:01'),
(380, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 10:02:39'),
(381, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-08 19:00:00', '106.0.61.241', '2026-04-08 10:03:27'),
(382, 3, 'login', 'Admin logged in successfully', '129.232.89.7', '2026-04-08 16:08:17'),
(383, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-04-06T00:00 - 0 winner(s)', '129.232.89.7', '2026-04-08 16:14:38'),
(384, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:36:12'),
(385, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:37:52'),
(386, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:38:22'),
(387, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:38:45'),
(388, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:39:16'),
(389, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:39:42'),
(390, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:40:14'),
(391, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 04:40:54'),
(392, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:05:18'),
(393, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:07:13'),
(394, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:07:54'),
(395, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:08:33'),
(396, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:09:08'),
(397, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:09:46'),
(398, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:10:15'),
(399, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:10:51'),
(400, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:11:25'),
(401, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:11:56'),
(402, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:12:26'),
(403, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:12:54'),
(404, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:16:44'),
(405, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:17:15'),
(406, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:17:49'),
(407, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:18:30'),
(408, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:18:54'),
(409, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:19:22'),
(410, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-09 05:22:38'),
(411, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-04-08T00:00 - 0 winner(s)', '129.232.69.103', '2026-04-09 05:45:30'),
(412, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '106.0.61.241', '2026-04-09 14:41:16'),
(413, 3, 'login', 'Admin logged in successfully', '129.232.71.124', '2026-04-09 17:01:40'),
(414, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:41:27'),
(415, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:41:56'),
(416, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:42:18'),
(417, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:42:40'),
(418, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:42:58'),
(419, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:43:21'),
(420, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:43:47'),
(421, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:44:14');
INSERT INTO `activity_log` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(422, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:44:37'),
(423, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:45:05'),
(424, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:45:24'),
(425, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:45:45'),
(426, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:46:12'),
(427, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:46:33'),
(428, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:46:56'),
(429, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:48:04'),
(430, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:48:31'),
(431, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:48:57'),
(432, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:49:22'),
(433, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:49:45'),
(434, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:50:09'),
(435, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:50:31'),
(436, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:50:57'),
(437, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:51:29'),
(438, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:51:52'),
(439, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:52:13'),
(440, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:52:33'),
(441, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:53:02'),
(442, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:53:23'),
(443, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:53:48'),
(444, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:54:12'),
(445, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:54:34'),
(446, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:55:00'),
(447, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:55:22'),
(448, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:55:42'),
(449, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:56:06'),
(450, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:56:27'),
(451, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:56:46'),
(452, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:57:06'),
(453, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:57:27'),
(454, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:57:47'),
(455, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:58:07'),
(456, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:58:31'),
(457, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:58:56'),
(458, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-10 19:00:00', '103.83.240.172', '2026-04-10 02:59:22'),
(459, 3, 'login', 'Admin logged in successfully', '129.232.75.136', '2026-04-10 17:20:47'),
(460, 3, 'login', 'Admin logged in successfully', '129.232.75.136', '2026-04-10 19:29:45'),
(461, 3, 'login', 'Admin logged in successfully', '129.232.73.8', '2026-04-11 05:50:59'),
(462, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-04-10T00:00 - 0 winner(s)', '129.232.73.8', '2026-04-11 05:51:55'),
(463, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-11 08:40:19'),
(464, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-11 08:40:53'),
(465, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-11 08:41:20'),
(466, 3, 'login', 'Admin logged in successfully', '197.189.178.12', '2026-04-11 10:42:10'),
(467, 3, 'login', 'Admin logged in successfully', '129.232.73.8', '2026-04-11 10:47:15'),
(468, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '197.189.178.12', '2026-04-11 11:27:26'),
(469, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '197.189.178.12', '2026-04-11 11:27:54'),
(470, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-15 19:00:00', '197.189.178.12', '2026-04-11 11:28:21'),
(471, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '197.189.178.12', '2026-04-11 11:28:43'),
(472, 4, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-15 19:00:00', '197.189.178.12', '2026-04-11 11:36:04'),
(473, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '197.189.178.12', '2026-04-11 11:36:31'),
(474, 3, 'login', 'Admin logged in successfully', '129.232.73.8', '2026-04-11 12:25:20'),
(475, 3, 'login', 'Admin logged in successfully', '129.232.95.233', '2026-04-11 15:55:53'),
(476, 3, 'login', 'Admin logged in successfully', '197.254.179.12', '2026-04-12 06:25:09'),
(477, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-12 08:48:08'),
(478, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-12 08:48:41'),
(479, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-12 08:49:26'),
(480, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-12 08:49:50'),
(481, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-12 08:50:19'),
(482, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-12 08:50:38'),
(483, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:50:18'),
(484, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:51:42'),
(485, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:52:53'),
(486, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:55:06'),
(487, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:55:35'),
(488, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:55:58'),
(489, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:56:22'),
(490, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:57:03'),
(491, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:57:27'),
(492, 19, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '106.0.61.241', '2026-04-13 08:58:00'),
(493, 20, 'register', 'New user registered: Lions Kartel', '197.189.140.13', '2026-04-13 12:29:54'),
(494, 20, 'login', 'User logged in successfully', '197.189.140.13', '2026-04-13 12:31:10'),
(495, 20, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '197.189.140.13', '2026-04-13 12:31:41'),
(496, 12, 'login', 'User logged in successfully', '129.232.87.181', '2026-04-13 14:13:48'),
(497, 12, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '129.232.69.162', '2026-04-13 16:38:20'),
(498, 12, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-13 19:00:00', '129.232.69.162', '2026-04-13 16:38:39'),
(499, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-04-13T00:00 - 0 winner(s)', '197.254.177.180', '2026-04-14 09:44:56'),
(500, 12, 'login', 'User logged in successfully', '129.232.69.132', '2026-04-15 16:07:20'),
(501, 12, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-15 19:00:00', '129.232.69.132', '2026-04-15 16:07:39'),
(502, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-15 19:28:52'),
(503, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-15 19:29:21'),
(504, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-15 19:29:45'),
(505, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-15 19:30:08'),
(506, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-15 19:30:35'),
(507, 21, 'register', 'New user registered: Jeremy McLaren', '2001:8004:1d84:4a2d:1178:4701:92a6:75e3', '2026-04-16 00:58:45'),
(508, 21, 'login', 'User logged in successfully', '2001:8004:1d84:4a2d:1178:4701:92a6:75e3', '2026-04-16 00:58:56'),
(509, 21, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '2001:8004:1d84:4a2d:1178:4701:92a6:75e3', '2026-04-16 00:59:29'),
(510, 21, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2001:8004:1d84:4a2d:1178:4701:92a6:75e3', '2026-04-16 00:59:56'),
(511, 21, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2001:8004:1d84:4a2d:1178:4701:92a6:75e3', '2026-04-16 01:00:22'),
(512, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-04-15T20:00 - 0 winner(s)', '197.189.163.27', '2026-04-16 11:47:07'),
(513, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:51:43'),
(514, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:52:09'),
(515, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:52:38'),
(516, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:53:48'),
(517, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:54:14'),
(518, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:54:49'),
(519, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:55:21'),
(520, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:55:57'),
(521, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:56:21'),
(522, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:56:54'),
(523, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:57:27'),
(524, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:57:57'),
(525, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:58:27'),
(526, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:58:59'),
(527, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:59:28'),
(528, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 04:59:58'),
(529, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:00:25'),
(530, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:01:08'),
(531, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:01:36'),
(532, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:01:57'),
(533, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:02:56'),
(534, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:03:07'),
(535, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '103.83.240.172', '2026-04-17 05:03:41'),
(536, 22, 'register', 'New user registered: Imran Haider', '154.81.233.49', '2026-04-17 14:46:22'),
(537, 22, 'login', 'User logged in successfully', '154.81.233.49', '2026-04-17 14:46:41'),
(538, 22, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '154.81.233.49', '2026-04-17 14:48:21'),
(539, 22, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '154.81.233.49', '2026-04-17 14:48:53'),
(540, 22, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '154.81.233.49', '2026-04-17 14:49:13'),
(541, 22, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '154.81.233.49', '2026-04-17 14:49:31'),
(542, 22, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '154.81.233.49', '2026-04-17 14:50:06'),
(543, 22, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '154.81.233.49', '2026-04-17 14:52:32'),
(544, 22, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-17 19:00:00', '154.81.233.49', '2026-04-17 14:54:37'),
(545, 22, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '154.81.233.49', '2026-04-17 15:00:15'),
(546, 23, 'register', 'New user registered: Karla Hubbard', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:40:17'),
(547, 23, 'login', 'User logged in successfully', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:40:37'),
(548, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:42:04'),
(549, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:42:37'),
(550, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:43:01'),
(551, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:43:24'),
(552, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:43:42'),
(553, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:44:08'),
(554, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:44:27'),
(555, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:44:44'),
(556, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:45:07'),
(557, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:45:26'),
(558, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:45:45'),
(559, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:46:18'),
(560, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:46:38'),
(561, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:47:03'),
(562, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:47:26'),
(563, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:47:47'),
(564, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:48:09'),
(565, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:48:32'),
(566, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:48:55'),
(567, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:49:16'),
(568, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:49:39'),
(569, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:50:01'),
(570, 23, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-17 17:50:20'),
(571, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-04-17T20:00 - 0 winner(s)', '197.254.136.197', '2026-04-17 18:00:44'),
(572, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:00:55'),
(573, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:02:28'),
(574, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:04:15'),
(575, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:05:21'),
(576, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:06:21'),
(577, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:07:18'),
(578, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:08:08'),
(579, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:09:25'),
(580, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:09:59'),
(581, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:10:24'),
(582, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:11:30'),
(583, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:12:20'),
(584, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:13:13'),
(585, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:13:59'),
(586, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:14:56'),
(587, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:17:11'),
(588, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:18:02'),
(589, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:18:43'),
(590, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:19:30'),
(591, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:20:02'),
(592, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:20:41'),
(593, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:21:34'),
(594, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:22:50'),
(595, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:23:24'),
(596, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:24:02'),
(597, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:24:26'),
(598, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:24:57'),
(599, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:25:40'),
(600, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:26:29'),
(601, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:27:00'),
(602, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:27:25'),
(603, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:28:01'),
(604, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:28:30'),
(605, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:29:08'),
(606, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:29:53'),
(607, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:30:50'),
(608, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:31:14'),
(609, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:31:47'),
(610, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:32:11'),
(611, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:32:40'),
(612, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:33:06'),
(613, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:33:38'),
(614, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:34:05'),
(615, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:34:34'),
(616, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:35:06'),
(617, 23, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:35:43'),
(618, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:36:12'),
(619, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:36:47'),
(620, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:38:37'),
(621, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:38:59'),
(622, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:39:26'),
(623, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:39:54'),
(624, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:40:21'),
(625, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:40:45'),
(626, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:41:07'),
(627, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:41:32'),
(628, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:41:56'),
(629, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:42:30'),
(630, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:43:02'),
(631, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:43:32'),
(632, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:44:10'),
(633, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:44:46'),
(634, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:45:26'),
(635, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:54:12'),
(636, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:54:36'),
(637, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:55:35'),
(638, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:56:23'),
(639, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:56:45'),
(640, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:57:12'),
(641, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:57:35'),
(642, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:57:59'),
(643, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:58:45'),
(644, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:59:08'),
(645, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:59:30'),
(646, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 05:59:53'),
(647, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:00:15'),
(648, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:00:42'),
(649, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:01:12'),
(650, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:01:37'),
(651, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:01:59'),
(652, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:02:27'),
(653, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 06:02:54'),
(654, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:39:19'),
(655, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:39:40'),
(656, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:40:22'),
(657, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:40:39'),
(658, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:40:55'),
(659, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:41:11'),
(660, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:41:32'),
(661, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:42:03'),
(662, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:42:21'),
(663, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:42:42'),
(664, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:43:03'),
(665, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:43:21'),
(666, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:43:41'),
(667, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:44:01'),
(668, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:44:24'),
(669, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:44:45'),
(670, 23, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-20 19:00:00', '2600:1009:b168:b48b:0:50:114:d101', '2026-04-18 15:45:07'),
(671, 3, 'login', 'Admin logged in successfully', '197.254.183.43', '2026-04-20 18:03:39'),
(672, 3, 'login', 'Admin logged in successfully', '197.254.183.43', '2026-04-20 19:49:55'),
(673, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:14:32'),
(674, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:15:08'),
(675, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:19:05'),
(676, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:19:24'),
(677, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:19:43'),
(678, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:20:02'),
(679, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:20:23'),
(680, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:20:45'),
(681, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:21:08'),
(682, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:21:37'),
(683, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-22 19:00:00', '106.0.61.241', '2026-04-21 10:22:03'),
(684, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-04-20T20:00 - 0 winner(s)', '197.189.184.172', '2026-04-21 14:13:23'),
(685, 24, 'register', 'New user registered: Bokang  Letsoso', '129.232.102.210', '2026-04-22 18:11:36'),
(686, 24, 'login', 'User logged in successfully', '129.232.102.210', '2026-04-22 18:12:11'),
(687, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '129.232.102.210', '2026-04-22 18:13:51'),
(688, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '129.232.102.210', '2026-04-22 18:14:54'),
(689, 24, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-27 19:00:00', '129.232.102.210', '2026-04-22 18:15:44'),
(690, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '129.232.102.210', '2026-04-22 18:17:31'),
(691, 3, 'login', 'Admin logged in successfully', '197.189.161.116', '2026-04-22 18:31:45'),
(692, 3, 'login', 'Admin logged in successfully', '197.189.161.116', '2026-04-22 18:39:31'),
(693, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-04-22T20:00 - 0 winner(s)', '197.189.161.116', '2026-04-22 18:42:24'),
(694, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '103.83.240.128', '2026-04-24 11:36:43'),
(695, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '103.83.240.128', '2026-04-24 11:37:06'),
(696, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '103.83.240.128', '2026-04-24 11:37:24'),
(697, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '103.83.240.128', '2026-04-24 11:37:53'),
(698, 19, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-04-24 19:00:00', '103.83.240.128', '2026-04-24 11:39:25'),
(699, 3, 'login', 'Admin logged in successfully', '197.189.167.166', '2026-04-24 19:17:23'),
(700, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-04-24T20:00 - 0 winner(s)', '197.189.167.166', '2026-04-24 19:18:37'),
(701, 25, 'register', 'New user registered: Abdulghafour Mohamed', '197.133.118.70', '2026-04-25 17:28:16'),
(702, 25, 'login', 'User logged in successfully', '197.133.118.70', '2026-04-25 17:28:53'),
(703, 25, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-27 19:00:00', '197.133.118.70', '2026-04-25 17:29:21'),
(704, 25, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-04-27 19:00:00', '197.133.118.70', '2026-04-25 17:29:46'),
(705, 25, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-29 19:00:00', '197.133.118.70', '2026-04-25 17:30:18'),
(706, 25, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-01 19:00:00', '197.133.118.70', '2026-04-25 17:31:15'),
(707, 3, 'login', 'Admin logged in successfully', '197.189.129.55', '2026-04-25 18:58:30'),
(708, 26, 'register', 'New user registered: Sheeja Kr', '2405:201:d02b:5806:1c4f:f1d4:ac04:1925', '2026-04-27 13:33:22'),
(709, 26, 'login', 'User logged in successfully', '2405:201:d02b:5806:1c4f:f1d4:ac04:1925', '2026-04-27 13:33:50'),
(710, 3, 'login', 'Admin logged in successfully', '197.189.167.52', '2026-04-27 18:17:29'),
(711, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-04-27T20:00 - 0 winner(s)', '197.189.167.52', '2026-04-27 18:18:51'),
(712, 24, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-04-29 19:00:00', '129.232.103.232', '2026-04-28 18:28:27'),
(713, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-01 19:00:00', '129.232.103.232', '2026-04-28 18:30:11'),
(714, 4, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-05-04 19:00:00', '197.254.137.21', '2026-04-29 20:35:22'),
(715, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-01 19:00:00', '197.254.137.21', '2026-04-29 20:35:50'),
(716, 4, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-01 19:00:00', '197.254.137.21', '2026-04-29 20:36:07'),
(717, 3, 'login', 'Admin logged in successfully', '197.254.137.21', '2026-04-29 20:38:47'),
(718, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-04-29T20:00 - 0 winner(s)', '197.254.137.21', '2026-04-29 20:39:55'),
(719, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-01 19:00:00', '129.232.107.110', '2026-04-30 18:25:57'),
(720, 3, 'login', 'Admin logged in successfully', '197.189.181.80', '2026-05-01 18:40:32'),
(721, 3, 'upload_result', 'Admin uploaded results for Friday Lotto on 2026-05-01T20:00 - 0 winner(s)', '197.189.181.80', '2026-05-01 18:41:42'),
(722, 21, 'submit_entry', 'Submitted entry for Monday Lotto on 2026-05-04 19:00:00', '1.145.136.173', '2026-05-02 06:32:42'),
(723, 21, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '1.145.136.173', '2026-05-02 06:33:02'),
(724, 21, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-08 19:00:00', '1.145.136.173', '2026-05-02 06:33:29'),
(725, 3, 'login', 'Admin logged in successfully', '197.189.167.106', '2026-05-04 19:24:48'),
(726, 3, 'upload_result', 'Admin uploaded results for Monday Lotto on 2026-05-04T20:00 - 0 winner(s)', '197.189.167.106', '2026-05-04 19:26:19'),
(727, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '103.83.240.128', '2026-05-05 19:06:35'),
(728, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '103.83.240.128', '2026-05-05 19:07:45'),
(729, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '103.83.240.128', '2026-05-05 19:08:37'),
(730, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '103.83.240.128', '2026-05-05 19:09:25'),
(731, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '103.83.240.128', '2026-05-05 19:09:58'),
(732, 19, 'submit_entry', 'Submitted entry for Wednesday Lotto on 2026-05-06 19:00:00', '103.83.240.128', '2026-05-05 19:10:34'),
(733, 3, 'login', 'Admin logged in successfully', '197.254.181.28', '2026-05-06 10:14:52'),
(734, 3, 'login', 'Admin logged in successfully', '197.254.170.76', '2026-05-07 08:23:55'),
(735, 3, 'upload_result', 'Admin uploaded results for Wednesday Lotto on 2026-05-06T20:00 - 0 winner(s)', '197.254.170.76', '2026-05-07 08:24:58'),
(736, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-08 19:00:00', '197.189.160.197', '2026-05-07 19:57:51'),
(737, 24, 'submit_entry', 'Submitted entry for Friday Lotto on 2026-05-08 19:00:00', '197.189.160.197', '2026-05-07 20:01:36'),
(738, 3, 'login', 'Admin logged in successfully', '129.232.81.245', '2026-05-08 11:29:16');

-- --------------------------------------------------------

--
-- Table structure for table `admin_vote`
--

CREATE TABLE `admin_vote` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` text DEFAULT NULL,
  `bonus_numbers` text DEFAULT NULL,
  `voting_data` text DEFAULT NULL,
  `allocated_votes` int(11) DEFAULT NULL,
  `total_votes` int(11) DEFAULT NULL,
  `vote_date` date NOT NULL,
  `draw_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_vote`
--

INSERT INTO `admin_vote` (`id`, `admin_id`, `lottery`, `numbers`, `bonus_numbers`, `voting_data`, `allocated_votes`, `total_votes`, `vote_date`, `draw_date`, `created_at`, `updated_at`) VALUES
(4, 3, 'Wednesday Lotto', '[1,11,34,46,58]', '[4,15]', '{\"mainNumberVotes\":{\"1\":50,\"11\":300,\"34\":400,\"46\":700,\"58\":900},\"bonusNumberVotes\":{\"4\":78,\"15\":700},\"mainNumbers\":[1,11,34,46,58],\"bonusNumbers\":[4,15]}', 3128, 3128, '2026-03-11', '2026-03-11', '2026-03-11 13:44:56', NULL),
(5, 3, 'Friday Lotto', '[1,11,34,46,58]', '[4,15]', '{\"mainNumberVotes\":{\"1\":10,\"11\":10,\"34\":10,\"46\":10,\"58\":10},\"bonusNumberVotes\":{\"4\":10,\"15\":10},\"mainNumbers\":[1,11,34,46,58],\"bonusNumbers\":[4,15]}', 70, 70, '2026-03-11', '2026-03-11', '2026-03-11 15:54:26', NULL),
(10, 3, 'Monday Lotto', '[10,20,30,40,50]', '[60,70]', '{\"mainNumberVotes\":{\"10\":100,\"20\":100,\"30\":100,\"40\":100,\"50\":100},\"bonusNumberVotes\":{\"60\":100,\"70\":100},\"mainNumbers\":[10,20,30,40,50],\"bonusNumbers\":[60,70]}', 700, 700, '2026-04-12', '2026-04-13', '2026-04-12 08:05:17', NULL),
(11, 3, 'Monday Lotto', '[10,20,30,40,50]', '[60,70]', '{\"mainNumberVotes\":{\"10\":10,\"20\":10,\"30\":10,\"40\":10,\"50\":10},\"bonusNumberVotes\":{\"60\":10,\"70\":10},\"mainNumbers\":[10,20,30,40,50],\"bonusNumbers\":[60,70]}', 70, 70, '2026-04-12', '2026-04-13', '2026-04-12 08:06:41', NULL),
(12, 3, 'Monday Lotto', '[17,18,19,20,21]', '[74,75]', '{\"mainNumberVotes\":{\"17\":50,\"18\":50,\"19\":50,\"20\":50,\"21\":50},\"bonusNumberVotes\":{\"74\":50,\"75\":50},\"mainNumbers\":[17,18,19,20,21],\"bonusNumbers\":[74,75]}', 350, 350, '2026-04-12', '2026-04-13', '2026-04-12 08:09:26', NULL),
(13, 3, 'Monday Lotto', '[10,20,30,40,50]', '[60,70]', '{\"mainNumberVotes\":{\"10\":200,\"20\":200,\"30\":200,\"40\":200,\"50\":200},\"bonusNumberVotes\":{\"60\":200,\"70\":200},\"mainNumbers\":[10,20,30,40,50],\"bonusNumbers\":[60,70]}', 1400, 1400, '2026-04-12', '2026-04-13', '2026-04-12 08:37:49', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `admin_vote_statistics`
-- (See below for the actual view)
--
CREATE TABLE `admin_vote_statistics` (
`lottery` varchar(50)
,`draw_date` date
,`total_allocated_votes` decimal(32,0)
,`total_admin_votes` decimal(32,0)
,`allocation_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'LEBOHANG MONAMANE', 'monamane.lebohang45@gmail.com', 'Ke kopa ho thusoa', '2026-02-22 17:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `data_file`
--

CREATE TABLE `data_file` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_data` longblob NOT NULL,
  `file_category` varchar(50) DEFAULT 'profile_picture',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entry`
--

CREATE TABLE `entry` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`numbers`)),
  `bonus_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bonus_numbers`)),
  `draw_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `entry`
--

INSERT INTO `entry` (`id`, `user_id`, `lottery`, `numbers`, `bonus_numbers`, `draw_date`, `created_at`) VALUES
(29, 4, 'Wednesday Lotto', '[3,4,8,9,14]', '[7,8]', '2026-01-21', '2026-01-20 11:17:35'),
(30, 4, 'Wednesday Lotto', '[17,18,19,22,23]', '[21,22]', '2026-01-21', '2026-01-20 11:18:04'),
(31, 4, 'Wednesday Lotto', '[10,15,20,25,30]', '[8,13]', '2026-01-21', '2026-01-20 11:19:02'),
(32, 4, 'Wednesday Lotto', '[51,52,53,54,62]', '[12,13]', '2026-01-21', '2026-01-20 11:20:04'),
(33, 4, 'Wednesday Lotto', '[18,19,23,24,28]', '[18,19]', '2026-01-21', '2026-01-20 11:20:38'),
(34, 4, 'Wednesday Lotto', '[1,2,3,4,5]', '[1,11]', '2026-01-21', '2026-01-21 16:03:47'),
(35, 4, 'Wednesday Lotto', '[5,15,25,35,45]', '[2,12]', '2026-01-21', '2026-01-21 16:04:28'),
(36, 4, 'Wednesday Lotto', '[26,27,28,29,30]', '[12,22]', '2026-01-21', '2026-01-21 16:05:01'),
(37, 4, 'Wednesday Lotto', '[33,42,43,44,45]', '[11,21]', '2026-01-21', '2026-01-21 16:07:20'),
(38, 4, 'Wednesday Lotto', '[42,43,44,45,54]', '[13,23]', '2026-01-21', '2026-01-21 16:08:02'),
(39, 4, 'Wednesday Lotto', '[33,34,35,36,37]', '[5,15]', '2026-01-21', '2026-01-21 16:09:18'),
(40, 4, 'Wednesday Lotto', '[13,23,32,42,43]', '[11,21]', '2026-01-21', '2026-01-21 16:10:01'),
(42, 4, 'Monday Lotto', '[5,6,16,26,35]', '[1,2]', '2026-01-21', '2026-01-21 16:17:39'),
(43, 4, 'Monday Lotto', '[30,40,50,59,60]', '[11,22]', '2026-01-26', '2026-01-21 16:18:17'),
(44, 4, 'Monday Lotto', '[39,48,49,58,59]', '[12,22]', '2026-01-26', '2026-01-21 16:19:00'),
(45, 4, 'Monday Lotto', '[46,56,64,65,66]', '[23,24]', '2026-01-21', '2026-01-21 16:19:37'),
(46, 4, 'Monday Lotto', '[27,36,45,54,63]', '[21,31]', '2026-01-21', '2026-01-21 16:20:29'),
(47, 4, 'Monday Lotto', '[6,15,26,35,36]', '[5,15]', '2026-01-21', '2026-01-21 16:21:45'),
(48, 4, 'Monday Lotto', '[5,6,15,25,35]', '[11,21]', '2026-01-21', '2026-01-21 16:23:04'),
(49, 4, 'Monday Lotto', '[16,27,37,46,47]', '[23,33]', '2026-01-26', '2026-01-21 16:23:40'),
(50, 4, 'Monday Lotto', '[12,13,14,15,16]', '[22,33]', '2026-01-21', '2026-01-21 16:24:31'),
(53, 4, 'Wednesday Lotto', '[1,11,12,22,32]', '[22,32]', '2026-01-21', '2026-01-21 16:48:25'),
(54, 4, 'Wednesday Lotto', '[1,2,3,4,5]', '[1,2]', '2026-01-21', '2026-01-21 16:48:57'),
(55, 4, 'Friday Lotto', '[23,33,44,54,55]', '[1,2]', '2026-01-23', '2026-01-22 14:49:33'),
(56, 4, 'Wednesday Lotto', '[11,12,13,14,15]', '[21,31]', '2026-01-28', '2026-01-22 14:50:14'),
(57, 4, 'Wednesday Lotto', '[1,11,21,31,41]', '[1,11]', '2026-01-28', '2026-01-22 14:51:32'),
(58, 4, 'Wednesday Lotto', '[16,36,44,45,46]', '[12,22]', '2026-01-28', '2026-01-22 14:52:58'),
(59, 4, 'Monday Lotto', '[32,33,34,35,36]', '[12,22]', '2026-01-26', '2026-01-22 14:53:54'),
(60, 4, 'Friday Lotto', '[28,38,48,58,68]', '[12,22]', '2026-01-23', '2026-01-22 14:55:17'),
(61, 4, 'Wednesday Lotto', '[17,18,19,20,27]', '[22,52]', '2026-01-28', '2026-01-22 14:56:14'),
(62, 4, 'Wednesday Lotto', '[1,6,8,9,11]', '[11,12]', '2026-01-28', '2026-01-22 14:57:27'),
(63, 4, 'Wednesday Lotto', '[23,27,28,33,34]', '[16,17]', '2026-01-28', '2026-01-22 14:58:04'),
(64, 4, 'Monday Lotto', '[21,22,23,27,28]', '[12,13]', '2026-01-26', '2026-01-22 14:58:39'),
(65, 4, 'Friday Lotto', '[16,17,18,21,23]', '[13,14]', '2026-01-23', '2026-01-22 14:59:38'),
(66, 4, 'Friday Lotto', '[24,25,26,27,28]', '[1,2]', '2026-01-23', '2026-01-23 16:32:54'),
(67, 4, 'Friday Lotto', '[23,24,25,26,27]', '[21,31]', '2026-01-23', '2026-01-23 16:33:33'),
(68, 4, 'Friday Lotto', '[23,24,25,26,27]', '[1,2]', '2026-01-23', '2026-01-23 16:34:29'),
(69, 4, 'Friday Lotto', '[1,7,11,13,18]', '[16,17]', '2026-01-23', '2026-01-23 16:38:57'),
(70, 4, 'Friday Lotto', '[27,28,29,30,33]', '[12,18]', '2026-01-23', '2026-01-23 16:39:42'),
(71, 4, 'Friday Lotto', '[3,4,5,8,9]', '[12,13]', '2026-01-23', '2026-01-23 16:40:37'),
(72, 4, 'Friday Lotto', '[25,30,33,34,38]', '[11,12]', '2026-01-23', '2026-01-23 16:42:46'),
(73, 4, 'Friday Lotto', '[8,13,18,23,28]', '[16,17]', '2026-01-23', '2026-01-23 16:43:34'),
(74, 4, 'Friday Lotto', '[14,18,19,23,24]', '[12,17]', '2026-01-23', '2026-01-23 16:44:11'),
(75, 4, 'Wednesday Lotto', '[8,13,18,23,28]', '[6,11]', '2026-01-28', '2026-01-23 16:45:39'),
(76, 4, 'Monday Lotto', '[3,8,13,14,39]', '[17,22]', '2026-01-26', '2026-01-23 16:46:34'),
(77, 4, 'Wednesday Lotto', '[14,18,19,23,29]', '[16,17]', '2026-01-28', '2026-01-23 16:47:13'),
(78, 4, 'Friday Lotto', '[21,22,23,26,27]', '[21,22]', '2026-01-23', '2026-01-23 16:47:53'),
(79, 4, 'Friday Lotto', '[23,27,28,29,33]', '[16,17]', '2026-01-23', '2026-01-23 16:48:22'),
(80, 4, 'Monday Lotto', '[47,48,57,58,67]', '[11,22]', '2026-01-26', '2026-01-23 16:50:59'),
(81, 4, 'Friday Lotto', '[23,33,44,45,46]', '[2,3]', '2026-01-23', '2026-01-23 16:51:44'),
(82, 4, 'Monday Lotto', '[32,33,34,35,36]', '[22,32]', '2026-01-26', '2026-01-23 16:52:13'),
(83, 4, 'Friday Lotto', '[26,34,35,36,37]', '[22,33]', '2026-01-23', '2026-01-23 16:52:56'),
(84, 4, 'Friday Lotto', '[6,16,27,37,47]', '[2,12]', '2026-01-30', '2026-01-24 08:00:54'),
(85, 4, 'Friday Lotto', '[31,32,33,34,35]', '[21,31]', '2026-01-30', '2026-01-24 08:01:34'),
(86, 4, 'Friday Lotto', '[56,58,59,65,66]', '[26,27]', '2026-01-30', '2026-01-27 20:39:41'),
(87, 4, 'Friday Lotto', '[44,45,46,47,54]', '[21,22]', '2026-01-30', '2026-01-27 20:40:21'),
(88, 4, 'Wednesday Lotto', '[34,35,36,46,47]', '[11,21]', '2026-01-28', '2026-01-27 20:40:57'),
(89, 4, 'Monday Lotto', '[36,44,45,47,56]', '[12,22]', '2026-02-02', '2026-01-27 20:41:29'),
(90, 4, 'Monday Lotto', '[37,38,46,49,50]', '[32,34]', '2026-02-02', '2026-01-27 20:42:21'),
(91, 4, 'Wednesday Lotto', '[32,33,42,43,53]', '[21,31]', '2026-01-28', '2026-01-27 20:47:19'),
(92, 4, 'Monday Lotto', '[17,18,19,20,27]', '[21,22]', '2026-02-02', '2026-01-27 20:47:57'),
(93, 4, 'Friday Lotto', '[43,48,53,54,58]', '[17,22]', '2026-01-30', '2026-01-28 20:01:20'),
(94, 4, 'Monday Lotto', '[17,18,22,23,27]', '[17,21]', '2026-02-02', '2026-01-28 20:01:55'),
(95, 4, 'Wednesday Lotto', '[18,19,20,23,28]', '[21,22]', '2026-02-04', '2026-01-28 20:02:29'),
(96, 4, 'Wednesday Lotto', '[25,30,34,35,39]', '[17,22]', '2026-02-04', '2026-01-28 20:02:59'),
(97, 4, 'Friday Lotto', '[7,8,9,12,13]', '[21,22]', '2026-01-30', '2026-01-28 20:03:30'),
(98, 4, 'Friday Lotto', '[5,10,15,20,25]', '[16,21]', '2026-01-30', '2026-01-28 20:04:09'),
(99, 4, 'Wednesday Lotto', '[9,13,17,21,23]', '[21,22]', '2026-02-04', '2026-01-28 20:04:45'),
(100, 4, 'Friday Lotto', '[29,34,38,39,43]', '[17,22]', '2026-01-30', '2026-01-28 20:05:17'),
(101, 4, 'Friday Lotto', '[20,25,29,30,34]', '[13,18]', '2026-01-30', '2026-01-28 20:05:46'),
(102, 4, 'Friday Lotto', '[22,23,27,28,33]', '[21,22]', '2026-01-30', '2026-01-28 20:06:37'),
(103, 4, 'Friday Lotto', '[18,23,28,33,38]', '[1,6]', '2026-01-30', '2026-01-28 20:07:13'),
(104, 4, 'Wednesday Lotto', '[24,28,32,33,38]', '[22,27]', '2026-02-04', '2026-01-28 20:07:46'),
(105, 4, 'Wednesday Lotto', '[38,42,43,47,48]', '[21,26]', '2026-02-04', '2026-01-28 20:08:17'),
(108, 4, 'Monday Lotto', '[33,34,45,56,57]', '[2,3]', '2026-02-02', '2026-02-01 13:34:02'),
(109, 4, 'Friday Lotto', '[20,30,40,49,50]', '[32,42]', '2026-02-06', '2026-02-01 13:34:52'),
(110, 4, 'Wednesday Lotto', '[31,32,33,34,35]', '[21,31]', '2026-02-04', '2026-02-01 13:35:31'),
(111, 4, 'Friday Lotto', '[28,38,48,49,50]', '[24,34]', '2026-02-06', '2026-02-01 13:36:28'),
(112, 4, 'Friday Lotto', '[34,35,36,37,38]', '[31,41]', '2026-02-06', '2026-02-01 13:37:39'),
(113, 4, 'Friday Lotto', '[52,53,54,55,56]', '[23,33]', '2026-02-06', '2026-02-01 13:38:43'),
(114, 4, 'Wednesday Lotto', '[37,48,55,56,57]', '[3,4]', '2026-02-04', '2026-02-02 17:28:57'),
(116, 4, 'Monday Lotto', '[45,58,59,68,69]', '[15,25]', '2026-02-09', '2026-02-04 16:39:13'),
(117, 4, 'Wednesday Lotto', '[10,13,14,25,26]', '[2,16]', '2026-02-04', '2026-02-04 16:40:23'),
(127, 3, 'Monday Lotto', '[44,45,46,47,48]', '[31,32]', '2026-02-09', '2026-02-05 21:50:13'),
(128, 3, 'Monday Lotto', '[23,24,25,26,27]', '[31,41]', '2026-02-09', '2026-02-05 21:50:53'),
(129, 3, 'Wednesday Lotto', '[60,66,67,68,69]', '[24,34]', '2026-02-11', '2026-02-05 21:59:30'),
(130, 3, 'Monday Lotto', '[43,44,45,46,47]', '[22,32]', '2026-02-09', '2026-02-05 22:00:09'),
(131, 3, 'Monday Lotto', '[34,35,36,37,38]', '[32,42]', '2026-02-09', '2026-02-05 22:00:33'),
(138, 4, 'Monday Lotto', '[33,43,46,47,48]', '[16,17]', '2026-02-09', '2026-02-07 15:06:34'),
(139, 4, 'Monday Lotto', '[32,36,42,43,48]', '[21,22]', '2026-02-09', '2026-02-07 15:07:03'),
(140, 4, 'Friday Lotto', '[2,3,4,18,27]', '[23,24]', '2026-02-13', '2026-02-07 15:07:44'),
(141, 4, 'Friday Lotto', '[22,23,28,33,38]', '[21,22]', '2026-02-13', '2026-02-07 15:08:18'),
(142, 4, 'Friday Lotto', '[25,26,27,28,29]', '[21,26]', '2026-02-13', '2026-02-07 15:08:37'),
(143, 4, 'Wednesday Lotto', '[7,13,17,18,21]', '[21,22]', '2026-02-11', '2026-02-07 15:09:04'),
(144, 4, 'Monday Lotto', '[13,18,21,22,23]', '[21,22]', '2026-02-09', '2026-02-07 15:09:34'),
(145, 4, 'Friday Lotto', '[71,72,73,74,75]', '[16,17]', '2026-02-13', '2026-02-09 07:41:14'),
(146, 3, 'Monday Lotto', '[42,43,44,45,46]', '[1,11]', '2026-02-16', '2026-02-11 13:06:29'),
(154, 4, 'Wednesday Lotto', '[1,2,3,7,21]', '[3,7]', '2026-02-18', '2026-02-18 05:54:42'),
(157, 12, 'Monday Lotto', '[14,16,28,32,52]', '[6,38]', '2026-02-23', '2026-02-22 16:48:13'),
(158, 4, 'Friday Lotto', '[1,2,12,22,32]', '[12,22]', '2026-02-27', '2026-02-22 18:09:01'),
(159, 3, 'Monday Lotto', '[2,24,32,39,61]', '[10,35]', '2026-02-23', '2026-02-22 21:22:35'),
(160, 12, 'Monday Lotto', '[2,17,22,26,29]', '[17,69]', '2026-02-23', '2026-02-23 14:27:32'),
(161, 12, 'Monday Lotto', '[3,16,42,52,59]', '[12,13]', '2026-02-23', '2026-02-23 14:27:44'),
(162, 9, 'Monday Lotto', '[8,9,13,14,15]', '[2,3]', '2026-02-23', '2026-02-23 14:28:23'),
(163, 9, 'Monday Lotto', '[1,8,13,14,18]', '[7,8]', '2026-02-23', '2026-02-23 14:28:56'),
(164, 9, 'Monday Lotto', '[12,13,14,15,16]', '[13,17]', '2026-02-23', '2026-02-23 14:29:16'),
(165, 13, 'Monday Lotto', '[18,24,28,33,38]', '[67,75]', '2026-02-23', '2026-02-23 16:02:22'),
(169, 4, 'Wednesday Lotto', '[22,23,24,25,26]', '[21,22]', '2026-02-25', '2026-02-23 18:51:32'),
(170, 4, 'Monday Lotto', '[21,22,23,24,25]', '[21,31]', '2026-03-02', '2026-02-23 18:51:57'),
(172, 4, 'Wednesday Lotto', '[3,4,5,6,17]', '[34,41]', '2026-02-25', '2026-02-24 15:17:53'),
(173, 4, 'Friday Lotto', '[38,48,58,67,68]', '[32,33]', '2026-02-27', '2026-02-24 15:18:24'),
(174, 4, 'Wednesday Lotto', '[35,36,37,39,40]', '[21,31]', '2026-02-25', '2026-02-24 15:19:29'),
(175, 14, 'Wednesday Lotto', '[10,17,29,42,49]', '[29,74]', '2026-02-25', '2026-02-24 20:33:58'),
(176, 12, 'Friday Lotto', '[6,32,49,66,69]', '[40,62]', '2026-02-27', '2026-02-25 20:55:49'),
(177, 12, 'Friday Lotto', '[19,35,39,61,64]', '[13,73]', '2026-02-27', '2026-02-25 20:56:32'),
(178, 12, 'Friday Lotto', '[35,38,40,62,70]', '[14,40]', '2026-02-27', '2026-02-27 13:53:43'),
(179, 12, 'Friday Lotto', '[8,15,17,44,71]', '[51,53]', '2026-02-27', '2026-02-27 13:53:59'),
(180, 12, 'Friday Lotto', '[11,33,34,35,61]', '[13,72]', '2026-02-27', '2026-02-27 13:54:14'),
(181, 12, 'Friday Lotto', '[9,23,35,37,40]', '[47,63]', '2026-02-27', '2026-02-27 13:54:29'),
(182, 12, 'Friday Lotto', '[15,23,31,43,65]', '[8,27]', '2026-02-27', '2026-02-27 13:55:26'),
(183, 15, 'Wednesday Lotto', '[19,24,37,53,68]', '[36,66]', '2026-03-04', '2026-03-03 12:30:03'),
(184, 15, 'Wednesday Lotto', '[15,35,54,58,66]', '[15,47]', '2026-03-04', '2026-03-03 12:31:41'),
(185, 15, 'Monday Lotto', '[29,36,44,58,63]', '[37,54]', '2026-03-09', '2026-03-04 07:47:42'),
(186, 15, 'Monday Lotto', '[15,28,43,46,55]', '[28,53]', '2026-03-09', '2026-03-04 07:48:16'),
(187, 12, 'Wednesday Lotto', '[1,6,22,39,46]', '[11,17]', '2026-03-04', '2026-03-04 15:31:34'),
(188, 12, 'Wednesday Lotto', '[24,26,27,56,61]', '[51,55]', '2026-03-04', '2026-03-04 15:31:46'),
(189, 12, 'Wednesday Lotto', '[29,31,41,54,61]', '[55,65]', '2026-03-04', '2026-03-04 15:31:57'),
(190, 12, 'Wednesday Lotto', '[36,39,41,45,53]', '[25,61]', '2026-03-04', '2026-03-04 15:32:11'),
(191, 12, 'Wednesday Lotto', '[23,25,45,56,63]', '[14,40]', '2026-03-04', '2026-03-04 15:32:58'),
(192, 12, 'Wednesday Lotto', '[18,36,37,72,74]', '[7,12]', '2026-03-04', '2026-03-04 15:33:08'),
(193, 12, 'Wednesday Lotto', '[10,12,15,40,50]', '[46,64]', '2026-03-04', '2026-03-04 15:33:20'),
(194, 12, 'Wednesday Lotto', '[8,20,28,44,64]', '[23,56]', '2026-03-04', '2026-03-04 15:33:35'),
(195, 4, 'Friday Lotto', '[44,45,55,64,65]', '[34,35]', '2026-03-06', '2026-03-05 20:04:17'),
(196, 4, 'Friday Lotto', '[53,54,64,65,66]', '[32,33]', '2026-03-06', '2026-03-05 20:04:52'),
(197, 4, 'Friday Lotto', '[13,17,18,19,23]', '[3,8]', '2026-03-06', '2026-03-05 20:12:20'),
(198, 4, 'Monday Lotto', '[13,18,23,27,28]', '[16,17]', '2026-03-09', '2026-03-05 20:12:39'),
(199, 4, 'Wednesday Lotto', '[37,38,39,40,47]', '[12,22]', '2026-03-11', '2026-03-10 08:03:42'),
(200, 12, 'Wednesday Lotto', '[10,24,44,47,54]', '[11,32]', '2026-03-11', '2026-03-11 13:41:39'),
(201, 12, 'Wednesday Lotto', '[11,16,38,44,61]', '[1,15]', '2026-03-11', '2026-03-11 13:42:05'),
(202, 3, 'Friday Lotto', '[23,34,44,54,55]', '[12,22]', '2026-03-13', '2026-03-12 11:53:03'),
(203, 4, 'Monday Lotto', '[26,36,46,56,66]', '[21,22]', '2026-03-23', '2026-03-21 05:50:29'),
(204, 4, 'Wednesday Lotto', '[36,45,55,65,74]', '[11,12]', '2026-03-25', '2026-03-21 05:50:54'),
(205, 4, 'Monday Lotto', '[22,23,28,29,33]', '[21,22]', '2026-03-23', '2026-03-21 20:45:41'),
(206, 4, 'Monday Lotto', '[31,32,33,38,39]', '[21,22]', '2026-03-23', '2026-03-21 20:46:01'),
(207, 4, 'Wednesday Lotto', '[11,12,21,23,28]', '[21,22]', '2026-03-25', '2026-03-21 20:46:28'),
(208, 4, 'Wednesday Lotto', '[18,23,24,28,29]', '[18,23]', '2026-03-25', '2026-03-24 17:40:51'),
(209, 4, 'Wednesday Lotto', '[13,18,23,24,28]', '[21,22]', '2026-03-25', '2026-03-24 17:41:07'),
(210, 4, 'Friday Lotto', '[13,23,24,28,34]', '[21,22]', '2026-03-27', '2026-03-24 17:41:27'),
(211, 4, 'Friday Lotto', '[16,22,26,31,32]', '[16,17]', '2026-03-27', '2026-03-24 17:41:46'),
(212, 4, 'Friday Lotto', '[11,12,13,17,23]', '[26,27]', '2026-03-27', '2026-03-24 17:42:05'),
(213, 16, 'Wednesday Lotto', '[13,19,29,47,56]', '[39,52]', '2026-04-01', '2026-03-29 02:08:29'),
(214, 17, 'Wednesday Lotto', '[2,17,22,36,54]', '[7,11]', '2026-04-01', '2026-03-30 18:59:36'),
(215, 17, 'Monday Lotto', '[1,5,8,12,26]', '[13,25]', '2026-04-06', '2026-03-30 19:01:00'),
(216, 12, 'Wednesday Lotto', '[17,27,28,36,57]', '[24,48]', '2026-04-01', '2026-04-01 16:39:18'),
(217, 12, 'Wednesday Lotto', '[1,13,26,54,69]', '[14,68]', '2026-04-01', '2026-04-01 16:39:32'),
(218, 18, 'Friday Lotto', '[13,17,34,42,46]', '[4,27]', '2026-04-03', '2026-04-02 19:06:51'),
(219, 18, 'Friday Lotto', '[2,15,37,52,74]', '[24,68]', '2026-04-03', '2026-04-02 19:07:42'),
(220, 18, 'Monday Lotto', '[5,22,39,54,62]', '[7,57]', '2026-04-06', '2026-04-02 19:08:15'),
(221, 18, 'Wednesday Lotto', '[22,28,46,62,68]', '[13,38]', '2026-04-08', '2026-04-02 19:08:41'),
(222, 18, 'Monday Lotto', '[17,23,38,62,66]', '[10,25]', '2026-04-06', '2026-04-02 19:09:05'),
(223, 18, 'Wednesday Lotto', '[28,34,46,62,67]', '[12,36]', '2026-04-08', '2026-04-03 09:45:21'),
(224, 18, 'Monday Lotto', '[2,19,33,46,63]', '[6,37]', '2026-04-06', '2026-04-03 09:45:52'),
(225, 18, 'Wednesday Lotto', '[15,33,49,57,64]', '[23,47]', '2026-04-08', '2026-04-03 09:46:21'),
(226, 18, 'Friday Lotto', '[3,18,25,58,62]', '[12,42]', '2026-04-03', '2026-04-03 09:46:52'),
(227, 18, 'Friday Lotto', '[3,19,25,52,66]', '[20,32]', '2026-04-03', '2026-04-03 09:47:22'),
(228, 18, 'Friday Lotto', '[2,17,21,25,41]', '[6,32]', '2026-04-03', '2026-04-03 09:47:46'),
(229, 19, 'Friday Lotto', '[10,25,55,57,58]', '[21,36]', '2026-04-03', '2026-04-03 11:44:58'),
(230, 19, 'Monday Lotto', '[19,51,55,62,64]', '[36,75]', '2026-04-06', '2026-04-03 11:48:43'),
(231, 19, 'Wednesday Lotto', '[34,37,59,69,70]', '[16,70]', '2026-04-08', '2026-04-03 11:50:27'),
(232, 19, 'Friday Lotto', '[2,20,22,66,74]', '[30,72]', '2026-04-03', '2026-04-03 11:51:34'),
(233, 19, 'Monday Lotto', '[22,56,65,66,69]', '[4,36]', '2026-04-06', '2026-04-03 11:58:53'),
(234, 19, 'Wednesday Lotto', '[50,51,58,65,72]', '[32,65]', '2026-04-08', '2026-04-03 11:59:36'),
(235, 18, 'Monday Lotto', '[7,13,28,53,66]', '[9,34]', '2026-04-06', '2026-04-04 08:47:43'),
(236, 18, 'Wednesday Lotto', '[9,27,48,52,73]', '[8,32]', '2026-04-08', '2026-04-04 08:48:11'),
(237, 18, 'Friday Lotto', '[14,17,45,59,63]', '[22,36]', '2026-04-10', '2026-04-04 08:48:35'),
(238, 18, 'Monday Lotto', '[17,33,47,63,67]', '[20,31]', '2026-04-06', '2026-04-04 08:49:03'),
(239, 18, 'Friday Lotto', '[8,22,35,47,53]', '[24,75]', '2026-04-10', '2026-04-04 08:49:29'),
(240, 18, 'Monday Lotto', '[10,15,25,45,65]', '[41,57]', '2026-04-06', '2026-04-04 08:49:57'),
(241, 18, 'Wednesday Lotto', '[8,21,37,56,61]', '[27,55]', '2026-04-08', '2026-04-04 08:50:31'),
(242, 18, 'Wednesday Lotto', '[8,34,48,64,66]', '[37,62]', '2026-04-08', '2026-04-04 08:50:58'),
(243, 18, 'Monday Lotto', '[8,25,37,42,54]', '[24,52]', '2026-04-06', '2026-04-04 08:53:54'),
(244, 19, 'Monday Lotto', '[25,26,32,58,72]', '[27,48]', '2026-04-06', '2026-04-04 09:08:38'),
(245, 19, 'Monday Lotto', '[1,15,41,52,73]', '[12,29]', '2026-04-06', '2026-04-04 09:09:21'),
(246, 19, 'Wednesday Lotto', '[21,31,36,41,58]', '[19,50]', '2026-04-08', '2026-04-04 09:09:59'),
(247, 19, 'Wednesday Lotto', '[25,32,37,38,72]', '[4,46]', '2026-04-08', '2026-04-04 09:10:36'),
(248, 19, 'Monday Lotto', '[27,41,61,69,71]', '[19,59]', '2026-04-06', '2026-04-04 09:12:49'),
(249, 19, 'Monday Lotto', '[5,6,51,59,72]', '[14,46]', '2026-04-06', '2026-04-04 09:13:24'),
(250, 19, 'Wednesday Lotto', '[6,8,25,60,61]', '[9,49]', '2026-04-08', '2026-04-04 09:14:02'),
(251, 19, 'Wednesday Lotto', '[13,28,52,68,70]', '[29,53]', '2026-04-08', '2026-04-04 09:15:26'),
(252, 19, 'Monday Lotto', '[10,17,22,53,56]', '[6,49]', '2026-04-06', '2026-04-04 09:16:24'),
(253, 19, 'Monday Lotto', '[18,56,57,58,71]', '[18,44]', '2026-04-06', '2026-04-04 09:17:13'),
(254, 19, 'Monday Lotto', '[7,16,42,61,64]', '[22,74]', '2026-04-06', '2026-04-04 09:18:12'),
(255, 19, 'Monday Lotto', '[9,19,42,61,72]', '[8,40]', '2026-04-06', '2026-04-04 09:19:39'),
(256, 19, 'Monday Lotto', '[4,12,23,33,71]', '[19,53]', '2026-04-06', '2026-04-04 09:21:43'),
(257, 19, 'Monday Lotto', '[6,12,30,36,52]', '[24,54]', '2026-04-06', '2026-04-04 09:22:27'),
(258, 19, 'Monday Lotto', '[19,23,28,32,53]', '[18,42]', '2026-04-06', '2026-04-04 09:23:08'),
(259, 19, 'Wednesday Lotto', '[4,29,41,51,71]', '[5,40]', '2026-04-08', '2026-04-04 09:24:57'),
(260, 19, 'Monday Lotto', '[5,12,30,37,40]', '[13,45]', '2026-04-06', '2026-04-04 09:25:21'),
(261, 19, 'Monday Lotto', '[14,40,49,54,60]', '[22,63]', '2026-04-06', '2026-04-04 09:26:28'),
(262, 19, 'Monday Lotto', '[7,10,29,43,74]', '[12,69]', '2026-04-06', '2026-04-04 09:28:07'),
(263, 19, 'Monday Lotto', '[5,8,16,25,51]', '[36,53]', '2026-04-06', '2026-04-04 17:56:41'),
(264, 19, 'Monday Lotto', '[34,42,54,61,64]', '[19,69]', '2026-04-06', '2026-04-04 17:57:11'),
(265, 19, 'Monday Lotto', '[24,51,61,65,66]', '[20,28]', '2026-04-06', '2026-04-04 17:58:55'),
(266, 19, 'Monday Lotto', '[4,20,36,38,60]', '[1,64]', '2026-04-06', '2026-04-04 17:59:17'),
(267, 19, 'Monday Lotto', '[30,37,41,55,71]', '[48,57]', '2026-04-06', '2026-04-04 17:59:43'),
(268, 19, 'Monday Lotto', '[2,8,24,30,33]', '[7,37]', '2026-04-06', '2026-04-04 18:00:08'),
(269, 19, 'Monday Lotto', '[22,36,47,53,69]', '[16,68]', '2026-04-06', '2026-04-04 18:00:31'),
(270, 19, 'Monday Lotto', '[5,31,40,67,70]', '[25,73]', '2026-04-06', '2026-04-04 18:00:55'),
(271, 19, 'Monday Lotto', '[12,19,23,42,54]', '[10,44]', '2026-04-06', '2026-04-04 18:01:19'),
(272, 19, 'Monday Lotto', '[2,16,44,50,62]', '[46,48]', '2026-04-06', '2026-04-04 18:06:37'),
(273, 19, 'Monday Lotto', '[7,47,48,52,64]', '[26,48]', '2026-04-06', '2026-04-05 08:54:56'),
(274, 19, 'Monday Lotto', '[2,24,49,59,62]', '[25,39]', '2026-04-06', '2026-04-05 08:55:59'),
(275, 19, 'Monday Lotto', '[2,24,49,59,62]', '[25,39]', '2026-04-06', '2026-04-05 08:56:04'),
(276, 19, 'Monday Lotto', '[5,18,31,55,75]', '[13,59]', '2026-04-06', '2026-04-05 08:56:45'),
(277, 19, 'Monday Lotto', '[14,38,47,72,73]', '[9,50]', '2026-04-06', '2026-04-05 08:57:21'),
(278, 18, 'Monday Lotto', '[17,24,47,53,74]', '[7,33]', '2026-04-06', '2026-04-05 08:58:03'),
(279, 19, 'Monday Lotto', '[11,32,40,50,51]', '[13,73]', '2026-04-06', '2026-04-05 08:58:15'),
(280, 18, 'Monday Lotto', '[3,24,36,59,63]', '[5,45]', '2026-04-06', '2026-04-05 08:58:35'),
(281, 19, 'Monday Lotto', '[11,32,40,50,51]', '[13,73]', '2026-04-06', '2026-04-05 08:58:36'),
(282, 18, 'Wednesday Lotto', '[7,26,44,62,67]', '[36,72]', '2026-04-08', '2026-04-05 08:59:08'),
(283, 19, 'Monday Lotto', '[11,32,40,50,51]', '[13,73]', '2026-04-06', '2026-04-05 08:59:23'),
(284, 18, 'Friday Lotto', '[7,32,36,63,67]', '[9,33]', '2026-04-10', '2026-04-05 08:59:34'),
(285, 18, 'Monday Lotto', '[4,10,34,38,64]', '[2,33]', '2026-04-06', '2026-04-05 08:59:56'),
(286, 19, 'Monday Lotto', '[6,24,54,69,74]', '[15,27]', '2026-04-06', '2026-04-05 09:00:15'),
(287, 18, 'Monday Lotto', '[5,27,33,55,58]', '[7,22]', '2026-04-06', '2026-04-05 09:00:47'),
(288, 19, 'Monday Lotto', '[4,23,28,57,69]', '[22,62]', '2026-04-06', '2026-04-05 09:01:03'),
(289, 19, 'Monday Lotto', '[4,17,27,50,69]', '[49,75]', '2026-04-06', '2026-04-05 09:01:51'),
(290, 19, 'Monday Lotto', '[15,20,21,45,72]', '[36,72]', '2026-04-06', '2026-04-05 09:02:18'),
(291, 19, 'Monday Lotto', '[16,17,30,52,59]', '[14,40]', '2026-04-06', '2026-04-05 09:02:58'),
(292, 19, 'Monday Lotto', '[30,43,45,67,73]', '[41,64]', '2026-04-06', '2026-04-05 09:03:33'),
(293, 19, 'Monday Lotto', '[9,10,24,47,56]', '[6,54]', '2026-04-06', '2026-04-05 09:04:21'),
(294, 19, 'Monday Lotto', '[50,58,67,69,71]', '[28,72]', '2026-04-06', '2026-04-05 09:04:45'),
(295, 19, 'Monday Lotto', '[50,58,67,69,71]', '[28,72]', '2026-04-06', '2026-04-05 09:05:06'),
(296, 19, 'Monday Lotto', '[50,58,67,69,71]', '[28,72]', '2026-04-06', '2026-04-05 09:05:16'),
(297, 19, 'Monday Lotto', '[12,18,56,58,64]', '[21,52]', '2026-04-06', '2026-04-05 09:05:44'),
(298, 19, 'Monday Lotto', '[18,36,40,49,71]', '[2,48]', '2026-04-06', '2026-04-05 09:06:59'),
(299, 19, 'Monday Lotto', '[18,36,40,49,71]', '[2,48]', '2026-04-06', '2026-04-05 09:07:27'),
(300, 19, 'Monday Lotto', '[20,22,39,51,61]', '[49,74]', '2026-04-06', '2026-04-05 09:08:25'),
(301, 19, 'Monday Lotto', '[7,16,18,45,60]', '[40,54]', '2026-04-06', '2026-04-05 09:08:49'),
(302, 19, 'Monday Lotto', '[2,17,25,41,51]', '[41,68]', '2026-04-06', '2026-04-05 09:09:26'),
(303, 19, 'Monday Lotto', '[25,29,34,57,63]', '[20,51]', '2026-04-06', '2026-04-05 09:09:57'),
(304, 19, 'Monday Lotto', '[13,20,41,44,58]', '[6,30]', '2026-04-06', '2026-04-05 09:10:25'),
(305, 19, 'Monday Lotto', '[29,33,43,52,71]', '[40,69]', '2026-04-06', '2026-04-05 09:10:52'),
(306, 19, 'Monday Lotto', '[14,35,47,63,75]', '[7,28]', '2026-04-06', '2026-04-05 09:11:30'),
(307, 19, 'Monday Lotto', '[36,40,52,56,59]', '[12,71]', '2026-04-06', '2026-04-05 09:12:09'),
(308, 19, 'Monday Lotto', '[12,23,33,62,69]', '[18,23]', '2026-04-06', '2026-04-05 09:12:34'),
(309, 19, 'Monday Lotto', '[55,62,67,69,70]', '[55,72]', '2026-04-06', '2026-04-05 09:13:20'),
(310, 19, 'Monday Lotto', '[17,31,44,56,68]', '[48,50]', '2026-04-06', '2026-04-05 09:14:03'),
(311, 19, 'Monday Lotto', '[15,22,41,44,45]', '[4,7]', '2026-04-06', '2026-04-05 09:14:42'),
(312, 19, 'Monday Lotto', '[3,18,67,70,72]', '[32,33]', '2026-04-06', '2026-04-05 09:15:12'),
(313, 19, 'Monday Lotto', '[1,12,22,38,71]', '[7,10]', '2026-04-06', '2026-04-05 09:15:49'),
(314, 19, 'Monday Lotto', '[16,24,51,57,73]', '[5,57]', '2026-04-06', '2026-04-05 10:01:00'),
(315, 19, 'Monday Lotto', '[7,33,57,58,64]', '[14,73]', '2026-04-06', '2026-04-05 10:01:19'),
(316, 19, 'Monday Lotto', '[12,27,41,56,67]', '[54,67]', '2026-04-06', '2026-04-05 10:01:39'),
(317, 19, 'Monday Lotto', '[13,31,34,52,53]', '[59,61]', '2026-04-06', '2026-04-05 10:01:59'),
(318, 19, 'Monday Lotto', '[35,40,47,50,56]', '[4,13]', '2026-04-06', '2026-04-05 10:02:31'),
(319, 19, 'Monday Lotto', '[29,34,56,59,70]', '[11,43]', '2026-04-06', '2026-04-05 10:03:19'),
(320, 19, 'Monday Lotto', '[20,23,32,58,72]', '[16,42]', '2026-04-06', '2026-04-05 10:03:39'),
(321, 19, 'Monday Lotto', '[43,44,48,69,73]', '[32,56]', '2026-04-06', '2026-04-05 10:04:03'),
(322, 19, 'Monday Lotto', '[15,21,30,41,55]', '[11,71]', '2026-04-06', '2026-04-05 10:04:24'),
(323, 19, 'Monday Lotto', '[10,17,36,50,61]', '[44,63]', '2026-04-06', '2026-04-05 10:04:45'),
(324, 19, 'Monday Lotto', '[17,51,52,57,66]', '[57,60]', '2026-04-06', '2026-04-05 10:05:08'),
(325, 19, 'Monday Lotto', '[4,21,48,55,62]', '[21,46]', '2026-04-06', '2026-04-05 10:05:27'),
(326, 19, 'Monday Lotto', '[3,25,40,46,49]', '[33,61]', '2026-04-06', '2026-04-05 10:05:48'),
(327, 19, 'Monday Lotto', '[33,54,61,64,67]', '[43,65]', '2026-04-06', '2026-04-05 10:06:12'),
(328, 19, 'Monday Lotto', '[16,17,18,24,41]', '[5,65]', '2026-04-06', '2026-04-05 10:06:33'),
(329, 19, 'Monday Lotto', '[7,14,24,50,55]', '[65,67]', '2026-04-06', '2026-04-05 10:06:56'),
(330, 19, 'Monday Lotto', '[12,43,51,58,73]', '[8,37]', '2026-04-06', '2026-04-05 10:07:30'),
(331, 19, 'Monday Lotto', '[38,40,59,65,71]', '[4,14]', '2026-04-06', '2026-04-05 10:07:56'),
(332, 19, 'Monday Lotto', '[4,18,24,40,68]', '[7,64]', '2026-04-06', '2026-04-05 10:08:32'),
(333, 19, 'Monday Lotto', '[25,44,46,69,74]', '[61,73]', '2026-04-06', '2026-04-05 10:08:56'),
(334, 19, 'Monday Lotto', '[1,18,39,41,62]', '[1,38]', '2026-04-06', '2026-04-05 10:09:18'),
(335, 19, 'Monday Lotto', '[19,20,24,25,35]', '[8,62]', '2026-04-06', '2026-04-05 10:09:50'),
(336, 19, 'Monday Lotto', '[16,27,41,44,63]', '[46,51]', '2026-04-06', '2026-04-05 13:57:33'),
(337, 19, 'Monday Lotto', '[5,26,42,52,56]', '[19,49]', '2026-04-06', '2026-04-05 13:57:58'),
(338, 19, 'Monday Lotto', '[18,19,26,62,72]', '[2,49]', '2026-04-06', '2026-04-05 13:58:22'),
(339, 19, 'Monday Lotto', '[6,36,59,60,73]', '[9,13]', '2026-04-06', '2026-04-05 13:58:41'),
(340, 19, 'Monday Lotto', '[9,18,19,42,69]', '[31,64]', '2026-04-06', '2026-04-05 13:59:10'),
(341, 19, 'Monday Lotto', '[17,31,34,53,75]', '[3,11]', '2026-04-06', '2026-04-05 13:59:31'),
(342, 19, 'Monday Lotto', '[12,13,16,22,58]', '[41,43]', '2026-04-06', '2026-04-05 13:59:49'),
(343, 19, 'Monday Lotto', '[10,25,26,32,73]', '[26,73]', '2026-04-06', '2026-04-05 14:00:13'),
(344, 19, 'Wednesday Lotto', '[3,54,61,69,70]', '[35,59]', '2026-04-08', '2026-04-06 03:41:41'),
(345, 19, 'Monday Lotto', '[4,17,47,55,61]', '[31,65]', '2026-04-06', '2026-04-06 03:42:21'),
(346, 19, 'Monday Lotto', '[13,19,44,62,71]', '[60,66]', '2026-04-06', '2026-04-06 03:42:54'),
(347, 19, 'Monday Lotto', '[4,22,37,41,75]', '[8,67]', '2026-04-06', '2026-04-06 03:43:21'),
(348, 19, 'Monday Lotto', '[21,40,46,51,52]', '[22,62]', '2026-04-06', '2026-04-06 03:44:00'),
(349, 19, 'Monday Lotto', '[22,39,51,58,65]', '[19,64]', '2026-04-06', '2026-04-06 03:44:29'),
(350, 19, 'Monday Lotto', '[6,15,29,58,63]', '[24,62]', '2026-04-06', '2026-04-06 03:44:53'),
(351, 19, 'Monday Lotto', '[8,35,45,59,71]', '[23,61]', '2026-04-06', '2026-04-06 03:45:41'),
(352, 19, 'Monday Lotto', '[8,35,45,59,71]', '[23,61]', '2026-04-06', '2026-04-06 03:45:55'),
(353, 19, 'Monday Lotto', '[5,53,58,64,67]', '[10,35]', '2026-04-06', '2026-04-06 04:55:02'),
(354, 19, 'Monday Lotto', '[8,11,23,32,38]', '[15,51]', '2026-04-06', '2026-04-06 04:55:26'),
(355, 19, 'Monday Lotto', '[17,42,53,57,72]', '[9,23]', '2026-04-06', '2026-04-06 04:55:48'),
(356, 19, 'Monday Lotto', '[8,22,38,65,68]', '[14,29]', '2026-04-06', '2026-04-06 04:56:17'),
(357, 19, 'Monday Lotto', '[4,24,30,39,51]', '[46,49]', '2026-04-06', '2026-04-06 04:56:40'),
(358, 19, 'Monday Lotto', '[2,4,11,15,68]', '[22,52]', '2026-04-06', '2026-04-06 04:56:59'),
(359, 19, 'Monday Lotto', '[9,13,25,49,57]', '[17,21]', '2026-04-06', '2026-04-06 04:57:38'),
(360, 19, 'Monday Lotto', '[24,25,41,42,62]', '[7,53]', '2026-04-06', '2026-04-06 04:58:13'),
(361, 19, 'Monday Lotto', '[6,22,26,29,44]', '[1,62]', '2026-04-06', '2026-04-06 04:59:00'),
(362, 19, 'Monday Lotto', '[6,22,26,29,44]', '[1,62]', '2026-04-06', '2026-04-06 04:59:04'),
(363, 19, 'Monday Lotto', '[6,8,17,20,43]', '[39,63]', '2026-04-06', '2026-04-06 04:59:31'),
(364, 18, 'Friday Lotto', '[28,35,24,43,65]', '[25,16]', '2026-04-10', '2026-04-06 08:33:29'),
(365, 18, 'Monday Lotto', '[15,44,33,74,38]', '[34,2]', '2026-04-06', '2026-04-06 08:33:57'),
(366, 19, 'Monday Lotto', '[10,29,46,52,60]', '[19,43]', '2026-04-06', '2026-04-06 09:02:59'),
(367, 19, 'Monday Lotto', '[20,44,47,48,70]', '[39,73]', '2026-04-06', '2026-04-06 09:03:28'),
(368, 19, 'Monday Lotto', '[6,35,38,68,72]', '[16,47]', '2026-04-06', '2026-04-06 09:04:07'),
(369, 19, 'Monday Lotto', '[2,12,16,37,40]', '[11,25]', '2026-04-06', '2026-04-06 09:04:32'),
(370, 19, 'Monday Lotto', '[30,62,65,69,72]', '[33,51]', '2026-04-06', '2026-04-06 09:05:01'),
(371, 19, 'Monday Lotto', '[7,10,54,55,74]', '[32,33]', '2026-04-06', '2026-04-06 09:05:35'),
(372, 19, 'Monday Lotto', '[15,21,45,52,55]', '[8,13]', '2026-04-06', '2026-04-06 09:15:20'),
(373, 19, 'Monday Lotto', '[7,12,17,34,63]', '[14,30]', '2026-04-06', '2026-04-06 09:19:00'),
(374, 19, 'Monday Lotto', '[27,28,59,61,66]', '[10,24]', '2026-04-06', '2026-04-06 09:19:41'),
(375, 19, 'Monday Lotto', '[2,34,40,64,71]', '[13,58]', '2026-04-06', '2026-04-06 09:20:10'),
(376, 19, 'Monday Lotto', '[3,5,33,34,48]', '[30,32]', '2026-04-06', '2026-04-06 09:20:45'),
(377, 19, 'Monday Lotto', '[7,26,27,47,55]', '[4,30]', '2026-04-06', '2026-04-06 09:21:08'),
(378, 19, 'Monday Lotto', '[5,17,30,59,69]', '[13,71]', '2026-04-06', '2026-04-06 09:21:30'),
(379, 19, 'Monday Lotto', '[3,27,36,48,74]', '[29,68]', '2026-04-06', '2026-04-06 09:22:30'),
(380, 19, 'Monday Lotto', '[1,10,17,41,48]', '[7,30]', '2026-04-06', '2026-04-06 09:24:18'),
(381, 19, 'Monday Lotto', '[16,30,31,35,47]', '[3,36]', '2026-04-06', '2026-04-06 09:25:09'),
(382, 19, 'Monday Lotto', '[8,18,50,56,68]', '[2,23]', '2026-04-06', '2026-04-06 09:25:38'),
(383, 19, 'Monday Lotto', '[6,45,48,59,69]', '[38,63]', '2026-04-06', '2026-04-06 09:26:15'),
(384, 19, 'Monday Lotto', '[9,11,26,41,67]', '[30,42]', '2026-04-06', '2026-04-06 09:26:37'),
(385, 19, 'Wednesday Lotto', '[17,36,43,58,68]', '[45,65]', '2026-04-08', '2026-04-06 09:27:32'),
(386, 19, 'Monday Lotto', '[5,7,20,48,70]', '[10,44]', '2026-04-06', '2026-04-06 09:27:56'),
(387, 19, 'Monday Lotto', '[24,37,43,44,62]', '[54,63]', '2026-04-06', '2026-04-06 09:28:40'),
(388, 19, 'Monday Lotto', '[14,32,49,51,54]', '[57,65]', '2026-04-06', '2026-04-06 09:29:19'),
(389, 19, 'Monday Lotto', '[26,61,62,69,74]', '[18,52]', '2026-04-06', '2026-04-06 09:29:47'),
(390, 19, 'Monday Lotto', '[11,26,44,47,68]', '[25,69]', '2026-04-06', '2026-04-06 09:31:02'),
(391, 19, 'Monday Lotto', '[12,51,55,63,71]', '[53,69]', '2026-04-06', '2026-04-06 09:34:14'),
(392, 19, 'Monday Lotto', '[25,27,55,64,75]', '[68,70]', '2026-04-06', '2026-04-06 09:35:02'),
(393, 19, 'Monday Lotto', '[19,23,58,59,68]', '[61,72]', '2026-04-06', '2026-04-06 09:35:29'),
(394, 19, 'Monday Lotto', '[15,17,30,34,48]', '[47,52]', '2026-04-06', '2026-04-06 09:35:54'),
(395, 19, 'Monday Lotto', '[15,34,53,56,59]', '[27,33]', '2026-04-06', '2026-04-06 09:36:20'),
(396, 19, 'Monday Lotto', '[2,14,30,48,67]', '[15,58]', '2026-04-06', '2026-04-06 09:36:52'),
(397, 19, 'Wednesday Lotto', '[4,11,20,50,60]', '[2,35]', '2026-04-08', '2026-04-07 08:45:28'),
(398, 19, 'Wednesday Lotto', '[19,24,28,49,74]', '[20,33]', '2026-04-08', '2026-04-07 08:50:54'),
(399, 19, 'Wednesday Lotto', '[19,24,28,49,74]', '[20,33]', '2026-04-08', '2026-04-07 08:51:07'),
(400, 19, 'Wednesday Lotto', '[26,34,38,64,73]', '[13,69]', '2026-04-08', '2026-04-07 08:51:41'),
(401, 19, 'Wednesday Lotto', '[15,32,44,64,72]', '[2,54]', '2026-04-08', '2026-04-07 08:52:20'),
(402, 19, 'Wednesday Lotto', '[6,17,31,53,68]', '[63,71]', '2026-04-08', '2026-04-07 08:52:52'),
(403, 19, 'Wednesday Lotto', '[4,16,21,51,65]', '[11,46]', '2026-04-08', '2026-04-08 09:37:11'),
(404, 19, 'Wednesday Lotto', '[4,7,30,54,60]', '[2,58]', '2026-04-08', '2026-04-08 09:37:49'),
(405, 19, 'Wednesday Lotto', '[2,27,42,51,70]', '[18,37]', '2026-04-08', '2026-04-08 09:38:19'),
(406, 19, 'Wednesday Lotto', '[2,10,19,57,70]', '[7,42]', '2026-04-08', '2026-04-08 09:38:51'),
(407, 19, 'Wednesday Lotto', '[11,14,27,45,55]', '[49,63]', '2026-04-08', '2026-04-08 09:39:46'),
(408, 19, 'Wednesday Lotto', '[12,25,31,38,67]', '[35,75]', '2026-04-08', '2026-04-08 09:40:28'),
(409, 19, 'Wednesday Lotto', '[17,31,45,55,74]', '[3,52]', '2026-04-08', '2026-04-08 09:40:54'),
(410, 19, 'Wednesday Lotto', '[10,22,33,57,68]', '[54,74]', '2026-04-08', '2026-04-08 09:41:49'),
(411, 19, 'Wednesday Lotto', '[6,13,33,35,68]', '[16,42]', '2026-04-08', '2026-04-08 09:42:31'),
(412, 19, 'Wednesday Lotto', '[19,25,37,42,71]', '[11,18]', '2026-04-08', '2026-04-08 09:42:55'),
(413, 19, 'Wednesday Lotto', '[36,42,53,64,67]', '[34,72]', '2026-04-08', '2026-04-08 09:43:20'),
(414, 19, 'Wednesday Lotto', '[22,38,52,59,68]', '[5,73]', '2026-04-08', '2026-04-08 09:43:47'),
(415, 19, 'Wednesday Lotto', '[1,20,29,56,60]', '[33,34]', '2026-04-08', '2026-04-08 09:44:18'),
(416, 19, 'Wednesday Lotto', '[1,5,15,37,38]', '[12,61]', '2026-04-08', '2026-04-08 09:44:46'),
(417, 19, 'Wednesday Lotto', '[7,19,35,43,55]', '[39,50]', '2026-04-08', '2026-04-08 09:45:18'),
(418, 19, 'Wednesday Lotto', '[6,21,25,60,67]', '[24,54]', '2026-04-08', '2026-04-08 09:50:56'),
(419, 19, 'Wednesday Lotto', '[12,25,29,55,58]', '[13,20]', '2026-04-08', '2026-04-08 09:51:21'),
(420, 19, 'Wednesday Lotto', '[1,13,25,30,64]', '[47,54]', '2026-04-08', '2026-04-08 09:51:52'),
(421, 19, 'Wednesday Lotto', '[14,25,54,65,67]', '[35,38]', '2026-04-08', '2026-04-08 09:52:52'),
(422, 19, 'Wednesday Lotto', '[21,38,41,60,72]', '[47,67]', '2026-04-08', '2026-04-08 09:53:27'),
(423, 19, 'Wednesday Lotto', '[16,35,41,62,68]', '[26,38]', '2026-04-08', '2026-04-08 09:54:07'),
(424, 19, 'Wednesday Lotto', '[22,36,39,42,48]', '[10,29]', '2026-04-08', '2026-04-08 09:54:31'),
(425, 19, 'Wednesday Lotto', '[2,12,40,68,75]', '[13,49]', '2026-04-08', '2026-04-08 09:54:55'),
(426, 19, 'Wednesday Lotto', '[5,6,44,52,63]', '[19,39]', '2026-04-08', '2026-04-08 09:55:24'),
(427, 19, 'Wednesday Lotto', '[8,42,44,46,59]', '[36,47]', '2026-04-08', '2026-04-08 09:56:15'),
(428, 19, 'Wednesday Lotto', '[8,12,21,26,74]', '[11,65]', '2026-04-08', '2026-04-08 09:57:06'),
(429, 19, 'Wednesday Lotto', '[8,19,21,22,46]', '[26,65]', '2026-04-08', '2026-04-08 09:57:41'),
(430, 19, 'Wednesday Lotto', '[5,18,32,35,62]', '[16,19]', '2026-04-08', '2026-04-08 09:58:08'),
(431, 19, 'Wednesday Lotto', '[1,17,23,39,70]', '[40,65]', '2026-04-08', '2026-04-08 09:58:37'),
(432, 19, 'Wednesday Lotto', '[1,6,13,64,70]', '[2,18]', '2026-04-08', '2026-04-08 10:00:21'),
(433, 19, 'Wednesday Lotto', '[27,31,38,46,70]', '[26,29]', '2026-04-08', '2026-04-08 10:00:49'),
(434, 19, 'Wednesday Lotto', '[11,23,70,73,74]', '[52,59]', '2026-04-08', '2026-04-08 10:01:17'),
(435, 19, 'Wednesday Lotto', '[3,31,45,66,73]', '[32,59]', '2026-04-08', '2026-04-08 10:02:01'),
(436, 19, 'Wednesday Lotto', '[10,22,39,46,73]', '[45,47]', '2026-04-08', '2026-04-08 10:02:39'),
(437, 19, 'Wednesday Lotto', '[4,5,17,21,28]', '[60,72]', '2026-04-08', '2026-04-08 10:03:27'),
(438, 19, 'Friday Lotto', '[7,10,41,65,74]', '[9,64]', '2026-04-10', '2026-04-09 04:36:12'),
(439, 19, 'Friday Lotto', '[22,53,57,60,64]', '[35,65]', '2026-04-10', '2026-04-09 04:37:52'),
(440, 19, 'Friday Lotto', '[2,13,31,33,66]', '[32,65]', '2026-04-10', '2026-04-09 04:38:22'),
(441, 19, 'Friday Lotto', '[26,30,41,60,61]', '[21,64]', '2026-04-10', '2026-04-09 04:38:45'),
(442, 19, 'Friday Lotto', '[1,26,32,41,64]', '[7,65]', '2026-04-10', '2026-04-09 04:39:16'),
(443, 19, 'Friday Lotto', '[18,22,34,54,64]', '[39,72]', '2026-04-10', '2026-04-09 04:39:42'),
(444, 19, 'Friday Lotto', '[28,30,32,41,59]', '[12,24]', '2026-04-10', '2026-04-09 04:40:14'),
(445, 19, 'Friday Lotto', '[17,27,34,41,71]', '[14,16]', '2026-04-10', '2026-04-09 04:40:54'),
(446, 19, 'Friday Lotto', '[24,25,45,56,69]', '[44,71]', '2026-04-10', '2026-04-09 05:05:18'),
(447, 19, 'Friday Lotto', '[25,31,41,74,75]', '[34,54]', '2026-04-10', '2026-04-09 05:07:13'),
(448, 19, 'Friday Lotto', '[3,19,25,48,57]', '[1,62]', '2026-04-10', '2026-04-09 05:07:54'),
(449, 19, 'Friday Lotto', '[6,34,42,57,62]', '[24,44]', '2026-04-10', '2026-04-09 05:08:33'),
(450, 19, 'Friday Lotto', '[8,24,29,32,68]', '[37,69]', '2026-04-10', '2026-04-09 05:09:08'),
(451, 19, 'Friday Lotto', '[5,7,21,35,52]', '[42,55]', '2026-04-10', '2026-04-09 05:09:46'),
(452, 19, 'Friday Lotto', '[2,51,53,58,61]', '[5,49]', '2026-04-10', '2026-04-09 05:10:15'),
(453, 19, 'Friday Lotto', '[9,36,38,43,45]', '[26,53]', '2026-04-10', '2026-04-09 05:10:51'),
(454, 19, 'Friday Lotto', '[21,42,56,68,75]', '[57,71]', '2026-04-10', '2026-04-09 05:11:25'),
(455, 19, 'Friday Lotto', '[19,64,66,68,72]', '[44,57]', '2026-04-10', '2026-04-09 05:11:56'),
(456, 19, 'Friday Lotto', '[11,38,46,51,66]', '[45,74]', '2026-04-10', '2026-04-09 05:12:26'),
(457, 19, 'Friday Lotto', '[22,24,38,45,69]', '[17,25]', '2026-04-10', '2026-04-09 05:12:54'),
(458, 19, 'Friday Lotto', '[18,23,25,44,68]', '[28,61]', '2026-04-10', '2026-04-09 05:16:44'),
(459, 19, 'Friday Lotto', '[4,12,44,58,61]', '[11,32]', '2026-04-10', '2026-04-09 05:17:15'),
(460, 19, 'Friday Lotto', '[24,32,63,65,75]', '[25,60]', '2026-04-10', '2026-04-09 05:17:49'),
(461, 19, 'Friday Lotto', '[3,7,27,45,53]', '[5,58]', '2026-04-10', '2026-04-09 05:18:30'),
(462, 19, 'Friday Lotto', '[12,26,34,57,59]', '[18,70]', '2026-04-10', '2026-04-09 05:18:54'),
(463, 19, 'Friday Lotto', '[5,8,13,30,33]', '[7,59]', '2026-04-10', '2026-04-09 05:19:22'),
(464, 19, 'Friday Lotto', '[12,32,55,63,72]', '[34,47]', '2026-04-10', '2026-04-09 05:22:38'),
(465, 19, 'Friday Lotto', '[8,32,45,62,72]', '[43,74]', '2026-04-10', '2026-04-09 14:41:16'),
(466, 19, 'Friday Lotto', '[11,14,44,54,70]', '[50,53]', '2026-04-10', '2026-04-10 02:41:27'),
(467, 19, 'Friday Lotto', '[7,17,47,57,62]', '[53,71]', '2026-04-10', '2026-04-10 02:41:56'),
(468, 19, 'Friday Lotto', '[16,29,45,47,75]', '[38,72]', '2026-04-10', '2026-04-10 02:42:18'),
(469, 19, 'Friday Lotto', '[1,3,44,45,71]', '[27,64]', '2026-04-10', '2026-04-10 02:42:40'),
(470, 19, 'Friday Lotto', '[21,30,47,57,71]', '[13,75]', '2026-04-10', '2026-04-10 02:42:58'),
(471, 19, 'Friday Lotto', '[24,35,48,65,71]', '[1,23]', '2026-04-10', '2026-04-10 02:43:21'),
(472, 19, 'Friday Lotto', '[5,20,33,50,62]', '[57,68]', '2026-04-10', '2026-04-10 02:43:47'),
(473, 19, 'Friday Lotto', '[21,45,49,67,74]', '[9,62]', '2026-04-10', '2026-04-10 02:44:14'),
(474, 19, 'Friday Lotto', '[23,34,38,70,75]', '[42,49]', '2026-04-10', '2026-04-10 02:44:37'),
(475, 19, 'Friday Lotto', '[3,49,59,60,68]', '[8,72]', '2026-04-10', '2026-04-10 02:45:05'),
(476, 19, 'Friday Lotto', '[39,49,54,70,72]', '[9,26]', '2026-04-10', '2026-04-10 02:45:24'),
(477, 19, 'Friday Lotto', '[11,19,53,55,67]', '[17,56]', '2026-04-10', '2026-04-10 02:45:45'),
(478, 19, 'Friday Lotto', '[15,28,39,45,73]', '[23,31]', '2026-04-10', '2026-04-10 02:46:12'),
(479, 19, 'Friday Lotto', '[6,10,31,52,75]', '[9,17]', '2026-04-10', '2026-04-10 02:46:33'),
(480, 19, 'Friday Lotto', '[4,22,52,62,66]', '[2,63]', '2026-04-10', '2026-04-10 02:46:56'),
(481, 19, 'Friday Lotto', '[1,20,38,48,62]', '[16,21]', '2026-04-10', '2026-04-10 02:48:04'),
(482, 19, 'Friday Lotto', '[10,11,32,35,50]', '[30,58]', '2026-04-10', '2026-04-10 02:48:31'),
(483, 19, 'Friday Lotto', '[26,32,69,73,74]', '[3,48]', '2026-04-10', '2026-04-10 02:48:57'),
(484, 19, 'Friday Lotto', '[9,31,42,60,62]', '[56,73]', '2026-04-10', '2026-04-10 02:49:22'),
(485, 19, 'Friday Lotto', '[33,35,47,52,58]', '[7,71]', '2026-04-10', '2026-04-10 02:49:45'),
(486, 19, 'Friday Lotto', '[3,4,35,42,66]', '[2,33]', '2026-04-10', '2026-04-10 02:50:09'),
(487, 19, 'Friday Lotto', '[13,29,47,57,63]', '[34,60]', '2026-04-10', '2026-04-10 02:50:31'),
(488, 19, 'Friday Lotto', '[13,28,34,51,64]', '[5,44]', '2026-04-10', '2026-04-10 02:50:57'),
(489, 19, 'Friday Lotto', '[9,15,21,35,74]', '[32,39]', '2026-04-10', '2026-04-10 02:51:29'),
(490, 19, 'Friday Lotto', '[18,27,28,31,58]', '[38,45]', '2026-04-10', '2026-04-10 02:51:52'),
(491, 19, 'Friday Lotto', '[1,6,40,49,74]', '[35,65]', '2026-04-10', '2026-04-10 02:52:13'),
(492, 19, 'Friday Lotto', '[23,24,28,38,47]', '[13,25]', '2026-04-10', '2026-04-10 02:52:33'),
(493, 19, 'Friday Lotto', '[37,46,54,59,70]', '[18,33]', '2026-04-10', '2026-04-10 02:53:02'),
(494, 19, 'Friday Lotto', '[12,14,21,42,59]', '[25,58]', '2026-04-10', '2026-04-10 02:53:23'),
(495, 19, 'Friday Lotto', '[1,5,34,35,63]', '[18,45]', '2026-04-10', '2026-04-10 02:53:48'),
(496, 19, 'Friday Lotto', '[9,33,38,46,47]', '[25,29]', '2026-04-10', '2026-04-10 02:54:12'),
(497, 19, 'Friday Lotto', '[16,25,32,34,57]', '[2,20]', '2026-04-10', '2026-04-10 02:54:34'),
(498, 19, 'Friday Lotto', '[9,12,37,39,60]', '[40,48]', '2026-04-10', '2026-04-10 02:55:00'),
(499, 19, 'Friday Lotto', '[15,18,22,25,41]', '[44,73]', '2026-04-10', '2026-04-10 02:55:22'),
(500, 19, 'Friday Lotto', '[15,27,39,69,72]', '[24,43]', '2026-04-10', '2026-04-10 02:55:42'),
(501, 19, 'Friday Lotto', '[20,32,37,39,54]', '[67,68]', '2026-04-10', '2026-04-10 02:56:06'),
(502, 19, 'Friday Lotto', '[21,26,36,49,74]', '[39,56]', '2026-04-10', '2026-04-10 02:56:27'),
(503, 19, 'Friday Lotto', '[23,29,50,51,53]', '[15,75]', '2026-04-10', '2026-04-10 02:56:46'),
(504, 19, 'Friday Lotto', '[21,27,52,54,62]', '[46,47]', '2026-04-10', '2026-04-10 02:57:06'),
(505, 19, 'Friday Lotto', '[5,20,46,50,53]', '[48,65]', '2026-04-10', '2026-04-10 02:57:27'),
(506, 19, 'Friday Lotto', '[10,23,39,66,71]', '[17,19]', '2026-04-10', '2026-04-10 02:57:47'),
(507, 19, 'Friday Lotto', '[4,14,41,45,50]', '[43,71]', '2026-04-10', '2026-04-10 02:58:07'),
(508, 19, 'Friday Lotto', '[4,17,23,33,48]', '[11,14]', '2026-04-10', '2026-04-10 02:58:31'),
(509, 19, 'Friday Lotto', '[1,42,49,53,57]', '[63,64]', '2026-04-10', '2026-04-10 02:58:56'),
(510, 19, 'Friday Lotto', '[15,36,53,54,75]', '[24,40]', '2026-04-10', '2026-04-10 02:59:22'),
(511, 19, 'Monday Lotto', '[5,50,59,64,75]', '[37,58]', '2026-04-13', '2026-04-11 08:40:19'),
(512, 19, 'Monday Lotto', '[19,27,29,49,57]', '[33,56]', '2026-04-13', '2026-04-11 08:40:53'),
(513, 19, 'Monday Lotto', '[8,17,48,55,66]', '[16,42]', '2026-04-13', '2026-04-11 08:41:20'),
(514, 4, 'Monday Lotto', '[34,44,64,65,75]', '[1,12]', '2026-04-13', '2026-04-11 11:27:26'),
(515, 4, 'Monday Lotto', '[23,34,44,53,54]', '[16,26]', '2026-04-13', '2026-04-11 11:27:54'),
(516, 4, 'Wednesday Lotto', '[36,45,54,63,72]', '[21,31]', '2026-04-15', '2026-04-11 11:28:21'),
(517, 4, 'Monday Lotto', '[36,47,57,56,55]', '[21,32]', '2026-04-13', '2026-04-11 11:28:43'),
(518, 4, 'Wednesday Lotto', '[13,14,18,19,44]', '[12,17]', '2026-04-15', '2026-04-11 11:36:04'),
(519, 4, 'Monday Lotto', '[12,13,11,18,16]', '[23,38]', '2026-04-13', '2026-04-11 11:36:31'),
(520, 19, 'Monday Lotto', '[3,22,36,50,75]', '[21,28]', '2026-04-13', '2026-04-12 08:48:08'),
(521, 19, 'Monday Lotto', '[16,32,39,43,47]', '[34,50]', '2026-04-13', '2026-04-12 08:48:41'),
(522, 19, 'Monday Lotto', '[4,14,55,59,70]', '[37,53]', '2026-04-13', '2026-04-12 08:49:26'),
(523, 19, 'Monday Lotto', '[25,43,49,50,59]', '[4,62]', '2026-04-13', '2026-04-12 08:49:50'),
(524, 19, 'Monday Lotto', '[12,13,21,25,52]', '[47,68]', '2026-04-13', '2026-04-12 08:50:19'),
(525, 19, 'Monday Lotto', '[2,8,9,31,57]', '[6,11]', '2026-04-13', '2026-04-12 08:50:38'),
(526, 19, 'Monday Lotto', '[3,19,35,65,66]', '[10,42]', '2026-04-13', '2026-04-13 08:50:18'),
(527, 19, 'Monday Lotto', '[21,33,55,67,75]', '[11,59]', '2026-04-13', '2026-04-13 08:51:42'),
(528, 19, 'Monday Lotto', '[47,48,63,65,67]', '[20,43]', '2026-04-13', '2026-04-13 08:52:53'),
(529, 19, 'Monday Lotto', '[19,33,40,68,71]', '[21,72]', '2026-04-13', '2026-04-13 08:55:06'),
(530, 19, 'Monday Lotto', '[42,54,57,74,75]', '[52,69]', '2026-04-13', '2026-04-13 08:55:35'),
(531, 19, 'Monday Lotto', '[15,17,39,40,43]', '[61,67]', '2026-04-13', '2026-04-13 08:55:58'),
(532, 19, 'Monday Lotto', '[5,23,63,64,68]', '[1,12]', '2026-04-13', '2026-04-13 08:56:22'),
(533, 19, 'Monday Lotto', '[23,24,33,49,57]', '[55,73]', '2026-04-13', '2026-04-13 08:57:03'),
(534, 19, 'Monday Lotto', '[5,19,35,62,72]', '[24,50]', '2026-04-13', '2026-04-13 08:57:27'),
(535, 19, 'Monday Lotto', '[15,29,30,39,40]', '[8,37]', '2026-04-13', '2026-04-13 08:58:00'),
(536, 20, 'Monday Lotto', '[3,8,13,18,23]', '[24,25]', '2026-04-13', '2026-04-13 12:31:41'),
(537, 12, 'Monday Lotto', '[5,17,18,22,59]', '[26,74]', '2026-04-13', '2026-04-13 16:38:20'),
(538, 12, 'Monday Lotto', '[1,50,51,64,71]', '[33,65]', '2026-04-13', '2026-04-13 16:38:39'),
(539, 12, 'Wednesday Lotto', '[15,39,50,57,67]', '[16,18]', '2026-04-15', '2026-04-15 16:07:39'),
(540, 19, 'Friday Lotto', '[41,52,57,63,65]', '[24,36]', '2026-04-17', '2026-04-15 19:28:52'),
(541, 19, 'Friday Lotto', '[3,21,39,43,50]', '[13,49]', '2026-04-17', '2026-04-15 19:29:21'),
(542, 19, 'Friday Lotto', '[3,19,43,55,62]', '[37,74]', '2026-04-17', '2026-04-15 19:29:45'),
(543, 19, 'Friday Lotto', '[33,40,58,64,69]', '[65,67]', '2026-04-17', '2026-04-15 19:30:08'),
(544, 19, 'Friday Lotto', '[7,11,13,47,74]', '[16,72]', '2026-04-17', '2026-04-15 19:30:35'),
(545, 21, 'Friday Lotto', '[2,9,17,28,32]', '[3,18]', '2026-04-17', '2026-04-16 00:59:29'),
(546, 21, 'Monday Lotto', '[2,13,17,29,33]', '[3,8]', '2026-04-20', '2026-04-16 00:59:56'),
(547, 21, 'Wednesday Lotto', '[3,12,24,32,43]', '[2,13]', '2026-04-22', '2026-04-16 01:00:22'),
(548, 19, 'Friday Lotto', '[4,32,33,52,69]', '[46,54]', '2026-04-17', '2026-04-17 04:51:43'),
(549, 19, 'Friday Lotto', '[29,37,42,54,65]', '[15,47]', '2026-04-17', '2026-04-17 04:52:09'),
(550, 19, 'Friday Lotto', '[12,48,50,62,67]', '[23,57]', '2026-04-17', '2026-04-17 04:52:38'),
(551, 19, 'Friday Lotto', '[27,33,52,58,66]', '[6,67]', '2026-04-17', '2026-04-17 04:53:48'),
(552, 19, 'Friday Lotto', '[2,41,48,61,70]', '[7,72]', '2026-04-17', '2026-04-17 04:54:14'),
(553, 19, 'Friday Lotto', '[3,22,37,56,75]', '[26,74]', '2026-04-17', '2026-04-17 04:54:49'),
(554, 19, 'Friday Lotto', '[9,13,44,57,62]', '[22,75]', '2026-04-17', '2026-04-17 04:55:21'),
(555, 19, 'Friday Lotto', '[27,36,37,43,47]', '[9,63]', '2026-04-17', '2026-04-17 04:55:57'),
(556, 19, 'Friday Lotto', '[19,32,36,38,68]', '[17,64]', '2026-04-17', '2026-04-17 04:56:21'),
(557, 19, 'Friday Lotto', '[4,35,37,49,72]', '[20,73]', '2026-04-17', '2026-04-17 04:56:54'),
(558, 19, 'Friday Lotto', '[7,9,32,67,73]', '[22,60]', '2026-04-17', '2026-04-17 04:57:27'),
(559, 19, 'Friday Lotto', '[8,27,39,48,54]', '[11,70]', '2026-04-17', '2026-04-17 04:57:57'),
(560, 19, 'Friday Lotto', '[16,17,19,31,60]', '[18,55]', '2026-04-17', '2026-04-17 04:58:27'),
(561, 19, 'Friday Lotto', '[15,34,48,52,53]', '[10,66]', '2026-04-17', '2026-04-17 04:58:59'),
(562, 19, 'Friday Lotto', '[1,37,42,46,47]', '[28,62]', '2026-04-17', '2026-04-17 04:59:28'),
(563, 19, 'Friday Lotto', '[5,27,45,53,68]', '[9,50]', '2026-04-17', '2026-04-17 04:59:58'),
(564, 19, 'Friday Lotto', '[5,10,35,40,68]', '[11,49]', '2026-04-17', '2026-04-17 05:00:25'),
(565, 19, 'Friday Lotto', '[6,23,63,73,75]', '[39,61]', '2026-04-17', '2026-04-17 05:01:08'),
(566, 19, 'Friday Lotto', '[5,6,16,24,52]', '[22,70]', '2026-04-17', '2026-04-17 05:01:36'),
(567, 19, 'Friday Lotto', '[11,12,16,27,61]', '[26,45]', '2026-04-17', '2026-04-17 05:01:57'),
(568, 19, 'Friday Lotto', '[20,32,43,48,64]', '[9,51]', '2026-04-17', '2026-04-17 05:02:56'),
(569, 19, 'Friday Lotto', '[20,32,43,48,64]', '[9,51]', '2026-04-17', '2026-04-17 05:03:07'),
(570, 19, 'Friday Lotto', '[1,5,15,41,53]', '[29,75]', '2026-04-17', '2026-04-17 05:03:41'),
(571, 22, 'Monday Lotto', '[15,19,28,40,68]', '[13,27]', '2026-04-20', '2026-04-17 14:48:21'),
(572, 22, 'Monday Lotto', '[2,8,16,24,43]', '[14,45]', '2026-04-20', '2026-04-17 14:48:53'),
(573, 22, 'Wednesday Lotto', '[3,11,45,50,52]', '[23,32]', '2026-04-22', '2026-04-17 14:49:13'),
(574, 22, 'Wednesday Lotto', '[7,39,51,62,65]', '[12,50]', '2026-04-22', '2026-04-17 14:49:31'),
(575, 22, 'Wednesday Lotto', '[1,4,6,15,51]', '[17,58]', '2026-04-22', '2026-04-17 14:50:06'),
(576, 22, 'Friday Lotto', '[27,38,66,71,74]', '[39,41]', '2026-04-17', '2026-04-17 14:52:32'),
(577, 22, 'Friday Lotto', '[5,20,26,71,74]', '[6,22]', '2026-04-17', '2026-04-17 14:54:37'),
(578, 22, 'Wednesday Lotto', '[14,31,53,54,64]', '[28,30]', '2026-04-22', '2026-04-17 15:00:15'),
(579, 23, 'Monday Lotto', '[33,6,47,11,73]', '[46,55]', '2026-04-20', '2026-04-17 17:42:04'),
(580, 23, 'Monday Lotto', '[8,11,33,48,54]', '[4,28]', '2026-04-20', '2026-04-17 17:42:37'),
(581, 23, 'Monday Lotto', '[3,4,10,16,41]', '[29,38]', '2026-04-20', '2026-04-17 17:43:01'),
(582, 23, 'Monday Lotto', '[25,26,36,52,75]', '[11,64]', '2026-04-20', '2026-04-17 17:43:24'),
(583, 23, 'Monday Lotto', '[7,11,13,35,74]', '[2,19]', '2026-04-20', '2026-04-17 17:43:42'),
(584, 23, 'Wednesday Lotto', '[5,14,22,55,57]', '[18,21]', '2026-04-22', '2026-04-17 17:44:08'),
(585, 23, 'Wednesday Lotto', '[11,13,18,58,72]', '[1,17]', '2026-04-22', '2026-04-17 17:44:27'),
(586, 23, 'Wednesday Lotto', '[2,17,35,51,65]', '[16,40]', '2026-04-22', '2026-04-17 17:44:44'),
(587, 23, 'Wednesday Lotto', '[22,37,41,46,48]', '[11,35]', '2026-04-22', '2026-04-17 17:45:07'),
(588, 23, 'Wednesday Lotto', '[8,41,45,63,74]', '[32,57]', '2026-04-22', '2026-04-17 17:45:26'),
(589, 23, 'Wednesday Lotto', '[17,24,31,49,65]', '[7,28]', '2026-04-22', '2026-04-17 17:45:45'),
(590, 23, 'Friday Lotto', '[9,19,36,45,71]', '[10,34]', '2026-04-24', '2026-04-17 17:46:18'),
(591, 23, 'Friday Lotto', '[43,67,69,71,74]', '[5,19]', '2026-04-24', '2026-04-17 17:46:38'),
(592, 23, 'Friday Lotto', '[6,14,30,37,65]', '[58,61]', '2026-04-24', '2026-04-17 17:47:03'),
(593, 23, 'Friday Lotto', '[9,51,52,53,69]', '[7,31]', '2026-04-24', '2026-04-17 17:47:26'),
(594, 23, 'Friday Lotto', '[2,3,5,13,45]', '[39,40]', '2026-04-24', '2026-04-17 17:47:47'),
(595, 23, 'Friday Lotto', '[27,31,50,60,65]', '[33,43]', '2026-04-24', '2026-04-17 17:48:09'),
(596, 23, 'Friday Lotto', '[3,7,12,13,27]', '[15,41]', '2026-04-24', '2026-04-17 17:48:32'),
(597, 23, 'Friday Lotto', '[4,8,39,53,59]', '[27,70]', '2026-04-24', '2026-04-17 17:48:55'),
(598, 23, 'Friday Lotto', '[13,14,51,69,74]', '[3,45]', '2026-04-24', '2026-04-17 17:49:16'),
(599, 23, 'Friday Lotto', '[5,23,25,52,71]', '[4,72]', '2026-04-24', '2026-04-17 17:49:39'),
(600, 23, 'Friday Lotto', '[30,32,36,63,67]', '[8,21]', '2026-04-24', '2026-04-17 17:50:01'),
(601, 23, 'Friday Lotto', '[3,22,42,44,63]', '[13,19]', '2026-04-24', '2026-04-17 17:50:20');
INSERT INTO `entry` (`id`, `user_id`, `lottery`, `numbers`, `bonus_numbers`, `draw_date`, `created_at`) VALUES
(602, 23, 'Monday Lotto', '[33,8,66,12,53]', '[11,2]', '2026-04-20', '2026-04-18 05:00:55'),
(603, 23, 'Monday Lotto', '[11,2,22,4,42]', '[33,6]', '2026-04-20', '2026-04-18 05:02:28'),
(604, 23, 'Monday Lotto', '[61,21,24,2,42]', '[65,28]', '2026-04-20', '2026-04-18 05:04:15'),
(605, 23, 'Monday Lotto', '[20,21,63,4,43]', '[19,31]', '2026-04-20', '2026-04-18 05:05:21'),
(606, 23, 'Monday Lotto', '[38,11,53,8,72]', '[23,5]', '2026-04-20', '2026-04-18 05:06:21'),
(607, 23, 'Monday Lotto', '[53,8,69,15,34]', '[18,9]', '2026-04-20', '2026-04-18 05:07:18'),
(608, 23, 'Monday Lotto', '[28,10,52,7,64]', '[2,21]', '2026-04-20', '2026-04-18 05:08:08'),
(609, 23, 'Monday Lotto', '[2,4,45,62,37]', '[3,67]', '2026-04-20', '2026-04-18 05:09:25'),
(610, 23, 'Monday Lotto', '[14,25,35,65,67]', '[32,59]', '2026-04-20', '2026-04-18 05:09:59'),
(611, 23, 'Monday Lotto', '[9,32,42,65,66]', '[8,51]', '2026-04-20', '2026-04-18 05:10:24'),
(612, 23, 'Monday Lotto', '[66,12,53,8,33]', '[16,51]', '2026-04-20', '2026-04-18 05:11:30'),
(613, 23, 'Monday Lotto', '[43,64,27,13,20]', '[18,3]', '2026-04-20', '2026-04-18 05:12:20'),
(614, 23, 'Monday Lotto', '[18,9,43,7,53]', '[35,61]', '2026-04-20', '2026-04-18 05:13:13'),
(615, 23, 'Monday Lotto', '[22,4,43,7,61]', '[19,60]', '2026-04-20', '2026-04-18 05:13:59'),
(616, 23, 'Monday Lotto', '[34,7,31,4,43]', '[69,72]', '2026-04-20', '2026-04-18 05:14:56'),
(617, 23, 'Monday Lotto', '[23,5,48,12,64]', '[7,24]', '2026-04-20', '2026-04-18 05:17:11'),
(618, 23, 'Wednesday Lotto', '[4,13,26,61,65]', '[23,69]', '2026-04-22', '2026-04-18 05:18:02'),
(619, 23, 'Wednesday Lotto', '[2,22,30,31,69]', '[19,26]', '2026-04-22', '2026-04-18 05:18:43'),
(620, 23, 'Wednesday Lotto', '[16,19,41,43,53]', '[21,59]', '2026-04-22', '2026-04-18 05:19:30'),
(621, 23, 'Wednesday Lotto', '[11,17,61,68,73]', '[1,32]', '2026-04-22', '2026-04-18 05:20:02'),
(622, 23, 'Wednesday Lotto', '[37,38,53,58,69]', '[25,68]', '2026-04-22', '2026-04-18 05:20:41'),
(623, 23, 'Wednesday Lotto', '[29,48,55,66,73]', '[12,44]', '2026-04-22', '2026-04-18 05:21:34'),
(624, 23, 'Wednesday Lotto', '[10,11,24,54,59]', '[8,15]', '2026-04-22', '2026-04-18 05:22:50'),
(625, 23, 'Wednesday Lotto', '[13,47,48,55,57]', '[24,65]', '2026-04-22', '2026-04-18 05:23:24'),
(626, 23, 'Wednesday Lotto', '[1,5,11,64,68]', '[62,70]', '2026-04-22', '2026-04-18 05:24:02'),
(627, 23, 'Wednesday Lotto', '[16,20,23,28,56]', '[13,68]', '2026-04-22', '2026-04-18 05:24:26'),
(628, 23, 'Wednesday Lotto', '[4,16,31,46,58]', '[9,19]', '2026-04-22', '2026-04-18 05:24:57'),
(629, 23, 'Wednesday Lotto', '[5,14,22,49,51]', '[9,29]', '2026-04-22', '2026-04-18 05:25:40'),
(630, 23, 'Wednesday Lotto', '[6,45,62,65,69]', '[31,58]', '2026-04-22', '2026-04-18 05:26:29'),
(631, 23, 'Wednesday Lotto', '[1,6,19,31,51]', '[22,29]', '2026-04-22', '2026-04-18 05:27:00'),
(632, 23, 'Wednesday Lotto', '[8,17,37,38,39]', '[4,75]', '2026-04-22', '2026-04-18 05:27:25'),
(633, 23, 'Wednesday Lotto', '[14,26,29,44,71]', '[38,66]', '2026-04-22', '2026-04-18 05:28:01'),
(634, 23, 'Wednesday Lotto', '[12,23,34,41,50]', '[3,14]', '2026-04-22', '2026-04-18 05:28:30'),
(635, 23, 'Wednesday Lotto', '[8,18,20,31,45]', '[15,33]', '2026-04-22', '2026-04-18 05:29:08'),
(636, 23, 'Wednesday Lotto', '[4,7,15,32,53]', '[10,46]', '2026-04-22', '2026-04-18 05:29:53'),
(637, 23, 'Wednesday Lotto', '[1,16,24,32,71]', '[12,54]', '2026-04-22', '2026-04-18 05:30:50'),
(638, 23, 'Wednesday Lotto', '[3,13,24,35,47]', '[39,46]', '2026-04-22', '2026-04-18 05:31:14'),
(639, 23, 'Wednesday Lotto', '[12,13,18,26,56]', '[17,29]', '2026-04-22', '2026-04-18 05:31:47'),
(640, 23, 'Wednesday Lotto', '[18,25,36,47,59]', '[20,52]', '2026-04-22', '2026-04-18 05:32:11'),
(641, 23, 'Wednesday Lotto', '[21,32,34,51,58]', '[27,35]', '2026-04-22', '2026-04-18 05:32:40'),
(642, 23, 'Wednesday Lotto', '[16,31,34,62,66]', '[13,72]', '2026-04-22', '2026-04-18 05:33:06'),
(643, 23, 'Wednesday Lotto', '[15,29,30,35,59]', '[12,63]', '2026-04-22', '2026-04-18 05:33:38'),
(644, 23, 'Wednesday Lotto', '[7,24,26,38,57]', '[23,55]', '2026-04-22', '2026-04-18 05:34:05'),
(645, 23, 'Wednesday Lotto', '[18,37,41,46,70]', '[26,69]', '2026-04-22', '2026-04-18 05:34:34'),
(646, 23, 'Wednesday Lotto', '[5,8,19,51,72]', '[37,57]', '2026-04-22', '2026-04-18 05:35:06'),
(647, 23, 'Wednesday Lotto', '[1,7,23,48,58]', '[29,61]', '2026-04-22', '2026-04-18 05:35:43'),
(648, 23, 'Monday Lotto', '[5,29,30,43,48]', '[15,24]', '2026-04-20', '2026-04-18 05:36:12'),
(649, 23, 'Monday Lotto', '[19,33,54,55,71]', '[6,46]', '2026-04-20', '2026-04-18 05:36:47'),
(650, 23, 'Monday Lotto', '[12,19,28,67,69]', '[30,32]', '2026-04-20', '2026-04-18 05:38:37'),
(651, 23, 'Monday Lotto', '[22,42,46,58,67]', '[32,59]', '2026-04-20', '2026-04-18 05:38:59'),
(652, 23, 'Monday Lotto', '[4,21,24,38,59]', '[27,60]', '2026-04-20', '2026-04-18 05:39:26'),
(653, 23, 'Monday Lotto', '[1,6,25,36,56]', '[7,68]', '2026-04-20', '2026-04-18 05:39:54'),
(654, 23, 'Monday Lotto', '[2,21,24,33,59]', '[57,71]', '2026-04-20', '2026-04-18 05:40:21'),
(655, 23, 'Monday Lotto', '[4,25,28,40,71]', '[8,36]', '2026-04-20', '2026-04-18 05:40:45'),
(656, 23, 'Monday Lotto', '[6,8,19,44,47]', '[5,46]', '2026-04-20', '2026-04-18 05:41:07'),
(657, 23, 'Monday Lotto', '[14,28,30,37,64]', '[15,22]', '2026-04-20', '2026-04-18 05:41:32'),
(658, 23, 'Monday Lotto', '[10,11,15,46,56]', '[9,39]', '2026-04-20', '2026-04-18 05:41:56'),
(659, 23, 'Monday Lotto', '[6,11,15,45,46]', '[29,38]', '2026-04-20', '2026-04-18 05:42:30'),
(660, 23, 'Monday Lotto', '[2,33,35,44,55]', '[46,67]', '2026-04-20', '2026-04-18 05:43:02'),
(661, 23, 'Monday Lotto', '[19,27,49,56,74]', '[44,62]', '2026-04-20', '2026-04-18 05:43:32'),
(662, 23, 'Monday Lotto', '[24,27,36,41,48]', '[28,45]', '2026-04-20', '2026-04-18 05:44:10'),
(663, 23, 'Monday Lotto', '[7,8,17,21,68]', '[4,20]', '2026-04-20', '2026-04-18 05:44:46'),
(664, 23, 'Monday Lotto', '[1,7,17,33,47]', '[16,20]', '2026-04-20', '2026-04-18 05:45:26'),
(665, 23, 'Monday Lotto', '[54,57,62,65,74]', '[3,17]', '2026-04-20', '2026-04-18 05:54:12'),
(666, 23, 'Monday Lotto', '[3,7,14,47,54]', '[34,71]', '2026-04-20', '2026-04-18 05:54:36'),
(667, 23, 'Monday Lotto', '[5,15,35,45,61]', '[74,11]', '2026-04-20', '2026-04-18 05:55:35'),
(668, 23, 'Monday Lotto', '[38,11,63,9,54]', '[23,5]', '2026-04-20', '2026-04-18 05:56:23'),
(669, 23, 'Monday Lotto', '[8,44,49,58,64]', '[41,52]', '2026-04-20', '2026-04-18 05:56:45'),
(670, 23, 'Monday Lotto', '[17,43,56,64,65]', '[2,35]', '2026-04-20', '2026-04-18 05:57:12'),
(671, 23, 'Monday Lotto', '[3,44,56,69,74]', '[36,60]', '2026-04-20', '2026-04-18 05:57:35'),
(672, 23, 'Monday Lotto', '[12,18,20,38,60]', '[39,45]', '2026-04-20', '2026-04-18 05:57:59'),
(673, 23, 'Monday Lotto', '[23,5,53,8,37]', '[26,42]', '2026-04-20', '2026-04-18 05:58:45'),
(674, 23, 'Monday Lotto', '[7,17,36,50,57]', '[11,44]', '2026-04-20', '2026-04-18 05:59:08'),
(675, 23, 'Monday Lotto', '[9,35,41,45,50]', '[21,49]', '2026-04-20', '2026-04-18 05:59:30'),
(676, 23, 'Monday Lotto', '[7,38,50,58,70]', '[57,62]', '2026-04-20', '2026-04-18 05:59:53'),
(677, 23, 'Monday Lotto', '[25,46,51,53,57]', '[6,58]', '2026-04-20', '2026-04-18 06:00:15'),
(678, 23, 'Monday Lotto', '[3,4,28,56,61]', '[6,73]', '2026-04-20', '2026-04-18 06:00:42'),
(679, 23, 'Monday Lotto', '[16,27,44,70,74]', '[7,31]', '2026-04-20', '2026-04-18 06:01:12'),
(680, 23, 'Monday Lotto', '[7,17,28,46,66]', '[1,13]', '2026-04-20', '2026-04-18 06:01:37'),
(681, 23, 'Monday Lotto', '[16,31,54,69,70]', '[48,57]', '2026-04-20', '2026-04-18 06:01:59'),
(682, 23, 'Monday Lotto', '[4,30,41,44,55]', '[24,63]', '2026-04-20', '2026-04-18 06:02:27'),
(683, 23, 'Monday Lotto', '[2,8,12,45,74]', '[50,60]', '2026-04-20', '2026-04-18 06:02:54'),
(684, 23, 'Monday Lotto', '[8,19,28,39,70]', '[7,35]', '2026-04-20', '2026-04-18 15:39:19'),
(685, 23, 'Monday Lotto', '[7,25,45,63,67]', '[21,22]', '2026-04-20', '2026-04-18 15:39:40'),
(686, 23, 'Monday Lotto', '[3,14,16,51,73]', '[63,65]', '2026-04-20', '2026-04-18 15:40:22'),
(687, 23, 'Monday Lotto', '[3,9,23,27,42]', '[33,60]', '2026-04-20', '2026-04-18 15:40:39'),
(688, 23, 'Monday Lotto', '[15,18,44,52,67]', '[7,65]', '2026-04-20', '2026-04-18 15:40:55'),
(689, 23, 'Monday Lotto', '[14,27,40,63,67]', '[24,39]', '2026-04-20', '2026-04-18 15:41:11'),
(690, 23, 'Monday Lotto', '[6,17,55,56,72]', '[4,68]', '2026-04-20', '2026-04-18 15:41:32'),
(691, 23, 'Monday Lotto', '[4,42,48,54,59]', '[38,72]', '2026-04-20', '2026-04-18 15:42:03'),
(692, 23, 'Monday Lotto', '[3,12,15,19,64]', '[40,65]', '2026-04-20', '2026-04-18 15:42:21'),
(693, 23, 'Monday Lotto', '[7,14,19,52,74]', '[3,28]', '2026-04-20', '2026-04-18 15:42:42'),
(694, 23, 'Monday Lotto', '[4,21,50,65,74]', '[11,16]', '2026-04-20', '2026-04-18 15:43:03'),
(695, 23, 'Monday Lotto', '[30,48,49,54,69]', '[6,74]', '2026-04-20', '2026-04-18 15:43:21'),
(696, 23, 'Monday Lotto', '[9,10,27,61,66]', '[3,17]', '2026-04-20', '2026-04-18 15:43:41'),
(697, 23, 'Monday Lotto', '[6,38,51,57,66]', '[1,12]', '2026-04-20', '2026-04-18 15:44:01'),
(698, 23, 'Monday Lotto', '[7,15,20,44,54]', '[13,49]', '2026-04-20', '2026-04-18 15:44:24'),
(699, 23, 'Monday Lotto', '[7,32,33,39,46]', '[10,60]', '2026-04-20', '2026-04-18 15:44:45'),
(700, 23, 'Monday Lotto', '[24,53,55,63,73]', '[66,74]', '2026-04-20', '2026-04-18 15:45:07'),
(701, 19, 'Wednesday Lotto', '[20,27,31,32,43]', '[11,45]', '2026-04-22', '2026-04-21 10:14:32'),
(702, 19, 'Wednesday Lotto', '[2,35,49,51,72]', '[20,62]', '2026-04-22', '2026-04-21 10:15:08'),
(703, 19, 'Wednesday Lotto', '[12,27,32,38,40]', '[36,5]', '2026-04-22', '2026-04-21 10:19:05'),
(704, 19, 'Wednesday Lotto', '[2,12,18,22,56]', '[20,68]', '2026-04-22', '2026-04-21 10:19:24'),
(705, 19, 'Wednesday Lotto', '[8,15,46,55,58]', '[2,52]', '2026-04-22', '2026-04-21 10:19:43'),
(706, 19, 'Wednesday Lotto', '[4,16,28,66,69]', '[21,29]', '2026-04-22', '2026-04-21 10:20:02'),
(707, 19, 'Wednesday Lotto', '[18,31,36,43,47]', '[24,59]', '2026-04-22', '2026-04-21 10:20:23'),
(708, 19, 'Wednesday Lotto', '[6,15,22,34,53]', '[24,51]', '2026-04-22', '2026-04-21 10:20:45'),
(709, 19, 'Wednesday Lotto', '[8,34,51,65,70]', '[21,43]', '2026-04-22', '2026-04-21 10:21:08'),
(710, 19, 'Wednesday Lotto', '[5,23,29,31,52]', '[3,70]', '2026-04-22', '2026-04-21 10:21:37'),
(711, 19, 'Wednesday Lotto', '[19,32,40,47,64]', '[52,68]', '2026-04-22', '2026-04-21 10:22:03'),
(712, 24, 'Friday Lotto', '[8,14,27,37,44]', '[11,51]', '2026-04-24', '2026-04-22 18:13:51'),
(713, 24, 'Friday Lotto', '[1,13,26,58,68]', '[18,71]', '2026-04-24', '2026-04-22 18:14:54'),
(714, 24, 'Monday Lotto', '[14,4,73,42,22]', '[52,30]', '2026-04-27', '2026-04-22 18:15:44'),
(715, 24, 'Friday Lotto', '[11,8,27,44,63]', '[42,23]', '2026-04-24', '2026-04-22 18:17:31'),
(716, 19, 'Friday Lotto', '[10,25,32,56,73]', '[63,70]', '2026-04-24', '2026-04-24 11:36:43'),
(717, 19, 'Friday Lotto', '[23,30,64,70,71]', '[54,60]', '2026-04-24', '2026-04-24 11:37:06'),
(718, 19, 'Friday Lotto', '[6,44,62,66,68]', '[20,71]', '2026-04-24', '2026-04-24 11:37:24'),
(719, 19, 'Friday Lotto', '[2,13,30,48,73]', '[31,62]', '2026-04-24', '2026-04-24 11:37:53'),
(720, 19, 'Friday Lotto', '[35,45,50,60,62]', '[40,47]', '2026-04-24', '2026-04-24 11:39:25'),
(721, 25, 'Monday Lotto', '[14,18,28,47,51]', '[20,66]', '2026-04-27', '2026-04-25 17:29:21'),
(722, 25, 'Monday Lotto', '[18,19,33,73,75]', '[10,52]', '2026-04-27', '2026-04-25 17:29:46'),
(723, 25, 'Wednesday Lotto', '[24,29,44,49,67]', '[7,70]', '2026-04-29', '2026-04-25 17:30:18'),
(724, 25, 'Friday Lotto', '[10,12,19,25,35]', '[45,69]', '2026-05-01', '2026-04-25 17:31:15'),
(725, 24, 'Wednesday Lotto', '[4,13,28,44,63]', '[18,38]', '2026-04-29', '2026-04-28 18:28:27'),
(726, 24, 'Friday Lotto', '[2,17,34,64,52]', '[27,53]', '2026-05-01', '2026-04-28 18:30:11'),
(727, 4, 'Monday Lotto', '[3,2,23,28,27]', '[22,18]', '2026-05-04', '2026-04-29 20:35:22'),
(728, 4, 'Friday Lotto', '[23,28,38,29,34]', '[26,27]', '2026-05-01', '2026-04-29 20:35:50'),
(729, 4, 'Friday Lotto', '[1,2,3,4,5]', '[21,26]', '2026-05-01', '2026-04-29 20:36:07'),
(730, 24, 'Friday Lotto', '[3,8,24,49,72]', '[11,4]', '2026-05-01', '2026-04-30 18:25:57'),
(731, 21, 'Monday Lotto', '[2,13,22,38,52]', '[3,18]', '2026-05-04', '2026-05-02 06:32:42'),
(732, 21, 'Wednesday Lotto', '[2,13,22,38,53]', '[3,18]', '2026-05-06', '2026-05-02 06:33:02'),
(733, 21, 'Friday Lotto', '[2,13,22,47,34]', '[3,18]', '2026-05-08', '2026-05-02 06:33:29'),
(734, 19, 'Wednesday Lotto', '[19,37,55,58,71]', '[44,69]', '2026-05-06', '2026-05-05 19:06:35'),
(735, 19, 'Wednesday Lotto', '[52,53,62,71,73]', '[19,44]', '2026-05-06', '2026-05-05 19:07:45'),
(736, 19, 'Wednesday Lotto', '[13,19,31,50,70]', '[9,24]', '2026-05-06', '2026-05-05 19:08:37'),
(737, 19, 'Wednesday Lotto', '[2,13,29,43,74]', '[1,58]', '2026-05-06', '2026-05-05 19:09:25'),
(738, 19, 'Wednesday Lotto', '[4,15,40,48,56]', '[14,41]', '2026-05-06', '2026-05-05 19:09:58'),
(739, 19, 'Wednesday Lotto', '[10,19,35,52,71]', '[46,73]', '2026-05-06', '2026-05-05 19:10:34'),
(740, 24, 'Friday Lotto', '[13,39,58,73,28]', '[10,42]', '2026-05-08', '2026-05-07 19:57:51'),
(741, 24, 'Friday Lotto', '[13,4,33,54,63]', '[20,12]', '2026-05-08', '2026-05-07 20:01:36');

-- --------------------------------------------------------

--
-- Table structure for table `leading_numbers_snapshot`
--

CREATE TABLE `leading_numbers_snapshot` (
  `id` int(11) NOT NULL,
  `lottery` varchar(100) NOT NULL,
  `draw_date` date NOT NULL,
  `top_five` text NOT NULL,
  `top_two` text NOT NULL,
  `section1_data` longtext NOT NULL,
  `section2_data` longtext NOT NULL,
  `total_user_votes` int(11) NOT NULL DEFAULT 0,
  `total_admin_allocations` int(11) NOT NULL DEFAULT 0,
  `total_main_votes` int(11) NOT NULL DEFAULT 0,
  `total_bonus_votes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sent_by` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `sent_by`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 4, 3, 'Congratulations! You Won!', 'You won $10.11 in the Wednesday Lotto draw!', 'success', 0, '2026-02-05 21:11:07'),
(3, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 04, 2026 are now available.', 'info', 0, '2026-02-05 21:11:07'),
(6, 4, 3, 'Congratulations! You Won!', 'You won $10.11 in the Wednesday Lotto draw!', 'success', 0, '2026-02-05 21:19:06'),
(8, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 04, 2026 are now available.', 'info', 0, '2026-02-05 21:19:06'),
(12, 4, 3, 'Results Published', 'Results for Friday Lotto on Feb 06, 2026 are now available.', 'info', 0, '2026-02-06 17:03:52'),
(15, 4, 3, 'Results Published', 'Results for Monday Lotto on Feb 09, 2026 are now available.', 'info', 0, '2026-02-09 19:48:33'),
(16, 3, 3, 'Results Published', 'Results for Monday Lotto on Feb 09, 2026 are now available.', 'info', 0, '2026-02-09 19:48:33'),
(19, 3, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 11, 2026 are now available.', 'info', 0, '2026-02-11 17:05:18'),
(20, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 11, 2026 are now available.', 'info', 0, '2026-02-11 17:05:18'),
(21, 4, 3, 'Results Published', 'Results for Friday Lotto on Feb 13, 2026 are now available.', 'info', 0, '2026-02-13 18:48:34'),
(22, 3, 3, 'Results Published', 'Results for Monday Lotto on Feb 16, 2026 are now available.', 'info', 0, '2026-02-16 17:06:21'),
(25, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 18, 2026 are now available.', 'info', 0, '2026-02-18 18:46:37'),
(27, 12, 3, 'Results Published', 'Results for Monday Lotto on Feb 23, 2026 are now available.', 'info', 0, '2026-02-23 18:45:57'),
(28, 3, 3, 'Results Published', 'Results for Monday Lotto on Feb 23, 2026 are now available.', 'info', 0, '2026-02-23 18:45:57'),
(29, 9, 3, 'Results Published', 'Results for Monday Lotto on Feb 23, 2026 are now available.', 'info', 0, '2026-02-23 18:45:57'),
(30, 13, 3, 'Results Published', 'Results for Monday Lotto on Feb 23, 2026 are now available.', 'info', 0, '2026-02-23 18:45:57'),
(31, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 25, 2026 are now available.', 'info', 0, '2026-02-25 20:36:55'),
(32, 14, 3, 'Results Published', 'Results for Wednesday Lotto on Feb 25, 2026 are now available.', 'info', 0, '2026-02-25 20:36:55'),
(33, 4, 3, 'Payment Processed', 'Your prize of $10.11 has been paid!', 'success', 0, '2026-02-27 13:50:49'),
(34, 4, 3, 'Congratulations! You Won!', 'You won $10.09 in the Friday Lotto draw!', 'success', 0, '2026-02-27 17:11:34'),
(35, 4, 3, 'Results Published', 'Results for Friday Lotto on Feb 27, 2026 are now available.', 'info', 0, '2026-02-27 17:11:34'),
(36, 12, 3, 'Results Published', 'Results for Friday Lotto on Feb 27, 2026 are now available.', 'info', 0, '2026-02-27 17:11:34'),
(37, 4, 3, 'Results Published', 'Results for Monday Lotto on Mar 02, 2026 are now available.', 'info', 0, '2026-03-02 21:23:01'),
(38, 15, 3, 'Results Published', 'Results for Wednesday Lotto on Mar 04, 2026 are now available.', 'info', 0, '2026-03-04 17:11:11'),
(39, 12, 3, 'Results Published', 'Results for Wednesday Lotto on Mar 04, 2026 are now available.', 'info', 0, '2026-03-04 17:11:11'),
(40, 4, 3, 'Results Published', 'Results for Friday Lotto on Mar 06, 2026 are now available.', 'info', 0, '2026-03-06 19:49:58'),
(41, 15, 3, 'Results Published', 'Results for Monday Lotto on Mar 09, 2026 are now available.', 'info', 0, '2026-03-10 08:04:57'),
(42, 4, 3, 'Results Published', 'Results for Monday Lotto on Mar 09, 2026 are now available.', 'info', 0, '2026-03-10 08:04:57'),
(43, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Mar 11, 2026 are now available.', 'info', 0, '2026-03-12 06:19:13'),
(44, 12, 3, 'Results Published', 'Results for Wednesday Lotto on Mar 11, 2026 are now available.', 'info', 0, '2026-03-12 06:19:13'),
(45, 3, 3, 'Results Published', 'Results for Friday Lotto on Mar 13, 2026 are now available.', 'info', 0, '2026-03-14 16:04:00'),
(46, 4, 3, 'Results Published', 'Results for Monday Lotto on Mar 23, 2026 are now available.', 'info', 0, '2026-03-24 15:33:25'),
(47, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Mar 25, 2026 are now available.', 'info', 0, '2026-03-25 20:06:48'),
(48, 4, 3, 'Results Published', 'Results for Friday Lotto on Mar 27, 2026 are now available.', 'info', 0, '2026-03-29 12:37:56'),
(49, 12, 3, 'Congratulations! You Won!', 'You won $10.04 in the Wednesday Lotto draw!', 'success', 0, '2026-04-01 18:38:36'),
(50, 16, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 01, 2026 are now available.', 'info', 0, '2026-04-01 18:38:36'),
(51, 17, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 01, 2026 are now available.', 'info', 0, '2026-04-01 18:38:36'),
(52, 12, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 01, 2026 are now available.', 'info', 0, '2026-04-01 18:38:36'),
(53, 18, 3, 'Results Published', 'Results for Friday Lotto on Apr 03, 2026 are now available.', 'info', 0, '2026-04-04 01:18:12'),
(54, 19, 3, 'Results Published', 'Results for Friday Lotto on Apr 03, 2026 are now available.', 'info', 0, '2026-04-04 01:18:12'),
(55, 17, 3, 'Results Published', 'Results for Monday Lotto on Apr 06, 2026 are now available.', 'info', 0, '2026-04-08 16:14:38'),
(56, 18, 3, 'Results Published', 'Results for Monday Lotto on Apr 06, 2026 are now available.', 'info', 0, '2026-04-08 16:14:38'),
(57, 19, 3, 'Results Published', 'Results for Monday Lotto on Apr 06, 2026 are now available.', 'info', 0, '2026-04-08 16:14:38'),
(58, 18, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 08, 2026 are now available.', 'info', 0, '2026-04-09 05:45:30'),
(59, 19, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 08, 2026 are now available.', 'info', 0, '2026-04-09 05:45:30'),
(60, 18, 3, 'Results Published', 'Results for Friday Lotto on Apr 10, 2026 are now available.', 'info', 0, '2026-04-11 05:51:55'),
(61, 19, 3, 'Results Published', 'Results for Friday Lotto on Apr 10, 2026 are now available.', 'info', 0, '2026-04-11 05:51:55'),
(62, 19, 3, 'Results Published', 'Results for Monday Lotto on Apr 13, 2026 are now available.', 'info', 0, '2026-04-14 09:44:56'),
(63, 4, 3, 'Results Published', 'Results for Monday Lotto on Apr 13, 2026 are now available.', 'info', 0, '2026-04-14 09:44:56'),
(64, 20, 3, 'Results Published', 'Results for Monday Lotto on Apr 13, 2026 are now available.', 'info', 0, '2026-04-14 09:44:56'),
(65, 12, 3, 'Results Published', 'Results for Monday Lotto on Apr 13, 2026 are now available.', 'info', 0, '2026-04-14 09:44:56'),
(66, 4, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 15, 2026 are now available.', 'info', 0, '2026-04-16 11:47:07'),
(67, 12, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 15, 2026 are now available.', 'info', 0, '2026-04-16 11:47:07'),
(68, 19, 3, 'Results Published', 'Results for Friday Lotto on Apr 17, 2026 are now available.', 'info', 0, '2026-04-17 18:00:44'),
(69, 21, 3, 'Results Published', 'Results for Friday Lotto on Apr 17, 2026 are now available.', 'info', 0, '2026-04-17 18:00:44'),
(70, 22, 3, 'Results Published', 'Results for Friday Lotto on Apr 17, 2026 are now available.', 'info', 0, '2026-04-17 18:00:44'),
(71, 21, 3, 'Results Published', 'Results for Monday Lotto on Apr 20, 2026 are now available.', 'info', 0, '2026-04-21 14:13:23'),
(72, 22, 3, 'Results Published', 'Results for Monday Lotto on Apr 20, 2026 are now available.', 'info', 0, '2026-04-21 14:13:23'),
(73, 23, 3, 'Results Published', 'Results for Monday Lotto on Apr 20, 2026 are now available.', 'info', 0, '2026-04-21 14:13:23'),
(74, 21, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 22, 2026 are now available.', 'info', 0, '2026-04-22 18:42:24'),
(75, 22, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 22, 2026 are now available.', 'info', 0, '2026-04-22 18:42:24'),
(76, 23, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 22, 2026 are now available.', 'info', 0, '2026-04-22 18:42:24'),
(77, 19, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 22, 2026 are now available.', 'info', 0, '2026-04-22 18:42:24'),
(78, 23, 3, 'Results Published', 'Results for Friday Lotto on Apr 24, 2026 are now available.', 'info', 0, '2026-04-24 19:18:37'),
(79, 24, 3, 'Results Published', 'Results for Friday Lotto on Apr 24, 2026 are now available.', 'info', 0, '2026-04-24 19:18:37'),
(80, 19, 3, 'Results Published', 'Results for Friday Lotto on Apr 24, 2026 are now available.', 'info', 0, '2026-04-24 19:18:37'),
(81, 24, 3, 'Results Published', 'Results for Monday Lotto on Apr 27, 2026 are now available.', 'info', 0, '2026-04-27 18:18:51'),
(82, 25, 3, 'Results Published', 'Results for Monday Lotto on Apr 27, 2026 are now available.', 'info', 0, '2026-04-27 18:18:51'),
(83, 25, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 29, 2026 are now available.', 'info', 0, '2026-04-29 20:39:55'),
(84, 24, 3, 'Results Published', 'Results for Wednesday Lotto on Apr 29, 2026 are now available.', 'info', 0, '2026-04-29 20:39:55'),
(85, 25, 3, 'Results Published', 'Results for Friday Lotto on May 01, 2026 are now available.', 'info', 0, '2026-05-01 18:41:42'),
(86, 24, 3, 'Results Published', 'Results for Friday Lotto on May 01, 2026 are now available.', 'info', 0, '2026-05-01 18:41:42'),
(87, 4, 3, 'Results Published', 'Results for Friday Lotto on May 01, 2026 are now available.', 'info', 0, '2026-05-01 18:41:42'),
(88, 4, 3, 'Results Published', 'Results for Monday Lotto on May 04, 2026 are now available.', 'info', 0, '2026-05-04 19:26:19'),
(89, 21, 3, 'Results Published', 'Results for Monday Lotto on May 04, 2026 are now available.', 'info', 0, '2026-05-04 19:26:19'),
(90, 21, 3, 'Results Published', 'Results for Wednesday Lotto on May 06, 2026 are now available.', 'info', 0, '2026-05-07 08:24:58'),
(91, 19, 3, 'Results Published', 'Results for Wednesday Lotto on May 06, 2026 are now available.', 'info', 0, '2026-05-07 08:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reset_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `past_draw`
--

CREATE TABLE `past_draw` (
  `id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `draw_date` date NOT NULL,
  `winning_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`winning_numbers`)),
  `bonus_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bonus_numbers`)),
  `jackpot` decimal(10,2) NOT NULL,
  `winners` int(11) DEFAULT 0,
  `status` varchar(20) DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `past_draw`
--

INSERT INTO `past_draw` (`id`, `lottery`, `draw_date`, `winning_numbers`, `bonus_numbers`, `jackpot`, `winners`, `status`, `created_at`) VALUES
(1, 'Monday Lotto', '2026-01-19', '[]', '[]', 10.03, 0, 'completed', '2026-01-20 08:39:10'),
(3, 'Wednesday Lotto', '2026-01-21', '[]', '[]', 10.00, 0, 'completed', '2026-01-21 18:21:16'),
(4, 'Wednesday Lotto', '2026-01-21', '[]', '[]', 10.00, 0, 'completed', '2026-01-21 18:21:22'),
(5, 'Friday Lotto', '2026-01-23', '[]', '[]', 10.22, 0, 'completed', '2026-01-24 08:00:27'),
(6, 'Monday Lotto', '2026-01-26', '[]', '[]', 10.10, 0, 'completed', '2026-01-27 20:43:03'),
(7, 'Wednesday Lotto', '2026-01-28', '[]', '[]', 10.10, 0, 'completed', '2026-01-28 19:55:12'),
(8, 'Friday Lotto', '2026-01-30', '[]', '[]', 10.13, 0, 'completed', '2026-02-01 10:04:30'),
(9, 'Monday Lotto', '2026-02-02', '[]', '[]', 10.05, 0, 'completed', '2026-02-02 17:19:18'),
(10, 'Monday Lotto', '2026-02-02', '[]', '[]', 10.00, 0, 'completed', '2026-02-02 17:21:09'),
(11, 'Monday Lotto', '2026-02-02', '[]', '[]', 10.00, 0, 'completed', '2026-02-02 17:23:00'),
(12, 'Monday Lotto', '2026-02-02', '[]', '[]', 10.00, 0, 'completed', '2026-02-02 17:26:40'),
(13, 'Monday Lotto', '2026-02-02', '[]', '[]', 10.00, 0, 'completed', '2026-02-02 20:00:02'),
(14, 'Wednesday Lotto', '2026-02-04', '[]', '[]', 10.11, 0, 'completed', '2026-02-04 17:00:02'),
(15, 'Friday Lotto', '2026-02-06', '[]', '[]', 10.15, 0, 'completed', '2026-02-06 17:00:02'),
(16, 'Monday Lotto', '2026-02-09', '[]', '[]', 10.08, 0, 'completed', '2026-02-09 17:00:03'),
(17, 'Wednesday Lotto', '2026-02-11', '[]', '[]', 10.04, 0, 'completed', '2026-02-11 17:00:03'),
(18, 'Friday Lotto', '2026-02-13', '[]', '[]', 10.04, 0, 'completed', '2026-02-13 17:00:03'),
(19, 'Monday Lotto', '2026-02-16', '[]', '[]', 10.03, 0, 'completed', '2026-02-16 17:00:02'),
(20, 'Wednesday Lotto', '2026-02-18', '[]', '[]', 10.04, 0, 'completed', '2026-02-18 17:00:03'),
(21, 'Friday Lotto', '2026-02-20', '[]', '[]', 10.02, 0, 'completed', '2026-02-20 17:00:03'),
(22, 'Monday Lotto', '2026-02-23', '[]', '[]', 10.09, 0, 'completed', '2026-02-23 17:00:03'),
(23, 'Wednesday Lotto', '2026-02-25', '[]', '[]', 10.05, 0, 'completed', '2026-02-25 17:00:03'),
(24, 'Friday Lotto', '2026-02-27', '[]', '[]', 10.09, 0, 'completed', '2026-02-27 17:00:02'),
(25, 'Monday Lotto', '2026-03-02', '[]', '[]', 10.01, 0, 'completed', '2026-03-02 17:00:02'),
(26, 'Wednesday Lotto', '2026-03-04', '[]', '[]', 10.10, 0, 'completed', '2026-03-04 17:00:02'),
(27, 'Friday Lotto', '2026-03-06', '[]', '[]', 10.03, 0, 'completed', '2026-03-06 17:00:02'),
(28, 'Monday Lotto', '2026-03-09', '[]', '[]', 10.03, 0, 'completed', '2026-03-09 17:00:02'),
(29, 'Wednesday Lotto', '2026-03-11', '[]', '[]', 10.03, 0, 'completed', '2026-03-11 17:00:02'),
(30, 'Friday Lotto', '2026-03-13', '[]', '[]', 10.01, 0, 'completed', '2026-03-13 17:00:02'),
(31, 'Monday Lotto', '2026-03-16', '[]', '[]', 10.00, 0, 'completed', '2026-03-16 17:00:02'),
(32, 'Wednesday Lotto', '2026-03-18', '[]', '[]', 10.00, 0, 'completed', '2026-03-18 17:00:02'),
(33, 'Friday Lotto', '2026-03-20', '[]', '[]', 10.00, 0, 'completed', '2026-03-20 17:00:03'),
(34, 'Monday Lotto', '2026-03-23', '[]', '[]', 10.03, 0, 'completed', '2026-03-23 17:00:02'),
(35, 'Wednesday Lotto', '2026-03-25', '[]', '[]', 10.04, 0, 'completed', '2026-03-25 17:00:02'),
(36, 'Friday Lotto', '2026-03-27', '[]', '[]', 10.03, 0, 'completed', '2026-03-27 17:00:02'),
(37, 'Monday Lotto', '2026-03-30', '[]', '[]', 10.00, 0, 'completed', '2026-03-30 17:00:02'),
(38, 'Wednesday Lotto', '2026-04-01', '[]', '[]', 10.04, 0, 'completed', '2026-04-01 17:00:03'),
(39, 'Friday Lotto', '2026-04-03', '[]', '[]', 10.07, 0, 'completed', '2026-04-03 17:00:02'),
(40, 'Monday Lotto', '2026-04-06', '[]', '[]', 11.53, 0, 'completed', '2026-04-06 17:00:02'),
(41, 'Wednesday Lotto', '2026-04-08', '[]', '[]', 10.57, 0, 'completed', '2026-04-08 17:00:02'),
(42, 'Friday Lotto', '2026-04-10', '[]', '[]', 10.77, 0, 'completed', '2026-04-10 17:00:02'),
(43, 'Friday Lotto', '2026-04-10', '[]', '[]', 10.00, 0, 'completed', '2026-04-10 17:59:02'),
(44, 'Friday Lotto', '2026-04-10', '[]', '[]', 10.00, 0, 'completed', '2026-04-10 18:00:03'),
(45, 'Monday Lotto', '2026-04-13', '[]', '[]', 10.26, 0, 'completed', '2026-04-13 17:00:02'),
(46, 'Wednesday Lotto', '2026-04-15', '[]', '[]', 10.03, 0, 'completed', '2026-04-15 17:00:02'),
(47, 'Friday Lotto', '2026-04-17', '[]', '[]', 10.31, 0, 'completed', '2026-04-17 17:00:02'),
(48, 'Monday Lotto', '2026-04-20', '[]', '[]', 10.77, 0, 'completed', '2026-04-20 17:00:03'),
(49, 'Wednesday Lotto', '2026-04-22', '[]', '[]', 10.52, 0, 'completed', '2026-04-22 17:00:03'),
(50, 'Friday Lotto', '2026-04-24', '[]', '[]', 10.20, 0, 'completed', '2026-04-24 17:00:02'),
(51, 'Monday Lotto', '2026-04-27', '[]', '[]', 10.03, 0, 'completed', '2026-04-27 17:00:03'),
(52, 'Wednesday Lotto', '2026-04-29', '[]', '[]', 10.02, 0, 'completed', '2026-04-29 17:00:03'),
(53, 'Friday Lotto', '2026-05-01', '[]', '[]', 10.05, 0, 'completed', '2026-05-01 17:00:03'),
(54, 'Monday Lotto', '2026-05-04', '[]', '[]', 10.02, 0, 'completed', '2026-05-04 17:00:03'),
(55, 'Wednesday Lotto', '2026-05-06', '[]', '[]', 10.07, 0, 'completed', '2026-05-06 17:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `winner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','cancelled') DEFAULT 'pending',
  `payment_details` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `winner_id`, `user_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_details`, `approved_by`, `approved_at`, `processed_at`, `created_at`) VALUES
(1, 6, 4, 10.11, NULL, NULL, 'completed', NULL, 3, '2026-02-27 15:50:49', NULL, '2026-02-27 13:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `winning_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`winning_numbers`)),
  `bonus_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bonus_numbers`)),
  `draw_date` datetime NOT NULL,
  `jackpot` decimal(15,2) NOT NULL,
  `winners` int(11) DEFAULT 0,
  `status` varchar(20) DEFAULT 'published',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`id`, `lottery`, `winning_numbers`, `bonus_numbers`, `draw_date`, `jackpot`, `winners`, `status`, `notes`, `created_at`) VALUES
(9, 'Wednesday Lotto', '[1,34,56,45,12]', '[75,70]', '2026-01-14 00:00:00', 15.00, 0, 'published', '', '2026-01-11 20:07:55'),
(11, 'Monday Lotto', '[12, 13, 14, 15, 16]', '[10, 20]', '2026-01-19 00:00:00', 810.03, 0, 'published', '', '2026-01-17 12:34:49'),
(12, 'Friday Lotto', '[20,24,67,23,45]', '[10,30]', '2026-01-16 00:00:00', 10.03, 0, 'published', '', '2026-01-20 09:53:16'),
(13, 'Wednesday Lotto', '[1,2,3,4,5]', '[1,2]', '2026-01-21 00:00:00', 10.89, 2, 'published', '', '2026-01-21 17:03:12'),
(14, 'Friday Lotto', '[23,33,44,45,46]', '[2,3]', '2026-01-23 00:00:00', 10.22, 0, 'published', '', '2026-01-23 17:04:05'),
(15, 'Monday Lotto', '[11,14,36,51,60]', '[3,38]', '2026-01-26 00:00:00', 10.02, 0, 'published', '', '2026-01-27 20:45:28'),
(16, 'Wednesday Lotto', '[32,33,42,43,53]', '[21,31]', '2026-01-28 00:00:00', 110.00, 0, 'published', '', '2026-01-28 20:00:22'),
(17, 'Friday Lotto', '[2,4,6,8,10]', '[12,14]', '2026-01-30 00:00:00', 10.13, 0, 'published', '', '2026-01-30 17:15:23'),
(18, 'Monday Lotto', '[33,34,45,56,63]', '[2,6]', '2026-02-02 00:00:00', 1240.00, 0, 'published', '', '2026-02-02 17:22:39'),
(40, 'Wednesday Lotto', '[10,13,14,25,26]', '[2,16]', '2026-02-04 19:15:00', 10.11, 2, 'published', '', '2026-02-05 21:19:06'),
(41, 'Friday Lotto', '[14,20,27,50,68]', '[24,54]', '2026-02-06 00:00:00', 10.15, 1, 'published', '', '2026-02-06 17:03:52'),
(42, 'Monday Lotto', '[20,40,50,60,70]', '[5,10]', '2026-02-09 00:00:00', 10.08, 0, 'published', '', '2026-02-09 19:48:33'),
(43, 'Wednesday Lotto', '[36,37,44,45,46]', '[31,41]', '2026-02-11 00:00:00', 10.04, 1, 'published', '', '2026-02-11 17:05:18'),
(44, 'Friday Lotto', '[1,7,12,32,59]', '[27,30]', '2026-02-13 19:00:00', 10.04, 0, 'published', '', '2026-02-13 18:48:34'),
(45, 'Monday Lotto', '[3,8,21,31,46]', '[24,42]', '2026-02-16 19:05:00', 10.03, 0, 'published', '', '2026-02-16 17:06:21'),
(46, 'Wednesday Lotto', '[2,12,13,40,52]', '[20,29]', '2026-02-18 00:00:00', 10.04, 0, 'published', '', '2026-02-18 18:46:37'),
(47, 'Friday Lotto', '[3,11,12,14,18]', '[13,23]', '2026-02-20 00:00:00', 10.02, 0, 'published', '', '2026-02-20 17:07:07'),
(48, 'Monday Lotto', '[1,21,26,31,39]', '[6,15]', '2026-02-23 19:15:00', 10.09, 0, 'published', '', '2026-02-23 18:45:57'),
(49, 'Wednesday Lotto', '[1,3,6,14,50]', '[35,35]', '2026-02-25 19:15:00', 10.05, 0, 'published', '', '2026-02-25 20:36:55'),
(50, 'Friday Lotto', '[38,48,58,67,68]', '[32,33]', '2026-02-27 19:15:00', 10.09, 1, 'published', '', '2026-02-27 17:11:34'),
(51, 'Monday Lotto', '[3,11,14,39,48]', '[10,14]', '2026-03-02 00:00:00', 10.01, 0, 'published', '', '2026-03-02 21:23:01'),
(52, 'Wednesday Lotto', '[21,30,31,38,75]', '[4,57]', '2026-03-04 00:00:00', 10.10, 0, 'published', '', '2026-03-04 17:11:11'),
(53, 'Friday Lotto', '[9,17,21,49,60]', '[34,37]', '2026-03-06 00:00:00', 10.03, 0, 'published', '', '2026-03-06 19:49:58'),
(54, 'Monday Lotto', '[6,12,16,49,61]', '[25,27]', '2026-03-09 20:00:00', 10.03, 0, 'published', '', '2026-03-10 08:04:57'),
(55, 'Wednesday Lotto', '[46,52,58,68,74]', '[1,26]', '2026-03-11 20:00:00', 10.03, 0, 'published', '', '2026-03-12 06:19:13'),
(56, 'Friday Lotto', '[2,6,10,29,54]', '[4,18]', '2026-03-13 20:00:00', 10.01, 0, 'published', '', '2026-03-14 16:04:00'),
(57, 'Monday Lotto', '[2,12,16,20,36]', '[37,41]', '2026-03-16 20:00:00', 10.00, 0, 'published', '', '2026-03-16 18:09:11'),
(58, 'Wednesday Lotto', '[2,6,14,22,35]', '[5,17]', '2026-03-18 20:00:00', 10.00, 0, 'published', '', '2026-03-18 18:08:33'),
(59, 'Friday Lotto', '[7,14,48,66,73]', '[53,72]', '2026-03-20 20:00:00', 10.00, 0, 'published', '', '2026-03-21 06:13:32'),
(60, 'Monday Lotto', '[3,11,30,41,42]', '[1,4]', '2026-03-23 20:00:00', 10.03, 0, 'published', '', '2026-03-24 15:33:25'),
(61, 'Wednesday Lotto', '[1,7,36,48,52]', '[25,39]', '2026-03-25 00:00:00', 10.04, 0, 'published', '', '2026-03-25 20:06:48'),
(62, 'Friday Lotto', '[10,20,30,40,50]', '[60,70]', '2026-03-27 00:00:00', 10.03, 0, 'published', '', '2026-03-29 12:37:56'),
(63, 'Monday Lotto', '[5,10,15,23,12]', '[20,25]', '2026-03-30 00:00:00', 10.00, 0, 'published', '', '2026-03-31 06:46:54'),
(64, 'Wednesday Lotto', '[1,13,26,54,69]', '[14,68]', '2026-04-01 00:00:00', 10.04, 1, 'published', '', '2026-04-01 18:38:36'),
(65, 'Friday Lotto', '[10,20,33,12,34]', '[1,11]', '2026-04-03 00:00:00', 10.07, 0, 'published', '', '2026-04-04 01:18:12'),
(66, 'Monday Lotto', '[1,2,5,7,9]', '[3,4]', '2026-04-06 00:00:00', 11.53, 0, 'published', '', '2026-04-08 16:14:38'),
(67, 'Wednesday Lotto', '[2,3,8,13,14]', '[4,7]', '2026-04-08 00:00:00', 10.57, 0, 'published', '', '2026-04-09 05:45:30'),
(68, 'Friday Lotto', '[2,4,5,70,75]', '[8,10]', '2026-04-10 00:00:00', 10.77, 0, 'published', '', '2026-04-11 05:51:55'),
(69, 'Monday Lotto', '[10,20,30,40,50]', '[60,70]', '2026-04-13 00:00:00', 10.26, 0, 'published', '', '2026-04-14 09:44:56'),
(70, 'Wednesday Lotto', '[6,11,23,44,51]', '[12,25]', '2026-04-15 20:00:00', 10.03, 0, 'published', '', '2026-04-16 11:47:07'),
(71, 'Friday Lotto', '[21,69,71,73,74]', '[7,31]', '2026-04-17 20:00:00', 10.31, 0, 'published', '', '2026-04-17 18:00:44'),
(72, 'Monday Lotto', '[14,28,48,49,63]', '[14,40]', '2026-04-20 20:00:00', 10.77, 0, 'published', '', '2026-04-21 14:13:23'),
(73, 'Wednesday Lotto', '[2,15,24,56,74]', '[20,50]', '2026-04-22 20:00:00', 10.52, 0, 'published', '', '2026-04-22 18:42:24'),
(74, 'Friday Lotto', '[5,10,20,53,60]', '[10,12]', '2026-04-24 20:00:00', 10.20, 0, 'published', '', '2026-04-24 19:18:37'),
(75, 'Monday Lotto', '[10,20,33,52,70]', '[1,45]', '2026-04-27 20:00:00', 10.03, 0, 'published', '', '2026-04-27 18:18:51'),
(76, 'Wednesday Lotto', '[2,17,42,50,70]', '[28,44]', '2026-04-29 20:00:00', 10.02, 0, 'published', '', '2026-04-29 20:39:55'),
(77, 'Friday Lotto', '[5,12,19,45,70]', '[20,51]', '2026-05-01 20:00:00', 10.05, 0, 'published', '', '2026-05-01 18:41:42'),
(78, 'Monday Lotto', '[5,12,55,60,70]', '[8,16]', '2026-05-04 20:00:00', 10.02, 0, 'published', '', '2026-05-04 19:26:19'),
(79, 'Wednesday Lotto', '[45,47,51,55,70]', '[6,66]', '2026-05-06 20:00:00', 10.07, 0, 'published', '', '2026-05-07 08:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `upcoming_draw`
--

CREATE TABLE `upcoming_draw` (
  `id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `draw_date` datetime NOT NULL,
  `jackpot` decimal(10,2) DEFAULT 10.00,
  `status` varchar(20) DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `upcoming_draw`
--

INSERT INTO `upcoming_draw` (`id`, `lottery`, `draw_date`, `jackpot`, `status`, `created_at`) VALUES
(62, 'Friday Lotto', '2026-05-08 19:00:00', 10.03, 'scheduled', '2026-05-01 17:00:03'),
(63, 'Monday Lotto', '2026-05-11 19:00:00', 10.00, 'scheduled', '2026-05-04 17:00:03'),
(64, 'Wednesday Lotto', '2026-05-13 19:00:00', 10.00, 'scheduled', '2026-05-06 17:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `profile_picture` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `full_name`, `email`, `phone`, `country`, `profile_picture`, `password`, `role`, `is_active`, `created_at`) VALUES
(3, 'Free Lotto', 'lebomona78@gmail.com', NULL, 'Lesotho', NULL, '$2y$12$un0rlEjJVbeQyc2T8ob17uwPZAFy1IYTZ25t.XLIJ2p1Qb1p4KiF.', 'admin', 1, '2026-01-11 07:07:37'),
(4, 'Dola Nhlapho', 'dolanhlapho@gmail.com', '0671550036', NULL, NULL, '$2y$10$WQ22GAiGemhUOps4L9OEI.2QDjDYQ1yo27H/tetnSdK1K1Uosmx0q', 'user', 1, '2026-01-20 10:19:05'),
(6, 'Annamaria fiore', 'fioreannamaria309@gmail.com', '3793254797', NULL, NULL, '$2y$10$c4G2hAzm8.XbhqJwS5CsVeM1wK7RezDgfIBrrzvWBDleDSOi4/iyW', 'user', 1, '2026-02-03 17:19:24'),
(7, 'Annamaria fiore', 'fioreannamaria304@gmail.com', '3334043914', NULL, NULL, '$2y$10$TF5dZCY2v.xiw2q2q/Ik1eBHAAA6ZEJr8xWbsHDO7wffBRIX0DS06', 'user', 1, '2026-02-03 17:23:38'),
(8, 'Annamaria fiore', 'ciaoluna61@gmail.com', '0984641222', NULL, NULL, '$2y$10$g9zGq2wajJum/7A./LYxvehG2LieNraKKKdNwOV8ECuHuXE.dZ70W', 'user', 1, '2026-02-04 13:37:18'),
(9, 'Mpolokeng Evelina Seekane', 'seekanempolokeng845@gmail.com', '53460000', NULL, NULL, '$2y$10$YGY.jjYP.DZshb7w142qKu1SmHG2bRiAiIv7nF3/0DjHB.VTR/Xvu', 'user', 1, '2026-02-18 04:57:12'),
(11, 'Free Lotto1', 'totalfreelotto494@gmail.com', NULL, NULL, NULL, '$2y$12$ePPDySbeBsVlCI/GXavv8e/IMJzRxTQhUMJKys1bv0.LZDYWBT5xy', 'admin', 1, '2026-02-22 13:10:48'),
(12, 'Lebohang Monamane', 'monamane.lebohang45@gmail.com', '59181664', 'Lesotho', NULL, '$2y$10$7K2jrZfEiisl9b1eiQdUjOsnlqKgtTCwfGdogUjZaBt8KlJwE/6ya', 'user', 1, '2026-02-22 16:46:27'),
(13, 'Khauhelo Mosehle', 'khauhelomosehle266@gmail.com', '62793214', 'Lesotho', NULL, '$2y$10$jh0lycr4iW2ez07.Q/AvGe88EnuqLaejf4TKu3tBvqda4R2f2ckhK', 'user', 1, '2026-02-23 16:00:18'),
(14, 'Relebohile  Kometsi', 'relebohilek5@gmail.com', '+26651839736', 'Lesotho', NULL, '$2y$10$Ry4GLY9nx4Rnoycnmoau4ezpiBGO4YMXfCgJGBuaTJfL/YA00iUxe', 'user', 1, '2026-02-24 20:31:31'),
(15, 'Piotr Sudy', 'bessenny5@o2.pl', NULL, NULL, NULL, '$2y$10$DNPyMa6t.iwXrrGysQwnRubb6ifpUZR89uPthJWF4uRNOMAB8Umia', 'user', 1, '2026-03-03 12:27:11'),
(16, 'Jermaine Mattis', 'ramonemattis6@gmail.com', '8764205309', 'Jamaica', NULL, '$2y$10$1RjSGslih7r1Q.VhfI8JZuGQzYbQk16gxcdDzcvNxCs7c5SVJr9RG', 'user', 1, '2026-03-29 02:07:55'),
(17, 'ezequiel mansilla', 'ezequiel.mansilla355@gmail.com', NULL, 'Argentina', NULL, '$2y$10$FyARl8vtGk7c9yjEZ.QYIux4ZXttg5kEdnOlhvYvBF3bXLHIufBN6', 'user', 1, '2026-03-30 18:57:18'),
(18, 'ali kemal  ulusal', 'lordbananda@gmail.com', '5316622586', 'Türkiye', NULL, '$2y$10$rkN2Xd2g1.0B5.hj.cxIb.ahlRFEmbxXbt.o2tfdRD.PRRF3k3OGi', 'user', 1, '2026-04-02 19:04:36'),
(19, 'Md.Monzurul Islam', 'monzurulislam745@gmail.com', '+8801725474747', 'Bangladesh ', NULL, '$2y$10$sWEAri5b/DmPNaai5IoFmuUJd/MCej.5tZG.91J33AW.p7OC/Rc4m', 'user', 1, '2026-04-03 11:41:59'),
(20, 'Lions Kartel', 'bataungx@gmail.com', '63404009', 'Lesotho', NULL, '$2y$10$AlJJNOhClYB4U2RuW2wRY.sOx11Fj6jVjt2avh708j0QA8dPz5Xk.', 'user', 1, '2026-04-13 12:29:54'),
(21, 'Jeremy McLaren', 'jeremymclaren30@yahoo.com.au', '0455092899', 'Australia', NULL, '$2y$10$pz4txhkKDo9XGRNXGG0W/eKb9MjWyhUUnk3.caijeqVPhllI6RZsy', 'user', 1, '2026-04-16 00:58:45'),
(22, 'Imran Haider', 'imraninranhaider@gmail.com', '00923274645084', 'Pakistan', NULL, '$2y$10$TWyhRvSQHoAE0f.svkJKguELkveinu.CWfw/DZfhkxz3v804qECgS', 'user', 1, '2026-04-17 14:46:22'),
(23, 'Karla Hubbard', 'hubbardkarla10@gmail.com', '3302315312', 'United States', NULL, '$2y$10$xnGpjKvJ9wkXwWln36g3vOLIGQYjljtQS6GMycqWSk8MFz62WIvd6', 'user', 1, '2026-04-17 17:40:17'),
(24, 'Bokang  Letsoso', 'marceloletsoso505@gmail.com', '57978674', 'Lesotho', NULL, '$2y$10$6rYRP7KttalW6vDKyTO6C.IF/GF5kvOkAsPRmWLtYU43.HcXDipym', 'user', 1, '2026-04-22 18:11:36'),
(25, 'Abdulghafour Mohamed', 'abdulghafour.mohamed21@outlook.com', '01020345532', 'Egypt', NULL, '$2y$10$MtPGxU0MlxH3PAQUkq8XnupjOmYx9DmAuduELTlVDeA9iCayUffs.', 'user', 1, '2026-04-25 17:28:16'),
(26, 'Sheeja Kr', 'sajinpr7@gmail.com', '928653375', 'India ', NULL, '$2y$10$EVXZIOMnU4wf1psc.cOuXOp12SPbyZ7yWbBN0bOO4NTrxLR4BuffW', 'user', 1, '2026-04-27 13:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE `vote` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`numbers`)),
  `bonus_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`bonus_numbers`)),
  `vote_date` date NOT NULL,
  `draw_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`id`, `user_id`, `lottery`, `numbers`, `bonus_numbers`, `vote_date`, `draw_date`, `created_at`, `updated_at`) VALUES
(16, 12, 'Wednesday Lotto', '[9,29,52,65,11]', '[8,49]', '2026-03-11', '2026-03-11', '2026-03-11 13:42:42', NULL),
(17, 4, 'Wednesday Lotto', '[9,1,11,34,46]', '[8,15]', '2026-03-11', '2026-03-11', '2026-03-11 15:30:52', NULL),
(18, 4, 'Monday Lotto', '[1,2,3,8,13]', '[1,2]', '2026-03-21', '2026-03-21', '2026-03-21 20:45:12', NULL),
(19, 4, 'Wednesday Lotto', '[1,2,3,4,5]', '[1,2]', '2026-03-24', '2026-03-24', '2026-03-24 17:43:00', NULL),
(20, 12, 'Wednesday Lotto', '[27,46,54,55,61]', '[51,62]', '2026-04-01', '2026-04-01', '2026-04-01 16:39:51', NULL),
(21, 12, 'Monday Lotto', '[25,33,38,39,48]', '[12,14]', '2026-04-04', '2026-04-06', '2026-04-04 01:15:14', NULL),
(22, 19, 'Friday Lotto', '[2,13,16,22,75]', '[32,52]', '2026-04-09', '2026-04-10', '2026-04-09 04:45:07', NULL),
(23, 19, 'Friday Lotto', '[3,31,37,68,72]', '[42,49]', '2026-04-09', '2026-04-10', '2026-04-09 04:46:52', NULL),
(24, 19, 'Friday Lotto', '[2,17,21,25,42]', '[1,39]', '2026-04-09', '2026-04-10', '2026-04-09 04:56:24', NULL),
(25, 19, 'Friday Lotto', '[6,35,44,53,71]', '[28,36]', '2026-04-09', '2026-04-10', '2026-04-09 04:57:54', NULL),
(26, 19, 'Friday Lotto', '[14,35,47,52,61]', '[3,28]', '2026-04-09', '2026-04-10', '2026-04-09 04:58:53', NULL),
(27, 19, 'Friday Lotto', '[3,19,21,34,74]', '[20,37]', '2026-04-09', '2026-04-10', '2026-04-09 04:59:27', NULL),
(28, 19, 'Friday Lotto', '[15,31,34,52,53]', '[8,25]', '2026-04-09', '2026-04-10', '2026-04-09 05:00:12', NULL),
(29, 19, 'Friday Lotto', '[19,36,48,51,56]', '[53,59]', '2026-04-09', '2026-04-10', '2026-04-09 05:02:49', NULL),
(30, 19, 'Friday Lotto', '[2,38,55,63,69]', '[41,49]', '2026-04-09', '2026-04-10', '2026-04-09 14:34:01', NULL),
(31, 19, 'Friday Lotto', '[10,14,41,44,73]', '[48,66]', '2026-04-09', '2026-04-10', '2026-04-09 14:34:43', NULL),
(32, 19, 'Friday Lotto', '[2,12,16,25,67]', '[4,73]', '2026-04-09', '2026-04-10', '2026-04-09 14:35:13', NULL),
(33, 19, 'Friday Lotto', '[16,35,62,67,72]', '[29,42]', '2026-04-09', '2026-04-10', '2026-04-09 14:35:40', NULL),
(34, 19, 'Friday Lotto', '[12,20,31,47,70]', '[15,38]', '2026-04-09', '2026-04-10', '2026-04-09 14:36:18', NULL),
(35, 19, 'Friday Lotto', '[10,24,46,71,75]', '[40,73]', '2026-04-09', '2026-04-10', '2026-04-09 14:36:51', NULL),
(36, 19, 'Friday Lotto', '[1,54,59,61,73]', '[36,62]', '2026-04-09', '2026-04-10', '2026-04-09 14:37:29', NULL),
(37, 19, 'Friday Lotto', '[2,24,45,61,64]', '[8,9]', '2026-04-09', '2026-04-10', '2026-04-09 14:38:07', NULL),
(38, 19, 'Friday Lotto', '[10,19,45,70,71]', '[8,56]', '2026-04-09', '2026-04-10', '2026-04-09 14:38:35', NULL),
(39, 19, 'Friday Lotto', '[6,8,32,34,66]', '[12,14]', '2026-04-10', '2026-04-10', '2026-04-10 03:00:16', NULL),
(40, 19, 'Friday Lotto', '[10,26,38,39,71]', '[20,36]', '2026-04-10', '2026-04-10', '2026-04-10 03:00:57', NULL),
(41, 19, 'Friday Lotto', '[20,22,33,64,71]', '[28,69]', '2026-04-10', '2026-04-10', '2026-04-10 03:01:40', NULL),
(42, 19, 'Friday Lotto', '[32,33,39,54,67]', '[3,34]', '2026-04-10', '2026-04-10', '2026-04-10 03:02:18', NULL),
(43, 19, 'Friday Lotto', '[4,44,46,57,67]', '[5,33]', '2026-04-10', '2026-04-10', '2026-04-10 03:02:50', NULL),
(44, 4, 'Monday Lotto', '[1,2,3,4,5]', '[6,7]', '2026-04-11', '2026-04-13', '2026-04-11 11:29:29', NULL),
(45, 4, 'Monday Lotto', '[3,13,23,33,43]', '[5,15]', '2026-04-11', '2026-04-13', '2026-04-11 11:30:05', NULL),
(46, 4, 'Monday Lotto', '[1,3,4,6,7]', '[13,23]', '2026-04-11', '2026-04-13', '2026-04-11 11:30:40', NULL),
(47, 4, 'Monday Lotto', '[2,13,23,34,44]', '[6,7]', '2026-04-11', '2026-04-13', '2026-04-11 11:31:13', NULL),
(48, 4, 'Monday Lotto', '[23,33,43,54,63]', '[5,6]', '2026-04-11', '2026-04-13', '2026-04-11 11:33:41', NULL),
(49, 4, 'Monday Lotto', '[2,3,7,8,13]', '[6,12]', '2026-04-11', '2026-04-13', '2026-04-11 11:38:12', NULL),
(50, 19, 'Monday Lotto', '[3,31,45,61,67]', '[11,39]', '2026-04-12', '2026-04-13', '2026-04-12 08:51:26', NULL),
(51, 19, 'Monday Lotto', '[9,14,38,55,71]', '[11,53]', '2026-04-12', '2026-04-13', '2026-04-12 08:51:54', NULL),
(52, 19, 'Monday Lotto', '[9,14,38,55,71]', '[11,53]', '2026-04-12', '2026-04-13', '2026-04-12 08:51:58', NULL),
(53, 19, 'Monday Lotto', '[13,19,44,45,74]', '[18,70]', '2026-04-12', '2026-04-13', '2026-04-12 08:52:29', NULL),
(54, 19, 'Monday Lotto', '[1,9,23,52,71]', '[5,57]', '2026-04-12', '2026-04-13', '2026-04-12 08:52:54', NULL),
(55, 19, 'Wednesday Lotto', '[9,18,24,43,61]', '[7,39]', '2026-04-14', '2026-04-15', '2026-04-14 08:14:12', NULL),
(56, 12, 'Friday Lotto', '[16,37,38,65,68]', '[15,61]', '2026-04-15', '2026-04-17', '2026-04-15 17:00:32', NULL),
(57, 19, 'Friday Lotto', '[1,14,19,27,54]', '[20,52]', '2026-04-24', '2026-04-24', '2026-04-24 11:40:45', NULL),
(58, 19, 'Friday Lotto', '[21,49,50,59,73]', '[34,70]', '2026-04-24', '2026-04-24', '2026-04-24 11:41:17', NULL),
(59, 19, 'Friday Lotto', '[6,26,40,66,67]', '[33,51]', '2026-04-24', '2026-04-24', '2026-04-24 11:41:43', NULL),
(60, 19, 'Friday Lotto', '[4,11,31,47,72]', '[15,64]', '2026-04-24', '2026-04-24', '2026-04-24 11:42:20', NULL),
(61, 19, 'Friday Lotto', '[10,33,40,51,53]', '[34,75]', '2026-04-24', '2026-04-24', '2026-04-24 11:42:55', NULL),
(62, 19, 'Friday Lotto', '[27,47,66,67,70]', '[32,39]', '2026-04-24', '2026-04-24', '2026-04-24 11:43:25', NULL),
(63, 19, 'Friday Lotto', '[21,29,61,72,73]', '[13,31]', '2026-04-24', '2026-04-24', '2026-04-24 11:43:51', NULL),
(64, 19, 'Friday Lotto', '[11,19,36,40,68]', '[14,27]', '2026-04-24', '2026-04-24', '2026-04-24 11:44:18', NULL),
(65, 19, 'Friday Lotto', '[3,17,31,51,73]', '[6,59]', '2026-04-24', '2026-04-24', '2026-04-24 11:44:45', NULL),
(66, 19, 'Wednesday Lotto', '[20,50,57,60,71]', '[10,22]', '2026-05-05', '2026-05-06', '2026-05-05 19:03:53', NULL),
(67, 19, 'Wednesday Lotto', '[28,33,41,49,72]', '[18,30]', '2026-05-05', '2026-05-06', '2026-05-05 19:04:33', NULL),
(68, 19, 'Wednesday Lotto', '[1,3,10,11,33]', '[6,46]', '2026-05-05', '2026-05-06', '2026-05-05 19:05:14', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vote_statistics`
-- (See below for the actual view)
--
CREATE TABLE `vote_statistics` (
`lottery` varchar(50)
,`draw_date` date
,`total_user_votes` bigint(21)
,`unique_voters` bigint(21)
,`all_numbers` longtext
,`all_bonus_numbers` longtext
);

-- --------------------------------------------------------

--
-- Table structure for table `winner`
--

CREATE TABLE `winner` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `result_id` int(11) DEFAULT NULL,
  `entry_id` int(11) DEFAULT NULL,
  `lottery` varchar(50) NOT NULL,
  `prize_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `payment_date` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `winner`
--

INSERT INTO `winner` (`id`, `user_id`, `result_id`, `entry_id`, `lottery`, `prize_amount`, `payment_status`, `status`, `payment_date`, `paid_at`, `created_at`) VALUES
(6, 4, 40, 117, 'Wednesday Lotto', 10.11, 'pending', 'paid', NULL, '2026-02-27 15:50:49', '2026-02-05 21:19:06'),
(10, 4, 50, 173, '', 10.09, 'pending', 'pending', NULL, NULL, '2026-02-27 17:11:34'),
(11, 12, 64, 217, '', 10.04, 'pending', 'pending', NULL, NULL, '2026-04-01 18:38:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `admin_vote`
--
ALTER TABLE `admin_vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lottery_date` (`lottery`,`vote_date`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_lottery_draw_date` (`lottery`,`draw_date`),
  ADD KEY `idx_admin_lottery_draw_date` (`lottery`,`draw_date`),
  ADD KEY `idx_admin_vote_draw_date` (`draw_date`),
  ADD KEY `idx_admin_vote_lottery_draw` (`lottery`,`draw_date`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_file`
--
ALTER TABLE `data_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category` (`file_category`);

--
-- Indexes for table `entry`
--
ALTER TABLE `entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lottery_draw` (`lottery`,`draw_date`),
  ADD KEY `idx_user_date` (`user_id`,`created_at`);

--
-- Indexes for table `leading_numbers_snapshot`
--
ALTER TABLE `leading_numbers_snapshot`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_leading_numbers_snapshot` (`lottery`,`draw_date`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sent_by` (`sent_by`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_code` (`reset_code`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `past_draw`
--
ALTER TABLE `past_draw`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `winner_id` (`winner_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_transaction` (`transaction_id`);

--
-- Indexes for table `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `upcoming_draw`
--
ALTER TABLE `upcoming_draw`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_lottery_date` (`lottery`,`draw_date`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `user_profile_picture_fk` (`profile_picture`);

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lottery_date` (`lottery`,`vote_date`),
  ADD KEY `idx_user_date` (`user_id`,`vote_date`),
  ADD KEY `idx_vote_lottery_draw_date` (`lottery`,`draw_date`),
  ADD KEY `idx_vote_draw_date` (`draw_date`),
  ADD KEY `idx_vote_lottery_draw` (`lottery`,`draw_date`);

--
-- Indexes for table `winner`
--
ALTER TABLE `winner`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `winner_result_fk` (`result_id`),
  ADD KEY `winner_entry_fk` (`entry_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=739;

--
-- AUTO_INCREMENT for table `admin_vote`
--
ALTER TABLE `admin_vote`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_file`
--
ALTER TABLE `data_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entry`
--
ALTER TABLE `entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=742;

--
-- AUTO_INCREMENT for table `leading_numbers_snapshot`
--
ALTER TABLE `leading_numbers_snapshot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `past_draw`
--
ALTER TABLE `past_draw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `upcoming_draw`
--
ALTER TABLE `upcoming_draw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vote`
--
ALTER TABLE `vote`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `winner`
--
ALTER TABLE `winner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------

--
-- Structure for view `admin_vote_statistics`
--
DROP TABLE IF EXISTS `admin_vote_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u606331557_lottery_db`@`127.0.0.1` SQL SECURITY DEFINER VIEW `admin_vote_statistics`  AS SELECT `admin_vote`.`lottery` AS `lottery`, `admin_vote`.`draw_date` AS `draw_date`, sum(`admin_vote`.`allocated_votes`) AS `total_allocated_votes`, sum(`admin_vote`.`total_votes`) AS `total_admin_votes`, count(0) AS `allocation_count` FROM `admin_vote` GROUP BY `admin_vote`.`lottery`, `admin_vote`.`draw_date` ;

-- --------------------------------------------------------

--
-- Structure for view `vote_statistics`
--
DROP TABLE IF EXISTS `vote_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u606331557_lottery_db`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vote_statistics`  AS SELECT `vote`.`lottery` AS `lottery`, `vote`.`draw_date` AS `draw_date`, count(0) AS `total_user_votes`, count(distinct `vote`.`user_id`) AS `unique_voters`, json_arrayagg(`vote`.`numbers`) AS `all_numbers`, json_arrayagg(`vote`.`bonus_numbers`) AS `all_bonus_numbers` FROM `vote` GROUP BY `vote`.`lottery`, `vote`.`draw_date` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_vote`
--
ALTER TABLE `admin_vote`
  ADD CONSTRAINT `admin_vote_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `data_file`
--
ALTER TABLE `data_file`
  ADD CONSTRAINT `data_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `entry`
--
ALTER TABLE `entry`
  ADD CONSTRAINT `entry_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`sent_by`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`winner_id`) REFERENCES `winner` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_profile_picture_fk` FOREIGN KEY (`profile_picture`) REFERENCES `data_file` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `winner`
--
ALTER TABLE `winner`
  ADD CONSTRAINT `winner_entry_fk` FOREIGN KEY (`entry_id`) REFERENCES `entry` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `winner_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `winner_result_fk` FOREIGN KEY (`result_id`) REFERENCES `result` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
