-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2022 at 03:07 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `strikingdash`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_grid`
--

CREATE TABLE `contact_grid` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_grid`
--

INSERT INTO `contact_grid` (`id`, `user`, `image`, `designation`, `mobile`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Grant Marshall', 'img/tm9.png', 'User Interface Designer', '+1 (861) 550-2796', 'marshall@yahoo.com', '225 Bills Place', NULL, NULL),
(2, 'Pena Valdez', 'img/tm10.png', 'User Interface Designer', '+1 (909) 445-2527', 'valdez@yahoo.com', '266 Tiffany Place', NULL, NULL),
(3, 'Jessica Miles', 'img/tm11.png', 'Product Designer', '+1 (824) 487-2755', 'miles@mail.com', '240 Hillel Place', NULL, NULL),
(4, 'Natasha Gamble', 'img/tm12.png', 'PMotion Designer', '+1 (824) 487-2755', 'gamble@outlook.com', '463 Harbor Court', NULL, NULL),
(5, 'Grant Marshall', 'img/tm9.png', 'User Interface Designer', '+1 (861) 550-2796', 'marshall@yahoo.com', '225 Bills Place', NULL, NULL),
(6, 'Pena Valdez', 'img/tm13.png', 'User Interface Designer', '+1 (909) 445-2527', 'valdez@yahoo.com', '266 Tiffany Place', NULL, NULL),
(7, 'Jessica Miles', 'img/tm14.png', 'Product Designer', '+1 (824) 487-2755', 'miles@mail.com', '240 Hillel Place', NULL, NULL),
(8, 'Natasha Gamble', 'img/tm6.png', 'PMotion Designer', '+1 (824) 487-2755', 'gamble@outlook.com', '463 Harbor Court', NULL, NULL),
(9, 'Natasha Gamble', 'img/tm5.png', 'PMotion Designer', '+1 (824) 487-2755', 'gamble@outlook.com', '463 Harbor Court', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_list`
--

CREATE TABLE `contact_list` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `star` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_list`
--

INSERT INTO `contact_list` (`id`, `user`, `image`, `location`, `email`, `company`, `position`, `phone`, `star`, `created_at`, `updated_at`) VALUES
(1, 'Kellie Marquot', 'img/tm6.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 0, NULL, NULL),
(2, 'Kellie Marquot', 'img/tm1.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 1, NULL, NULL),
(3, 'Kellie Marquot', 'img/tm2.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 0, NULL, NULL),
(4, 'Kellie Marquot', 'img/tm3.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 1, NULL, NULL),
(5, 'Kellie Marquot', 'img/tm4.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 0, NULL, NULL),
(6, 'Kellie Marquot', 'img/tm5.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 1, NULL, NULL),
(7, 'Kellie Marquot', 'img/tm6.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 0, NULL, NULL),
(8, 'Kellie Marquot', 'img/tm1.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 0, NULL, NULL),
(9, 'Kellie Marquot', 'img/tm2.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 0, NULL, NULL),
(10, 'Kellie Marquot', 'img/tm3.png', 'San Francisco, CA', 'marshall@yahoo.com', 'Business Development', 'Web Develope', '+1 (861) 550-2796', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profession` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE `email` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `star` tinyint(1) NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` time NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email`
--

INSERT INTO `email` (`id`, `user`, `email`, `star`, `image`, `subject`, `body`, `time`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Alice Freeman', 'name@domain.com', 0, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'inbox', NULL, NULL),
(2, 'Alice Freeman', 'name@domain.com', 1, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'inbox', NULL, NULL),
(3, 'Alice Freeman', 'name@domain.com', 0, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'inbox', NULL, NULL),
(4, 'Alice Freeman', 'name@domain.com', 0, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'inbox', NULL, NULL),
(5, 'Alice Freeman', 'name@domain.com', 1, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'inbox', NULL, NULL),
(6, 'Alice Freeman', 'name@domain.com', 0, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'inbox', NULL, NULL),
(7, 'Alice Freeman', 'name@domain.com', 0, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'send', NULL, NULL),
(8, 'Alice Freeman', 'name@domain.com', 1, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'send', NULL, NULL),
(9, 'Alice Freeman', 'name@domain.com', 0, 'img/author/e1.png', 'Email subject lorem ipsum', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt', '08:30:00', 'send', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `facebook_overviews`
--

CREATE TABLE `facebook_overviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `engaged_users` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_impressions` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_page_likes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_page_likes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chart_data_one` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`chart_data_one`)),
  `chart_data_two` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`chart_data_two`)),
  `chart_data_three` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`chart_data_three`)),
  `chart_data_four` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`chart_data_four`)),
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facebook_overviews`
--

INSERT INTO `facebook_overviews` (`id`, `engaged_users`, `page_impressions`, `total_page_likes`, `new_page_likes`, `chart_data_one`, `chart_data_two`, `chart_data_three`, `chart_data_four`, `status`, `created_at`, `updated_at`) VALUES
(1, '25,845', '2,534', '2,142', '1,132', '[150,100,200,250,200,300,150]', '[200,150,200,250,100,200,150]', '[150,100,200,250,200,300,150]', '[200,150,200,250,100,200,150]', 'today', NULL, NULL),
(2, '26,845', '3,534', '3,142', '2,132', '[1000,5000,1500,10000,14000,24000,20000]', '[10000,15000,10000,15000,14000,24000,20000]', '[100,300,150,200,250,500,300]', '[100,300,150,200,250,500,300]', 'week', NULL, NULL),
(3, '28,845', '8,534', '9,142', '5,132', '[15000,50000,15000,15000,40000,24000,20000]', '[20000,40000,16000,15000,30000,23000,25000]', '[5000,4000,16000,10000,20000,13000,10000,6000]', '[1100,2300,1500,1900,500,1400,800,500]', 'month', NULL, NULL),
(4, '24,845', '5,534', '6,142', '3,132', '[95000,19000,55000,90000,240000,95000,55000,19000,55000,66000,90000,240000]', '[98000,20000,55000,90000,240000,95000,55000,19000,55000,66000,90000,240000]', '[9500,1900,5500,9000,24000,9500,5500,1900,5500,9000,10000,24000]', '[5000,19000,15000,19000,20000,35000,20000,19000,27000,10000,15000,20000]', 'year', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `firestores`
--

CREATE TABLE `firestores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `image`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Snow covered mountain', 'img/gallery.png', '1', NULL, NULL),
(2, 'Snow covered mountain', 'img/gallery2.png', '1', NULL, NULL),
(3, 'Snow covered mountain', 'img/gallery3.png', '1', NULL, NULL),
(4, 'Snow covered mountain', 'img/gallery4.png', '1', NULL, NULL),
(5, 'Snow covered mountain', 'img/gallery5.png', '1', NULL, NULL),
(6, 'Snow covered mountain', 'img/gallery6.png', '1', NULL, NULL),
(7, 'Snow covered mountain', 'img/gallery7.png', '1', NULL, NULL),
(8, 'Snow covered mountain', 'img/gallery8.png', '1', NULL, NULL),
(9, 'Snow covered mountain', 'img/gallery9.png', '1', NULL, NULL),
(10, 'Snow covered mountain', 'img/gallery10.png', '1', NULL, NULL),
(11, 'Snow covered mountain', 'img/gallery11.png', '1', NULL, NULL),
(12, 'Snow covered mountain', 'img/gallery4.png', '2', NULL, NULL),
(13, 'Snow covered mountain', 'img/gallery5.png', '2', NULL, NULL),
(14, 'Snow covered mountain', 'img/gallery6.png', '2', NULL, NULL),
(15, 'Snow covered mountain', 'img/gallery7.png', '2', NULL, NULL),
(16, 'Snow covered mountain', 'img/gallery.png', '3', NULL, NULL),
(17, 'Snow covered mountain', 'img/gallery2.png', '3', NULL, NULL),
(18, 'Snow covered mountain', 'img/gallery3.png', '3', NULL, NULL),
(19, 'Snow covered mountain', 'img/gallery.png', '4', NULL, NULL),
(20, 'Snow covered mountain', 'img/gallery2.png', '4', NULL, NULL),
(21, 'Snow covered mountain', 'img/gallery3.png', '4', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2021_02_02_041507_create_firestores_table', 1),
(6, '2021_03_15_031802_create_todos_table', 1),
(7, '2021_03_15_055412_create_notes_table', 1),
(8, '2021_05_18_103431_create_email_table', 1),
(9, '2021_05_19_075945_create_facebook_overviews_table', 1),
(10, '2021_05_19_090927_create_twitter_overviews_table', 1),
(11, '2021_05_20_023615_create_social_traffics_metrics_table', 1),
(12, '2021_05_20_082322_create_user_teams_table', 1),
(13, '2021_05_20_085316_create_users_grid_table', 1),
(14, '2021_05_23_055607_create_users_list_table', 1),
(15, '2021_05_23_083203_create_users_grid_style_table', 1),
(16, '2021_05_24_012915_create_users_group_table', 1),
(17, '2021_05_24_033843_create_users_list_datatable', 1),
(18, '2021_05_25_041328_create_contact_grid', 1),
(19, '2021_05_26_030535_create_contact_list', 1),
(20, '2021_05_26_051822_create_gallery', 1),
(21, '2021_05_27_020447_create_youtube_subscribers_table', 1),
(22, '2021_05_27_092906_create_vuestores_table', 1),
(23, '2022_02_13_023229_create_customer_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_traffics_metrics`
--

CREATE TABLE `social_traffics_metrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `social_network` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sessions` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bounce_rate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pages` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_duration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_traffics_metrics`
--

INSERT INTO `social_traffics_metrics` (`id`, `social_network`, `user`, `new_user`, `sessions`, `bounce_rate`, `pages`, `session_duration`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Facebook', '1,458', '452', '9,245', '35', '5.9', '00:08:16', 'today', NULL, NULL),
(2, 'Twitter', '4,785', '426', '8,156', '-26', '1.5', '00:05:16', 'today', NULL, NULL),
(3, 'Linkedin', '3,416', '951', '6,124', '56', '5.3', '00:05:16', 'today', NULL, NULL),
(4, 'Youtube', '5,426', '753', '9,147', '15', '7.5', '00:05:16', 'today', NULL, NULL),
(5, 'Instagram', '6,258', '852', '4,369', '75', '2.7', '00:05:16', 'today', NULL, NULL),
(6, 'Google+', '9,632', '123', '1,256', '46', '2.6', '00:05:16', 'today', NULL, NULL),
(7, 'Facebook', '2,458', '552', '10,245', '38', '6.9', '00:08:16', 'week', NULL, NULL),
(8, 'Twitter', '5,785', '526', '6,156', '-36', '2.5', '00:05:16', 'week', NULL, NULL),
(9, 'Linkedin', '34,416', '151', '6,124', '36', '6.3', '00:05:16', 'week', NULL, NULL),
(10, 'Youtube', '6,426', '553', '10,147', '55', '6.5', '00:05:16', 'week', NULL, NULL),
(11, 'Instagram', '60,258', '5552', '2,369', '95', '6.7', '00:05:16', 'week', NULL, NULL),
(12, 'Google+', '2,632', '223', '3,256', '26', '5.6', '00:05:16', 'week', NULL, NULL),
(13, 'Facebook', '26,458', '5052', '15,245', '58', '3.9', '00:08:16', 'month', NULL, NULL),
(14, 'Twitter', '52,785', '6026', '60,156', '-66', '6.5', '00:05:16', 'month', NULL, NULL),
(15, 'Linkedin', '34,416', '151', '6,124', '36', '6.3', '00:05:16', 'month', NULL, NULL),
(16, 'Youtube', '60,426', '553', '10,147', '55', '8.5', '00:05:16', 'month', NULL, NULL),
(17, 'Instagram', '60,258', '5552', '2,369', '95', '6.7', '00:05:16', 'month', NULL, NULL),
(18, 'Google+', '20,632', '223', '30,256', '26', '6.6', '00:05:16', 'month', NULL, NULL),
(19, 'Facebook', '1,458', '452', '9,245', '35', '5.9', '00:08:16', 'year', NULL, NULL),
(20, 'Twitter', '4,285', '424', '8,356', '-25', '1.5', '00:05:16', 'year', NULL, NULL),
(21, 'Linkedin', '3,416', '951', '6,124', '56', '5.3', '00:05:16', 'year', NULL, NULL),
(22, 'Youtube', '5,426', '753', '9,147', '15', '7.5', '00:05:16', 'year', NULL, NULL),
(23, 'Instagram', '6,258', '852', '4,369', '75', '2.7', '00:05:16', 'year', NULL, NULL),
(24, 'Google+', '9,632', '123', '1,256', '46', '2.6', '00:05:16', 'year', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE `todos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `todo_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_overviews`
--

CREATE TABLE `twitter_overviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tweets` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `impressions` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `retweets` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `engagement_rate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_followers` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `twitter_overviews`
--

INSERT INTO `twitter_overviews` (`id`, `tweets`, `impressions`, `retweets`, `engagement_rate`, `new_followers`, `status`, `created_at`, `updated_at`) VALUES
(1, '472', '2K', '78', '4.8', '178', 'week', NULL, NULL),
(2, '2472', '8K', '278', '3.8', '778', 'month', NULL, NULL),
(3, '2472', '78K', '2078', '4.8', '5778', 'year', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2a$12$bMLd4HQCQBwG1c8u.Q7GmuCkfr7ZYODtBfjBX/RTTL/zfAXcrvpDm', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_grid`
--

CREATE TABLE `users_grid` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_revenue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orders` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `products` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_grid`
--

INSERT INTO `users_grid` (`id`, `name`, `position`, `image`, `total_revenue`, `orders`, `products`, `created_at`, `updated_at`) VALUES
(1, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', '72,572', '3,257', '74', NULL, NULL),
(2, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', '70,572', '2,257', '94', NULL, NULL),
(3, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', '70,572', '4,257', '44', NULL, NULL),
(4, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', '77,572', '6,257', '55', NULL, NULL),
(5, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', '88,572', '8,257', '91', NULL, NULL),
(6, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', '79,572', '30,257', '64', NULL, NULL),
(7, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', '72,572', '3,257', '74', NULL, NULL),
(8, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', '79,572', '6,257', '34', NULL, NULL),
(9, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', '73,572', '1,257', '76', NULL, NULL),
(10, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', '74,572', '1,257', '72', NULL, NULL),
(11, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', '44,572', '2,257', '25', NULL, NULL),
(12, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', '22,572', '12,257', '99', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_grid_style`
--

CREATE TABLE `users_grid_style` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_photo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_grid_style`
--

INSERT INTO `users_grid_style` (`id`, `name`, `position`, `cover_photo`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(2, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(3, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(4, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(5, 'Duran Clayton', 'UI/UX Designer', 'img/Cover5.png', 'img/author/profile.png', NULL, NULL),
(6, 'Duran Clayton', 'UI/UX Designer', 'img/Cover6.png', 'img/author/profile.png', NULL, NULL),
(7, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(8, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(9, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(10, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(11, 'Duran Clayton', 'UI/UX Designer', 'img/Cover5.png', 'img/author/profile.png', NULL, NULL),
(12, 'Duran Clayton', 'UI/UX Designer', 'img/Cover6.png', 'img/author/profile.png', NULL, NULL),
(13, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(14, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(15, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(16, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(17, 'Duran Clayton', 'UI/UX Designer', 'img/Cover5.png', 'img/author/profile.png', NULL, NULL),
(18, 'Duran Clayton', 'UI/UX Designer', 'img/Cover6.png', 'img/author/profile.png', NULL, NULL),
(19, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL),
(20, 'Duran Clayton', 'UI/UX Designer', 'img/Cover.png', 'img/author/profile.png', NULL, NULL),
(21, 'Duran Clayton', 'UI/UX Designer', 'img/Cover2.png', 'img/author/profile.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_group`
--

CREATE TABLE `users_group` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_project` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complete_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_group`
--

INSERT INTO `users_group` (`id`, `title`, `location`, `body`, `image`, `current_project`, `complete_status`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(2, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(3, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(4, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(5, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(6, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL),
(7, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(8, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(9, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(10, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(11, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(12, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL),
(13, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(14, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(15, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(16, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(17, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(18, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL),
(19, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(20, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(21, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(22, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(23, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(24, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL),
(25, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(26, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(27, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(28, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(29, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(30, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL),
(31, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(32, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(33, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(34, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(35, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(36, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL),
(37, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl1.png', 'Plugin Development', '45', NULL, NULL),
(38, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl2.png', 'Plugin Development', '45', NULL, NULL),
(39, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl3.png', 'Plugin Development', '45', NULL, NULL),
(40, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl4.png', 'Plugin Development', '45', NULL, NULL),
(41, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl5.png', 'Plugin Development', '45', NULL, NULL),
(42, 'Dashboard UI', 'San Francisco, CA', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod dolor ame.', 'img/ugl6.png', 'Plugin Development', '45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_list`
--

CREATE TABLE `users_list` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `aboutus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_hour` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `earned` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_list`
--

INSERT INTO `users_list` (`id`, `name`, `position`, `image`, `aboutus`, `per_hour`, `earned`, `created_at`, `updated_at`) VALUES
(1, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(2, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(3, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(4, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(5, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(6, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(7, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(8, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(9, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(10, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(11, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(12, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(13, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(14, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(15, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(16, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(17, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(18, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(19, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(20, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(21, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(22, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(23, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(24, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(25, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(26, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(27, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(28, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(29, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(30, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(31, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(32, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(33, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(34, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(35, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(36, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(37, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL),
(38, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', 'Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod.', '25', '88', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_list_datatable`
--

CREATE TABLE `users_list_datatable` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_list_datatable`
--

INSERT INTO `users_list_datatable` (`id`, `user`, `location`, `image`, `email`, `company`, `position`, `join_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(2, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(3, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(4, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(5, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(6, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(7, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(8, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(9, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(10, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(11, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(12, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(13, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(14, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(15, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(16, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(17, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Deactive', NULL, NULL),
(18, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(19, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(20, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(21, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(22, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(23, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(24, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(25, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(26, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(27, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(28, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(29, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(30, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(31, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(32, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(33, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(34, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(35, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(36, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(37, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(38, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(39, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(40, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(41, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(42, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(43, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(44, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(45, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(46, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(47, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(48, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(49, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(50, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(51, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(52, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(53, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(54, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(55, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(56, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(57, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(58, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(59, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(60, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(61, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(62, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(63, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(64, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(65, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(66, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(67, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(68, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL),
(69, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(70, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(71, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Active', NULL, NULL),
(72, 'Kellie Marquot', 'San Francisco, CA', 'img/tm6.png', 'John-Keller@Gmail.Com', 'Business Development', 'Web Developer', 'January 20, 2020', 'Blocked', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_teams`
--

CREATE TABLE `user_teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_teams`
--

INSERT INTO `user_teams` (`id`, `name`, `position`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', NULL, NULL),
(2, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', NULL, NULL),
(3, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', NULL, NULL),
(4, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', NULL, NULL),
(5, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', NULL, NULL),
(6, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', NULL, NULL),
(7, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', NULL, NULL),
(8, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', NULL, NULL),
(9, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', NULL, NULL),
(10, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', NULL, NULL),
(11, 'Garry Sobars', 'Founder & CEO', 'img/tm1.png', NULL, NULL),
(12, 'Barbara Marion', 'Tech Executive', 'img/tm2.png', NULL, NULL),
(13, 'Christine Arnold', 'Lead Designer', 'img/tm3.png', NULL, NULL),
(14, 'Natalie Corwin', 'Product Designer', 'img/tm4.png', NULL, NULL),
(15, 'Carolyn Park', 'Lead Designer', 'img/tm5.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vuestores`
--

CREATE TABLE `vuestores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_subscribers`
--

CREATE TABLE `youtube_subscribers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribers` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dataG` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`dataG`)),
  `dataL` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`dataL`)),
  `labels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`labels`)),
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `youtube_subscribers`
--

INSERT INTO `youtube_subscribers` (`id`, `subscribers`, `dataG`, `dataL`, `labels`, `status`, `created_at`, `updated_at`) VALUES
(1, '25,472', '[10,50,15,70,14,24,20]', '[70,35,10,25,10,14,15]', '[\"Sun\",\"Mon\",\"Tue\",\"Wed\",\"Thu\",\"Fri\",\"Sat\"]', 'week', NULL, NULL),
(2, '75,582', '[20,30,15,60,70,24]', '[70,60,40,20,15,65]', '[\"1-5\",\"6-10\",\"11-15\",\"15-20\",\"21-25\",\"25-30\"]', 'month', NULL, NULL),
(3, '124,892', '[20,60,50,45,50,60,70,40,45,35,25,30]', '[70,60,40,20,15,65]', '[10,40,30,40,60,55,45,35,30,20,15,20]', 'year', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_grid`
--
ALTER TABLE `contact_grid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_list`
--
ALTER TABLE `contact_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facebook_overviews`
--
ALTER TABLE `facebook_overviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `firestores`
--
ALTER TABLE `firestores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `firestores_email_unique` (`email`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `social_traffics_metrics`
--
ALTER TABLE `social_traffics_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `twitter_overviews`
--
ALTER TABLE `twitter_overviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `users_grid`
--
ALTER TABLE `users_grid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_grid_style`
--
ALTER TABLE `users_grid_style`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_group`
--
ALTER TABLE `users_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_list`
--
ALTER TABLE `users_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_list_datatable`
--
ALTER TABLE `users_list_datatable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_teams`
--
ALTER TABLE `user_teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vuestores`
--
ALTER TABLE `vuestores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vuestores_email_unique` (`email`);

--
-- Indexes for table `youtube_subscribers`
--
ALTER TABLE `youtube_subscribers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_grid`
--
ALTER TABLE `contact_grid`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_list`
--
ALTER TABLE `contact_list`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `facebook_overviews`
--
ALTER TABLE `facebook_overviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `firestores`
--
ALTER TABLE `firestores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_traffics_metrics`
--
ALTER TABLE `social_traffics_metrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `todos`
--
ALTER TABLE `todos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `twitter_overviews`
--
ALTER TABLE `twitter_overviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_grid`
--
ALTER TABLE `users_grid`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users_grid_style`
--
ALTER TABLE `users_grid_style`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users_group`
--
ALTER TABLE `users_group`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users_list`
--
ALTER TABLE `users_list`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users_list_datatable`
--
ALTER TABLE `users_list_datatable`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `user_teams`
--
ALTER TABLE `user_teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `vuestores`
--
ALTER TABLE `vuestores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `youtube_subscribers`
--
ALTER TABLE `youtube_subscribers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
