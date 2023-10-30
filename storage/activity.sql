-- phpMyAdmin SQL Dump
-- version 5.2.1deb1ubuntu0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 30, 2023 at 03:38 PM
-- Server version: 8.0.34-0ubuntu0.23.04.1
-- PHP Version: 8.1.12-1ubuntu4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cikanation_db`
--
TRUNCATE TABLE activity_log;
--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'User Login', 'User Login successfully', 'App\\Models\\User', NULL, 11, 'App\\Models\\User', 11, '{\"ip\": \"127.0.0.1\", \"target\": \"test\", \"activity\": \"User Login successfully\"}', NULL, '2023-10-30 09:37:38', '2023-10-30 09:37:38'),
(2, 'Role created', 'Test User created Role Writer.', 'Spatie\\Permission\\Models\\Role', NULL, 2, 'App\\Models\\User', 11, '{\"ip\": \"127.0.0.1\", \"target\": \"Writer\", \"activity\": \"Role created successfully\"}', NULL, '2023-10-30 09:38:21', '2023-10-30 09:38:21');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;