-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 18, 2016 at 04:16 PM
-- Server version: 5.5.46-0+deb8u1
-- PHP Version: 5.6.17-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizzlebot`
--

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE `answer` (
  `id` int(10) UNSIGNED NOT NULL,
  `riddle_id` int(10) UNSIGNED NOT NULL,
  `identity_id` int(10) UNSIGNED NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`id`, `riddle_id`, `identity_id`, `text`, `last_update`) VALUES
(1, 32, 30, 'la risposta ESATTA', '2016-10-18 14:01:46'),
(2, 33, 31, 'la risposta ESATTA', '2016-10-18 14:11:32'),
(3, 33, 32, 'la risposta ESATTA', '2016-10-18 14:12:04'),
(4, 33, 33, 'la risposta ESATTA', '2016-10-18 14:13:02'),
(5, 33, 34, 'la risposta ESATTA', '2016-10-18 14:14:21'),
(6, 33, 35, 'la risposta ESATTA', '2016-10-18 14:14:51'),
(7, 33, 36, 'la risposta ESATTA', '2016-10-18 14:15:21'),
(8, 33, 37, 'la risposta ESATTA', '2016-10-18 14:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `indentity`
--

CREATE TABLE `indentity` (
  `id` int(10) UNSIGNED NOT NULL,
  `telegram_id` int(11) NOT NULL,
  `first_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_seen_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `indentity`
--

INSERT INTO `indentity` (`id`, `telegram_id`, `first_name`, `full_name`, `first_seen_on`, `is_admin`) VALUES
(1, 456789, 'Ciaone', 'Ciaone Lorenzone', '2016-10-17 22:00:00', 0),
(2, 456789, 'Ciaone', 'Ciaone Lorenzone', '0000-00-00 00:00:00', 0),
(3, 45678, 'Ciaone', 'Lorenzone', '2016-10-18 12:24:46', 0),
(4, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 12:27:40', 0),
(5, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 12:38:46', 0),
(6, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:32:06', 0),
(7, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:34:22', 0),
(8, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:36:43', 0),
(9, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:37:18', 0),
(10, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:37:43', 0),
(11, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:39:11', 0),
(12, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:39:33', 0),
(13, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:40:04', 0),
(14, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:40:11', 0),
(15, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:41:42', 0),
(16, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:42:12', 0),
(17, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:42:20', 0),
(18, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:49:02', 0),
(19, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:49:52', 0),
(20, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:50:02', 0),
(21, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:50:52', 0),
(22, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:50:59', 0),
(23, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:51:10', 0),
(24, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:58:39', 0),
(25, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:59:08', 0),
(26, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 13:59:33', 0),
(27, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:00:09', 0),
(28, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:00:59', 0),
(29, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:01:39', 0),
(30, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:01:46', 0),
(31, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:11:32', 0),
(32, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:12:04', 0),
(33, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:13:02', 0),
(34, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:14:20', 0),
(35, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:14:51', 0),
(36, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:15:21', 0),
(37, 5678, 'Bello', 'Bello Lui Puccio Puccio', '2016-10-18 14:15:54', 0);

-- --------------------------------------------------------

--
-- Table structure for table `riddle`
--

CREATE TABLE `riddle` (
  `id` int(11) UNSIGNED NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `answer` text COLLATE utf8_bin,
  `end_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `riddle`
--

INSERT INTO `riddle` (`id`, `start_time`, `answer`, `end_time`) VALUES
(20, '2016-10-18 13:49:02', 'la risposta', '2016-10-18 13:49:02'),
(21, '2016-10-18 13:49:52', 'la risposta', '2016-10-18 13:49:52'),
(22, '2016-10-18 13:50:02', 'la risposta', '2016-10-18 13:50:02'),
(23, '2016-10-18 13:50:52', 'la risposta', '2016-10-18 13:50:52'),
(24, '2016-10-18 13:50:59', 'la risposta', '2016-10-18 13:50:59'),
(25, '2016-10-18 13:51:10', 'la risposta', '2016-10-18 13:51:10'),
(26, '2016-10-18 13:58:39', 'la risposta', '2016-10-18 13:58:39'),
(27, '2016-10-18 13:59:08', 'la risposta', '2016-10-18 13:59:08'),
(28, '2016-10-18 13:59:33', 'la risposta', '2016-10-18 13:59:33'),
(29, '2016-10-18 14:00:09', 'la risposta', '2016-10-18 14:00:09'),
(30, '2016-10-18 14:00:58', 'la risposta', '2016-10-18 14:00:58'),
(31, '2016-10-18 14:01:38', 'la risposta', '2016-10-18 14:01:39'),
(32, '2016-10-18 14:01:46', NULL, NULL),
(33, '2016-10-18 14:11:32', NULL, NULL),
(34, '2016-10-18 14:12:04', 'la risposta', '2016-10-18 14:12:04'),
(35, '2016-10-18 14:13:02', 'la risposta', '2016-10-18 14:13:02'),
(36, '2016-10-18 14:14:20', 'la risposta', '2016-10-18 14:14:20'),
(37, '2016-10-18 14:14:51', 'la risposta', '2016-10-18 14:14:51'),
(38, '2016-10-18 14:15:21', 'la risposta', '2016-10-18 14:15:21'),
(39, '2016-10-18 14:15:54', 'la risposta', '2016-10-18 14:15:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `riddle_id` (`riddle_id`),
  ADD KEY `identity_id` (`identity_id`);

--
-- Indexes for table `indentity`
--
ALTER TABLE `indentity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riddle`
--
ALTER TABLE `riddle`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `indentity`
--
ALTER TABLE `indentity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `riddle`
--
ALTER TABLE `riddle`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `identity_id_fkey` FOREIGN KEY (`identity_id`) REFERENCES `indentity` (`id`),
  ADD CONSTRAINT `riddle_id_fkey` FOREIGN KEY (`riddle_id`) REFERENCES `riddle` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
