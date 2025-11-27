-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 03, 2025 at 08:59 AM
-- Server version: 10.11.14-MariaDB-cll-lve
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bantwana_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `published` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_settings`
--

CREATE TABLE `contact_settings` (
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_settings`
--

INSERT INTO `contact_settings` (`setting_key`, `setting_value`) VALUES
('contact_hero_image', 'contact_hero_68b7f674c16a4.png'),
('contact_hero_subtitle', 'We\'d love to hear from you. Reach out with any questions or comments.'),
('contact_hero_title', 'Get In Touch');

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `name`, `slug`, `description`) VALUES
(5, 'Horticulture Learning exchange Visits', 'horticulture-learning-exchange-visits', 'Young women in horticulture enhanced their skills and income-generating activities through experiential learning. They participated in knowledge-sharing visits to the Malkerns Research Centre, renowned for innovative horticultural research, and GUBA Permaculture Centre, which focuses on sustainable agriculture and community empowerment through permaculture training in Eswatini.'),
(6, 'Litsemba Rising Project Launch', 'litsemba-rising-project-launch', 'Exciting moments during the launch of the Litsemba Rising Project at the University of Eswatini. This initiative signified a beginning of comprehensive and impactful journey dedicated to creating safer campuses free from sexual harassment. Litsemba Rising embodies hope and commitment toward empowering students to foster respectful and harassment-free learning environments.'),
(7, 'Staff Team Building Sessions', 'staff-team-building-sessions', 'As an organization, we prioritize meaningful team-building experiences to enhance communication, collaboration, trust, morale, and social connection. Years of project implementation have taught us that mental health is crucial for both staff well-being and performance. This commitment is evident in the team-building sessions showcased in our photos.'),
(8, 'Accelerating HIV Prevention Among Adolescent Boys and Young Men in Eswatini', 'accelerating-hiv-prevention-among-adolescent-boys-and-young-men-in-eswatini', 'Our targeted efforts in Eswatini promoting male circumcision among adolescent boys and young men have played a major role in reducing new HIV infections by over 70%, currently at around 4,000 cases annually. By integrating circumcision services with school-based extracurricular activities such as sports, we have boosted health outcomes and school engagement. These results significantly contribute to the national goal of ending the HIV epidemic by 2030, demonstrating our strong commitment to youth empowerment and epidemic control.'),
(9, 'Transforming Lives Through Horticulture and Broiler Production Training', 'transforming-lives-through-horticulture-and-broiler-production-training', 'Our organization remains deeply committed to empowering rural women, with a focused emphasis on young women, by equipping them with essential business management skills. This initiative aims to boost their productivity, drive economic growth, and broaden market opportunities. Through intensive and hands-on training sessions, we have significantly contributed to poverty reduction while enhancing family and community well-being by promoting women’s financial independence.\r\nThe success of these efforts is reflected in various economic strengthening interventions, including the recent trainings where approximately 300 young women engaged in horticulture and broiler production received comprehensive training delivered in partnership with the Ministry of Agriculture, specifically the Department of Veterinary Services and Agricultural Research. Through this collaboration we demonstrated our strength in fostering impactful partnerships to amplify the reach and effectiveness of women’s empowerment programs. As an Organization we continue to seek strong and like-minded collaborators to join us in expanding these opportunities, creating sustainable livelihoods, and transforming communities through women-led economic development.');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `gallery_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `gallery_id`, `filename`, `alt_text`, `caption`, `sort_order`, `uploaded_at`) VALUES
(19, 5, 'img_68d2851e1baea.jpg', '', '', 0, '2025-09-23 11:31:47'),
(20, 5, 'img_68d28523662a5.jpg', '', '', 0, '2025-09-23 11:31:52'),
(21, 5, 'img_68d2852883e9b.jpg', '', '', 0, '2025-09-23 11:31:57'),
(22, 5, 'img_68d2852da0518.jpg', '', '', 0, '2025-09-23 11:32:02'),
(23, 5, 'img_68d2880622a69.jpg', '', '', 0, '2025-09-23 11:44:08'),
(24, 5, 'img_68d2880863bc8.jpg', '', '', 0, '2025-09-23 11:44:09'),
(25, 5, 'img_68d2880979300.jpg', '', '', 0, '2025-09-23 11:44:12'),
(26, 5, 'img_68d2880c1d739.jpg', '', '', 0, '2025-09-23 11:44:14'),
(27, 5, 'img_68d2880e38bfa.jpg', '', '', 0, '2025-09-23 11:44:16'),
(28, 6, 'img_68d6671862247.jpg', '', '', 0, '2025-09-26 10:12:41'),
(29, 6, 'img_68d6671937014.jpg', '', '', 0, '2025-09-26 10:12:42'),
(30, 6, 'img_68d6671a1d8a6.jpg', '', '', 0, '2025-09-26 10:12:42'),
(31, 6, 'img_68d6671ad940e.jpg', '', '', 0, '2025-09-26 10:12:43'),
(32, 6, 'img_68d6671bdee54.jpg', '', '', 0, '2025-09-26 10:12:44'),
(33, 6, 'img_68d6671cd5203.jpg', '', '', 0, '2025-09-26 10:12:45'),
(34, 6, 'img_68d6671dbbf2a.jpg', '', '', 0, '2025-09-26 10:12:46'),
(35, 6, 'img_68d6671e83126.jpg', '', '', 0, '2025-09-26 10:12:47'),
(36, 6, 'img_68d66cc05d4a6.jpg', '', '', 0, '2025-09-26 10:36:49'),
(37, 6, 'img_68d66cc136f1d.jpg', '', '', 0, '2025-09-26 10:36:50'),
(38, 6, 'img_68d66cc20e1d7.jpg', '', '', 0, '2025-09-26 10:36:50'),
(39, 6, 'img_68d66cc2d900d.jpg', '', '', 0, '2025-09-26 10:36:51'),
(40, 6, 'img_68d66cc3dee2b.jpg', '', '', 0, '2025-09-26 10:36:52'),
(41, 6, 'img_68d66cc4d5c42.jpg', '', '', 0, '2025-09-26 10:36:53'),
(42, 6, 'img_68d66cc5ac278.jpg', '', '', 0, '2025-09-26 10:36:54'),
(43, 6, 'img_68d66cc6831b9.jpg', '', '', 0, '2025-09-26 10:36:55'),
(44, 7, 'img_68d67f1919a1b.jpg', '', '', 0, '2025-09-26 11:55:05'),
(45, 7, 'img_68d67f1919d8e.jpg', '', '', 0, '2025-09-26 11:55:05'),
(46, 7, 'img_68d67f194a6c5.jpg', '', '', 0, '2025-09-26 11:55:05'),
(47, 7, 'img_68d67f1967f51.jpg', '', '', 0, '2025-09-26 11:55:05'),
(48, 7, 'img_68d67f1990b6e.jpg', '', '', 0, '2025-09-26 11:55:05'),
(49, 7, 'img_68d67f19ac0c6.jpg', '', '', 0, '2025-09-26 11:55:05'),
(50, 7, 'img_68d67f39a4b0c.jpg', '', '', 0, '2025-09-26 11:55:37'),
(51, 7, 'img_68d67f39a4dd0.jpg', '', '', 0, '2025-09-26 11:55:37'),
(52, 7, 'img_68d67f39d5106.jpg', '', '', 0, '2025-09-26 11:55:37'),
(53, 7, 'img_68d67f39f2635.jpg', '', '', 0, '2025-09-26 11:55:38'),
(54, 7, 'img_68d67f3a27414.jpg', '', '', 0, '2025-09-26 11:55:38'),
(55, 7, 'img_68d67f3a44897.jpg', '', '', 0, '2025-09-26 11:55:38'),
(56, 7, 'img_68d680d868dfe.jpg', '', '', 0, '2025-09-26 12:02:32'),
(57, 7, 'img_68d680d869123.jpg', '', '', 0, '2025-09-26 12:02:32'),
(58, 7, 'img_68d680d8987d9.jpg', '', '', 0, '2025-09-26 12:02:32'),
(59, 7, 'img_68d680d8b7c3e.jpg', '', '', 0, '2025-09-26 12:02:32'),
(60, 7, 'img_68d680d8df130.jpg', '', '', 0, '2025-09-26 12:02:33'),
(61, 7, 'img_68d680d907f6e.jpg', '', '', 0, '2025-09-26 12:02:33'),
(62, 8, 'img_68d691b90b90e.jpg', '', '', 0, '2025-09-26 13:14:33'),
(63, 8, 'img_68d691b921ace.jpg', '', '', 0, '2025-09-26 13:14:33'),
(64, 8, 'img_68d691b97d9bc.jpg', '', '', 0, '2025-09-26 13:14:33'),
(65, 8, 'img_68d691b9929d2.jpg', '', '', 0, '2025-09-26 13:14:33'),
(66, 8, 'img_68d691b9ae00f.jpg', '', '', 0, '2025-09-26 13:14:33'),
(67, 9, 'img_68d69b0342a6b.jpg', '', '', 0, '2025-09-26 13:54:14'),
(68, 9, 'img_68d69b06d7ad4.jpg', '', '', 0, '2025-09-26 13:54:18'),
(69, 9, 'img_68d69b0a3cda9.jpg', '', '', 0, '2025-09-26 13:54:18'),
(70, 9, 'img_68d69b0a6d8b6.jpg', '', '', 0, '2025-09-26 13:54:18'),
(71, 9, 'img_68d69b0a9e57e.jpg', '', '', 0, '2025-09-26 13:54:18'),
(72, 9, 'img_68d69b0ad318e.jpg', '', '', 0, '2025-09-26 13:54:19'),
(73, 9, 'img_68d69b0b21644.jpg', '', '', 0, '2025-09-26 13:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `hero_sections`
--

CREATE TABLE `hero_sections` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `subheading` text DEFAULT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `internships`
--

CREATE TABLE `internships` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `deadline` date DEFAULT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `apply_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `timestamp`, `message`) VALUES
(1, '2025-08-08 10:24:09', 'Failed login attempt for identifier: \'admin\' from IP: ::1 at 2025-08-08 12:24:09'),
(2, '2025-08-08 10:31:15', 'Failed login attempt for identifier: \'admin\' from IP: ::1 at 2025-08-08 12:31:15'),
(3, '2025-08-08 10:33:50', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-08 12:33:50'),
(4, '2025-08-11 10:05:01', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-11 12:05:01'),
(5, '2025-08-11 14:11:28', 'Admin admin updated homepage hero/counter content at 2025-08-11 16:11:28'),
(6, '2025-08-11 14:58:15', 'Admin admin updated homepage hero/counter content at 2025-08-11 16:58:15 (New hero image: hero_689a0507a6bb0.jpg)'),
(7, '2025-08-14 07:23:16', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-14 09:23:16'),
(8, '2025-08-14 07:55:51', 'Admin admin updated About page content at 2025-08-14 09:55:51 (New About hero image: about_hero_689d96877325e.jpg)'),
(9, '2025-08-14 08:17:13', 'Admin admin updated About page content at 2025-08-14 10:17:13 (New About hero image: about_hero_689d9b893b261.jpg)'),
(10, '2025-08-14 08:19:06', 'Admin admin updated About page content at 2025-08-14 10:19:06'),
(11, '2025-08-14 08:29:28', 'Admin admin updated About page content at 2025-08-14 10:29:28'),
(12, '2025-08-14 08:29:48', 'Admin admin updated About page content at 2025-08-14 10:29:48'),
(13, '2025-08-14 08:38:29', 'Admin admin updated About page content at 2025-08-14 10:38:29'),
(14, '2025-08-14 08:38:57', 'Admin admin updated About page content at 2025-08-14 10:38:57'),
(15, '2025-08-14 08:54:32', 'Admin admin updated About page content at 2025-08-14 10:54:32'),
(16, '2025-08-14 08:55:59', 'Admin admin updated About page content at 2025-08-14 10:55:59'),
(17, '2025-08-14 08:56:14', 'Admin admin updated About page content at 2025-08-14 10:56:14'),
(18, '2025-08-14 09:16:01', 'Admin admin updated About page content at 2025-08-14 11:16:01 (New history image: about_history_689da951b67f3.jpg)'),
(19, '2025-08-14 09:17:13', 'Admin admin updated About page content at 2025-08-14 11:17:13 (New history image: about_history_689da9997b668.jpg)'),
(20, '2025-08-14 10:16:59', 'Admin admin created gallery: Test (ID: 1)'),
(21, '2025-08-14 10:17:11', 'Admin admin updated gallery ID: 1'),
(22, '2025-08-14 10:17:52', 'Admin admin uploaded 4 images to gallery: Test (ID: 1)'),
(23, '2025-08-14 10:18:31', 'Admin admin created gallery: Test 2 (ID: 2)'),
(24, '2025-08-14 10:18:38', 'Admin admin updated gallery ID: 2'),
(25, '2025-08-14 10:18:50', 'Admin admin uploaded 5 images to gallery: Test 2 (ID: 2)'),
(26, '2025-08-14 12:55:22', 'Admin admin updated Programs hero section at 2025-08-14 14:55:22 (New image: programs_hero_689ddcbaa6a64.jpg)'),
(27, '2025-08-14 12:55:51', 'Admin admin updated Programs hero section at 2025-08-14 14:55:51 (New image: programs_hero_689ddcd7b23c3.jpg)'),
(28, '2025-08-14 12:57:49', 'Admin admin created program: Insika Yakusasa (ID: 1)'),
(29, '2025-08-14 13:03:14', 'Admin admin deleted program: Insika Yakusasa (ID: 1)'),
(30, '2025-08-14 13:03:29', 'Admin admin created program: Voluntary Medical Male Circumcision Service Delivery and Support (VMMC) (ID: 12)'),
(31, '2025-08-21 15:59:51', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-21 17:59:51'),
(32, '2025-08-21 17:51:59', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-21 19:51:59'),
(33, '2025-08-22 08:46:46', 'Failed login attempt for identifier: \'admin\' from IP: ::1 at 2025-08-22 10:46:46'),
(34, '2025-08-22 08:46:48', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-22 10:46:48'),
(35, '2025-08-22 09:06:30', 'Admin admin updated Programs hero section at 2025-08-22 11:06:30 (New image: programs_hero_68a83316b2e11.jpg)'),
(36, '2025-08-22 12:56:20', 'Admin admin updated Programs hero section at 2025-08-22 14:56:20 (New image: programs_hero_68a868f422bf7.jpg)'),
(37, '2025-08-22 12:56:54', 'Admin admin updated program ID: 1'),
(38, '2025-08-22 12:57:08', 'Admin admin updated program ID: 2'),
(39, '2025-08-22 12:57:21', 'Admin admin updated program ID: 3'),
(40, '2025-08-22 13:03:30', 'Admin admin created program: test (ID: 4)'),
(41, '2025-08-22 13:57:10', 'Admin admin created program: test 2 (ID: 5)'),
(42, '2025-08-22 13:58:25', 'Admin admin updated program ID: 5'),
(43, '2025-08-23 16:42:26', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-23 18:42:26'),
(44, '2025-08-23 16:42:56', 'Admin admin updated homepage hero/counter content at 2025-08-23 18:42:56'),
(45, '2025-08-26 10:25:33', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-26 12:25:33'),
(46, '2025-08-26 11:47:14', 'Admin admin uploaded new Team hero image: team_hero_68ad9ec2d2986.jpg at 2025-08-26 13:47:14'),
(47, '2025-08-26 11:47:14', 'Admin admin updated Team page hero text content at 2025-08-26 13:47:14'),
(48, '2025-08-26 12:06:20', 'Admin admin uploaded new Team hero image: team_hero_68ada33c369af.jpg at 2025-08-26 14:06:20'),
(49, '2025-08-26 12:06:20', 'Admin admin updated Team page hero text content at 2025-08-26 14:06:20'),
(50, '2025-08-26 12:06:20', 'Admin admin updated Team page text content at 2025-08-26 14:06:20'),
(51, '2025-08-26 12:06:47', 'Admin admin uploaded new Team hero image: team_hero_68ada3576cb72.jpg at 2025-08-26 14:06:47'),
(52, '2025-08-26 12:06:47', 'Admin admin updated Team page hero text content at 2025-08-26 14:06:47'),
(53, '2025-08-26 12:07:28', 'Admin admin uploaded new Team hero image: team_hero_68ada380ae708.jpg at 2025-08-26 14:07:28'),
(54, '2025-08-26 12:07:28', 'Admin admin updated Team page hero text content at 2025-08-26 14:07:28'),
(55, '2025-08-26 13:16:42', 'Admin admin uploaded new Thulani image: thulani_68adb3baab482.jpg at 2025-08-26 15:16:42'),
(56, '2025-08-26 13:16:42', 'Admin admin updated Team page text content at 2025-08-26 15:16:42'),
(57, '2025-08-28 09:04:16', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-28 11:04:16'),
(58, '2025-08-28 09:17:41', 'Admin admin uploaded new Thulani image: thulani_68b01eb53f179.webp at 2025-08-28 11:17:41'),
(59, '2025-08-28 09:17:41', 'Admin admin updated Team page text content at 2025-08-28 11:17:41'),
(60, '2025-08-28 09:22:16', 'Admin admin updated About page content at 2025-08-28 11:22:16 (New hero image: about_hero_68b01fc806bed.jpg)'),
(61, '2025-08-28 09:24:09', 'Admin admin deleted program: test 2 (ID: 5)'),
(62, '2025-08-28 09:31:07', 'Admin admin updated program ID: 4'),
(63, '2025-08-28 09:31:56', 'Admin admin updated program ID: 1'),
(64, '2025-08-28 09:32:31', 'Admin admin updated program ID: 2'),
(65, '2025-08-28 09:32:54', 'Admin admin updated program ID: 3'),
(66, '2025-08-29 06:18:12', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-29 08:18:12'),
(67, '2025-08-29 07:27:14', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-29 09:27:14'),
(68, '2025-08-29 07:54:09', 'Admin admin uploaded new Donate hero image: donate_hero_68b15ca1a67f5.jpg'),
(69, '2025-08-29 07:54:09', 'Admin admin updated Donate page text content. New hero image: donate_hero_68b15ca1a67f5.jpg'),
(70, '2025-08-29 07:54:37', 'Admin admin updated Donate page text content.'),
(71, '2025-08-29 09:36:44', 'Admin admin uploaded new Blog hero image: blog_hero_68b174accaae5.jpg at 2025-08-29 11:36:44'),
(72, '2025-08-29 09:36:44', 'Admin admin updated Blog page hero text content at 2025-08-29 11:36:44'),
(73, '2025-08-29 09:38:33', 'Admin admin deleted image: img_689db80adb286.jpg from gallery: Test 2'),
(74, '2025-08-29 13:58:00', 'Admin admin uploaded publication file: publication_68b1b1e806093.doc (MIME: application/msword, Size: 29184 bytes) at 2025-08-29 15:58:00'),
(75, '2025-08-29 13:58:00', 'Admin admin created publication record: test 1 (ID: 1) at 2025-08-29 15:58:00'),
(76, '2025-08-29 13:58:45', 'Admin admin uploaded publication file: publication_68b1b2152ee27.zip (MIME: application/zip, Size: 283877 bytes) at 2025-08-29 15:58:45'),
(77, '2025-08-29 13:58:45', 'Admin admin created publication record: test 2 (ID: 2) at 2025-08-29 15:58:45'),
(78, '2025-08-31 16:59:36', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-08-31 18:59:36'),
(79, '2025-09-01 11:18:45', 'Admin admin uploaded publication file: publication_68b58115794dc.docx (MIME: application/vnd.openxmlformats-officedocument.wordprocessingml.document, Size: 155023 bytes) at 2025-09-01 13:18:45'),
(80, '2025-09-01 11:18:45', 'Admin admin created publication record: Nice test (ID: 3) at 2025-09-01 13:18:45'),
(81, '2025-09-01 11:20:24', 'Admin admin uploaded publication file: publication_68b5817870c9b.pdf (MIME: application/pdf, Size: 579173 bytes) at 2025-09-01 13:20:24'),
(82, '2025-09-01 11:20:24', 'Admin admin created publication record: 2025 Financial year (ID: 4) at 2025-09-01 13:20:24'),
(83, '2025-09-01 11:31:48', 'Admin admin uploaded new Publications hero image: publications_hero_68b5842494680.jpg at 2025-09-01 13:31:48'),
(84, '2025-09-01 11:31:48', 'Admin admin updated Publications page hero text content at 2025-09-01 13:31:48'),
(85, '2025-09-01 11:32:05', 'Admin admin uploaded new Publications hero image: publications_hero_68b58435e1dfe.jpg at 2025-09-01 13:32:05'),
(86, '2025-09-01 11:32:05', 'Admin admin updated Publications page hero text content at 2025-09-01 13:32:05'),
(87, '2025-09-01 11:56:25', 'Admin admin uploaded new Publications hero image: publications_hero_68b589e9c5fee.jpg at 2025-09-01 13:56:25'),
(88, '2025-09-01 11:56:25', 'Admin admin updated Publications page hero text content at 2025-09-01 13:56:25'),
(89, '2025-09-01 11:56:46', 'Admin admin uploaded new Publications hero image: publications_hero_68b589fe45b72.jpg at 2025-09-01 13:56:46'),
(90, '2025-09-01 11:56:46', 'Admin admin updated Publications page hero text content at 2025-09-01 13:56:46'),
(91, '2025-09-01 13:46:21', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68b5a3ad0eab9.jpg at 2025-09-01 15:46:21'),
(92, '2025-09-01 13:46:21', 'Admin admin updated Volunteer page hero text content at 2025-09-01 15:46:21'),
(93, '2025-09-01 14:44:20', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68b5b14406ced.jpg at 2025-09-01 16:44:20'),
(94, '2025-09-01 14:44:20', 'Admin admin updated Volunteer page hero text content at 2025-09-01 16:44:20'),
(95, '2025-09-01 14:44:50', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68b5b1622fcee.jpg at 2025-09-01 16:44:50'),
(96, '2025-09-01 14:44:50', 'Admin admin updated Volunteer page hero text content at 2025-09-01 16:44:50'),
(97, '2025-09-02 07:41:34', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 09:41:34'),
(98, '2025-09-02 07:41:38', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 09:41:38'),
(99, '2025-09-02 07:41:46', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 09:41:46'),
(100, '2025-09-02 07:43:34', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 09:43:34'),
(101, '2025-09-02 08:05:10', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 10:05:10'),
(102, '2025-09-02 08:05:18', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 10:05:18'),
(103, '2025-09-02 08:14:05', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 10:14:05'),
(104, '2025-09-02 08:14:14', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 10:14:14'),
(105, '2025-09-02 08:14:33', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 10:14:33'),
(106, '2025-09-02 08:19:53', 'Admin admin failed Internships hero content validation: Hero Title is required. at 2025-09-02 10:19:53'),
(107, '2025-09-02 08:26:06', 'Admin admin uploaded new Internships hero image: internships_hero_68b6aa1e29bec.jpg at 2025-09-02 10:26:06'),
(108, '2025-09-02 08:26:06', 'Admin admin updated Internships page hero content: {\"internships_hero_image\":\"internships_hero_68b6aa1e29bec.jpg\",\"internships_hero_title\":\"Gain Experience, Make an Impact\",\"internships_hero_subtitle\":\"Apply your academic knowledge in a real-world development setting\"} at 2025-09-02 10:26:06'),
(109, '2025-09-02 09:02:26', 'Admin admin uploaded new Careers hero image: careers_hero_68b6b2a2158d1.png at 2025-09-02 11:02:26'),
(110, '2025-09-02 09:02:26', 'Admin admin updated Careers page hero text content at 2025-09-02 11:02:26'),
(111, '2025-09-02 09:02:45', 'Admin admin failed to create new Job at 2025-09-02 11:02:45'),
(112, '2025-09-02 09:10:59', 'Admin admin created new Job ID: 1 at 2025-09-02 11:10:59'),
(113, '2025-09-02 09:11:08', 'Admin admin created new Job ID: 2 at 2025-09-02 11:11:08'),
(114, '2025-09-02 09:22:05', 'Admin admin created new Job ID: 3 at 2025-09-02 11:22:05'),
(115, '2025-09-02 09:22:33', 'Admin admin uploaded new Careers hero image: careers_hero_68b6b759c049e.png at 2025-09-02 11:22:33'),
(116, '2025-09-02 09:22:33', 'Admin admin updated Careers page hero text content at 2025-09-02 11:22:33'),
(117, '2025-09-02 12:25:03', 'Admin admin updated Careers page hero content: {\"careers_hero_title\":\"Build Your Career With Us\",\"careers_hero_subtitle\":\"Join a team passionate about creating lasting change for children and families\"} at 2025-09-02 14:25:03'),
(118, '2025-09-02 12:25:15', 'Admin admin uploaded new Careers hero image: careers_hero_68b6e22b8e615.png at 2025-09-02 14:25:15'),
(119, '2025-09-02 12:25:15', 'Admin admin updated Careers page hero content: {\"careers_hero_image\":\"careers_hero_68b6e22b8e615.png\",\"careers_hero_title\":\"Build Your Career With Us\",\"careers_hero_subtitle\":\"Join a team passionate about creating lasting change for children and families\"} at 2025-09-02 14:25:15'),
(120, '2025-09-02 12:26:06', 'Admin admin failed to create job: na at 2025-09-02 14:26:06'),
(121, '2025-09-02 13:18:45', 'Admin admin failed Partner hero content validation: Hero Title is required. at 2025-09-02 15:18:45'),
(122, '2025-09-02 13:18:50', 'Admin admin failed Partner hero content validation: Hero Title is required. at 2025-09-02 15:18:50'),
(123, '2025-09-02 13:19:22', 'Admin admin failed to create job: na at 2025-09-02 15:19:22'),
(124, '2025-09-02 13:23:13', 'Admin admin created job: na (ID: 1)'),
(125, '2025-09-02 13:54:35', 'Admin admin updated Partner hero settings: {\"partner_hero_title\":\"Collaborate For Greater Impact\",\"partner_hero_subtitle\":\"Bantwana\",\"partner_hero_image\":\"partner_hero_68b6f71bb6ed0.png\"}'),
(126, '2025-09-02 14:39:03', 'Admin admin updated hero settings: {\"partner_hero_title\":\"Collaborate For Greater Impact\",\"partner_hero_subtitle\":\"Join forces to create sustainable change for vulnerable children and families.\",\"partner_hero_image\":\"partner_hero_68b7018700969.png\"}'),
(127, '2025-09-03 06:16:07', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-09-03 08:16:07'),
(128, '2025-09-03 07:33:02', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-09-03 09:33:02'),
(129, '2025-09-03 07:43:38', 'Admin admin updated contact hero settings: {\"contact_hero_title\":\"Get In Touch\",\"contact_hero_subtitle\":\"We\'d love to hear from you. Reach out with any questions or comments.\",\"contact_hero_image\":\"contact_hero_68b7f1aabaa12.jpg\"}'),
(130, '2025-09-03 08:04:04', 'Admin admin updated contact hero settings: {\"contact_hero_title\":\"Get In Touch\",\"contact_hero_subtitle\":\"We\'d love to hear from you. Reach out with any questions or comments.\",\"contact_hero_image\":\"contact_hero_68b7f674c16a4.png\"}'),
(131, '2025-09-03 08:09:21', 'Admin admin updated contact hero settings: {\"contact_hero_title\":\"Get In Touch\",\"contact_hero_subtitle\":\"We\'d love to hear from you. Reach out with any questions or comments.\",\"contact_hero_image\":\"contact_hero_68b7f674c16a4.png\"}'),
(132, '2025-09-03 09:36:45', 'Admin admin deleted publication file: publication_68b1b1e806093.doc (ID: 1) at 2025-09-03 11:36:45'),
(133, '2025-09-03 09:36:45', 'Admin admin deleted publication record: test 1 (ID: 1) at 2025-09-03 11:36:45'),
(134, '2025-09-03 09:36:47', 'Admin admin deleted publication file: publication_68b58115794dc.docx (ID: 3) at 2025-09-03 11:36:47'),
(135, '2025-09-03 09:36:47', 'Admin admin deleted publication record: Nice test (ID: 3) at 2025-09-03 11:36:47'),
(136, '2025-09-03 09:36:50', 'Admin admin deleted publication file: publication_68b5817870c9b.pdf (ID: 4) at 2025-09-03 11:36:50'),
(137, '2025-09-03 09:36:50', 'Admin admin deleted publication record: 2025 Financial year (ID: 4) at 2025-09-03 11:36:50'),
(138, '2025-09-03 09:36:53', 'Admin admin deleted publication file: publication_68b1b2152ee27.zip (ID: 2) at 2025-09-03 11:36:53'),
(139, '2025-09-03 09:36:53', 'Admin admin deleted publication record: test 2 (ID: 2) at 2025-09-03 11:36:53'),
(140, '2025-09-04 13:35:36', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-09-04 15:35:36'),
(141, '2025-09-04 13:56:42', 'Admin admin created program: Young Woman Economic empowerment project (YWEE) (ID: 6)'),
(142, '2025-09-04 13:57:17', 'Admin admin created program: SABELO SENSHA PROJECT (ID: 7)'),
(143, '2025-09-04 13:58:15', 'Admin admin created program: HIV Prevention Life Skills Education in Secondary Schools (LSE) (ID: 8)'),
(144, '2025-09-04 13:58:49', 'Admin admin created program: GO Girls Connect (ID: 9)'),
(145, '2025-09-04 13:59:23', 'Admin admin created program: National Case Management System – Eswatini (ID: 10)'),
(146, '2025-09-04 14:00:32', 'Admin admin updated program ID: 6'),
(147, '2025-09-04 14:00:47', 'Admin admin updated program ID: 7'),
(148, '2025-09-04 14:00:59', 'Admin admin updated program ID: 8'),
(149, '2025-09-04 14:01:11', 'Admin admin updated program ID: 9'),
(150, '2025-09-04 14:02:16', 'Admin admin updated program ID: 10'),
(151, '2025-09-04 14:08:10', 'Admin admin updated Programs hero section at 2025-09-04 16:08:10 (New image: programs_hero_68b99d4a18843.jpg)'),
(152, '2025-09-04 14:08:41', 'Admin admin uploaded new Internships hero image: internships_hero_68b99d69b347c.jpg at 2025-09-04 16:08:41'),
(153, '2025-09-04 14:08:41', 'Admin admin updated Internships page hero content: {\"internships_hero_image\":\"internships_hero_68b99d69b347c.jpg\",\"internships_hero_title\":\"Gain Experience, Make an Impact\",\"internships_hero_subtitle\":\"Apply your academic knowledge in a real-world development setting\"} at 2025-09-04 16:08:41'),
(154, '2025-09-04 14:09:10', 'Admin admin updated homepage hero/counter content at 2025-09-04 16:09:10 (New hero image: hero_68b99d863cdb6.jpg)'),
(155, '2025-09-04 14:11:20', 'Admin admin uploaded new Team hero image: team_hero_68b99e087411f.jpg at 2025-09-04 16:11:20'),
(156, '2025-09-04 14:11:20', 'Admin admin updated Team page text content at 2025-09-04 16:11:20'),
(157, '2025-09-04 14:11:57', 'Admin admin updated hero settings: {\"partner_hero_title\":\"Collaborate For Greater Impact\",\"partner_hero_subtitle\":\"Join forces to create sustainable change for vulnerable children and families.\",\"partner_hero_image\":\"partner_hero_68b99e2d777e9.jpg\"}'),
(158, '2025-09-04 14:18:00', 'Admin admin uploaded new Donate hero image: donate_hero_68b99f9864023.jpg'),
(159, '2025-09-04 14:18:00', 'Admin admin updated Donate page text content. New hero image: donate_hero_68b99f9864023.jpg'),
(160, '2025-09-04 14:20:15', 'Admin admin updated program ID: 6'),
(161, '2025-09-04 14:21:27', 'Admin admin uploaded new Blog hero image: blog_hero_68b9a06745133.jpg at 2025-09-04 16:21:27'),
(162, '2025-09-04 14:21:27', 'Admin admin updated Blog page hero text content at 2025-09-04 16:21:27'),
(163, '2025-09-04 14:24:18', 'Admin admin uploaded new Internships hero image: internships_hero_68b9a1123dd46.jpg at 2025-09-04 16:24:18'),
(164, '2025-09-04 14:24:18', 'Admin admin updated Internships page hero content: {\"internships_hero_image\":\"internships_hero_68b9a1123dd46.jpg\",\"internships_hero_title\":\"Gain Experience, Make an Impact\",\"internships_hero_subtitle\":\"Apply your academic knowledge in a real-world development setting\"} at 2025-09-04 16:24:18'),
(165, '2025-09-04 14:43:58', 'Admin admin updated hero settings: {\"partner_hero_title\":\"Collaborate For Greater Impact\",\"partner_hero_subtitle\":\"Join forces to create sustainable change for vulnerable children and families.\",\"partner_hero_image\":\"partner_hero_68b9a5ae24687.jpg\"}'),
(166, '2025-09-05 07:54:45', 'Admin admin (ID: 1) logged in successfully from IP: ::1 at 2025-09-05 09:54:45'),
(167, '2025-09-05 07:55:28', 'Admin admin uploaded new Contact hero image: contact_hero_68ba97707b185.jpg at 2025-09-05 09:55:28'),
(168, '2025-09-05 07:55:28', 'Admin admin updated Contact page hero text content at 2025-09-05 09:55:28'),
(169, '2025-09-05 08:01:56', 'Admin admin updated program ID: 6'),
(170, '2025-09-05 08:17:38', 'Admin admin uploaded new Partner hero image: partner_hero_68ba9ca20ccc2.jpg at 2025-09-05 10:17:38'),
(171, '2025-09-05 08:17:38', 'Admin admin updated Partner page hero text content at 2025-09-05 10:17:38'),
(172, '2025-09-05 08:23:35', 'Admin admin uploaded new Partner hero image: partner_hero_68ba9e07c15dc.jpg at 2025-09-05 10:23:35'),
(173, '2025-09-05 08:23:35', 'Admin admin updated Partner page hero text content at 2025-09-05 10:23:35'),
(174, '2025-09-05 08:23:51', 'Admin admin uploaded new Partner hero image: partner_hero_68ba9e176080f.jpg at 2025-09-05 10:23:51'),
(175, '2025-09-05 08:23:51', 'Admin admin updated Partner page hero text content at 2025-09-05 10:23:51'),
(176, '2025-09-05 12:11:07', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-05 14:11:07'),
(177, '2025-09-05 12:11:49', 'Admin admin uploaded new Contact hero image: contact_hero_68bad383e8cf1.webp at 2025-09-05 14:11:49'),
(178, '2025-09-05 12:11:49', 'Admin admin updated Contact page hero text content at 2025-09-05 14:11:49'),
(179, '2025-09-05 12:12:34', 'Admin admin updated Contact page hero text content at 2025-09-05 14:12:34'),
(180, '2025-09-05 12:14:34', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-05 14:14:34'),
(181, '2025-09-12 07:52:47', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-12 09:52:47'),
(182, '2025-09-12 08:53:54', 'Admin admin (ID: 1) logged out at 2025-09-12 10:53:54'),
(183, '2025-09-12 08:53:59', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-12 10:53:59'),
(184, '2025-09-12 09:03:22', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-12 11:03:22'),
(185, '2025-09-12 10:03:02', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-12 12:03:02'),
(186, '2025-09-12 10:04:43', 'Admin admin (ID: 1) logged out at 2025-09-12 12:04:43'),
(187, '2025-09-12 10:07:14', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-12 12:07:14'),
(188, '2025-09-12 11:09:04', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-12 13:09:04'),
(189, '2025-09-18 12:33:28', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-18 14:33:28'),
(190, '2025-09-18 12:55:37', 'Admin admin (ID: 1) logged out at 2025-09-18 14:55:37'),
(191, '2025-09-18 12:56:30', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-18 14:56:30'),
(192, '2025-09-18 13:09:41', 'Admin admin updated About page content at 2025-09-18 15:09:41'),
(193, '2025-09-18 13:10:20', 'Admin admin updated About page content at 2025-09-18 15:10:20'),
(194, '2025-09-18 13:15:34', 'Admin admin updated Blog page hero text content at 2025-09-18 15:15:34'),
(195, '2025-09-18 13:20:20', 'Admin admin updated Blog page hero text content at 2025-09-18 15:20:20'),
(196, '2025-09-18 13:20:44', 'Admin admin updated Blog page hero text content at 2025-09-18 15:20:44'),
(197, '2025-09-18 13:20:58', 'Admin admin updated Blog page hero text content at 2025-09-18 15:20:58'),
(198, '2025-09-18 13:27:37', 'Admin admin uploaded new Blog hero image: blog_hero_68cc08c98e2dd.jpg at 2025-09-18 15:27:37'),
(199, '2025-09-18 13:27:37', 'Admin admin updated Blog page hero text content at 2025-09-18 15:27:37'),
(200, '2025-09-18 13:57:51', 'Admin admin updated Blog page hero text content at 2025-09-18 15:57:51'),
(201, '2025-09-18 14:01:30', 'Admin admin updated Blog page hero text content at 2025-09-18 16:01:30'),
(202, '2025-09-18 14:08:47', 'Admin admin uploaded new Blog hero image: blog_hero_68cc126f245cd.jpg at 2025-09-18 16:08:47'),
(203, '2025-09-18 14:08:47', 'Admin admin updated Blog page hero text content at 2025-09-18 16:08:47'),
(204, '2025-09-18 14:18:04', 'Admin admin uploaded new Blog hero image: blog_hero_68cc149c56904.jpg at 2025-09-18 16:18:04'),
(205, '2025-09-18 14:18:04', 'Admin admin updated Blog page hero text content at 2025-09-18 16:18:04'),
(206, '2025-09-18 14:36:34', 'Admin admin updated Contact page hero text content at 2025-09-18 16:36:34'),
(207, '2025-09-18 14:47:11', 'Admin admin updated Donate page text content.'),
(208, '2025-09-18 14:47:44', 'Admin admin updated Donate page text content.'),
(209, '2025-09-18 14:49:14', 'Admin admin updated About page content at 2025-09-18 16:49:14'),
(210, '2025-09-18 14:53:09', 'Admin admin created gallery: New initiave (ID: 3)'),
(211, '2025-09-18 14:53:29', 'Admin admin uploaded 1 images to gallery: New initiave (ID: 3)'),
(212, '2025-09-18 14:55:08', 'Admin admin deleted gallery: New initiave (ID: 3)'),
(213, '2025-09-18 14:58:40', 'Admin admin updated homepage hero/counter content at 2025-09-18 16:58:40'),
(214, '2025-09-18 14:59:13', 'Admin admin updated homepage hero/counter content at 2025-09-18 16:59:13'),
(215, '2025-09-18 14:59:52', 'Admin admin updated homepage hero/counter content at 2025-09-18 16:59:52'),
(216, '2025-09-18 15:00:05', 'Admin admin updated homepage hero/counter content at 2025-09-18 17:00:05'),
(217, '2025-09-18 15:00:21', 'Admin admin updated homepage hero/counter content at 2025-09-18 17:00:21'),
(218, '2025-09-18 15:00:36', 'Admin admin updated homepage hero/counter content at 2025-09-18 17:00:36'),
(219, '2025-09-19 06:06:38', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-19 08:06:38'),
(220, '2025-09-19 06:49:10', 'Admin admin updated homepage hero/counter content at 2025-09-19 08:49:10'),
(221, '2025-09-19 06:49:21', 'Admin admin updated homepage hero/counter content at 2025-09-19 08:49:21'),
(222, '2025-09-19 07:45:30', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-19 09:45:30'),
(223, '2025-09-19 08:33:15', 'Admin admin updated Internships page hero text content.'),
(224, '2025-09-19 08:33:29', 'Admin admin updated Internships page hero text content.'),
(225, '2025-09-19 08:48:01', 'Admin admin updated Partner page hero text content at 2025-09-19 10:48:01'),
(226, '2025-09-19 09:01:18', 'Admin admin updated Partner page hero text content.'),
(227, '2025-09-19 09:04:10', 'Admin admin uploaded new Partner hero image: partner_hero_68cd1c8a47720.jpg'),
(228, '2025-09-19 09:04:10', 'Admin admin updated Partner page hero text content.'),
(229, '2025-09-19 12:04:34', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-19 14:04:34'),
(230, '2025-09-19 12:05:59', 'Admin admin updated homepage hero/counter content at 2025-09-19 14:05:59'),
(231, '2025-09-19 12:06:20', 'Admin admin updated homepage hero/counter content at 2025-09-19 14:06:20'),
(232, '2025-09-19 12:10:16', 'Admin admin updated About page content at 2025-09-19 14:10:16'),
(233, '2025-09-19 12:13:16', 'Admin admin created gallery: Litsemba Rising Project (ID: 4)'),
(234, '2025-09-19 12:18:10', 'Admin admin uploaded 8 images to gallery: Litsemba Rising Project (ID: 4)'),
(235, '2025-09-19 12:19:15', 'Admin admin deleted gallery: Litsemba Rising Project (ID: 4)'),
(236, '2025-09-19 20:26:10', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.101 at 2025-09-19 22:26:10'),
(237, '2025-09-20 04:00:23', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.101 at 2025-09-20 06:00:23'),
(238, '2025-09-20 04:56:34', 'Admin admin updated Team page text content at 2025-09-20 06:56:34'),
(239, '2025-09-20 04:57:01', 'Admin admin updated Team page text content at 2025-09-20 06:57:01'),
(240, '2025-09-20 05:00:13', 'Admin admin updated Volunteer page hero text content at 2025-09-20 07:00:13'),
(241, '2025-09-20 05:06:19', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68ce364b556b9.jpg at 2025-09-20 07:06:19'),
(242, '2025-09-20 05:06:19', 'Admin admin updated Volunteer page hero text content at 2025-09-20 07:06:19'),
(243, '2025-09-20 05:10:25', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68ce374188f02.jpg at 2025-09-20 07:10:25'),
(244, '2025-09-20 05:10:25', 'Admin admin updated Volunteer page hero text content at 2025-09-20 07:10:25'),
(245, '2025-09-20 20:22:39', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.150 at 2025-09-20 22:22:39'),
(246, '2025-09-20 20:23:16', 'Admin admin updated Team page text content at 2025-09-20 22:23:16'),
(247, '2025-09-20 20:23:35', 'Admin admin updated Team page text content at 2025-09-20 22:23:35'),
(248, '2025-09-20 20:24:18', 'Admin admin updated Donate page text content.'),
(249, '2025-09-20 20:24:34', 'Admin admin updated Donate page text content.'),
(250, '2025-09-20 20:24:59', 'Admin admin updated Donate page text content.'),
(251, '2025-09-20 20:52:17', 'Admin admin updated Programs hero section text content.'),
(252, '2025-09-20 20:52:38', 'Admin admin updated Programs hero section text content.'),
(253, '2025-09-20 20:53:12', 'Admin admin created new program: test'),
(254, '2025-09-20 20:53:14', 'Admin admin updated Programs hero section text content.'),
(255, '2025-09-20 20:53:47', 'Admin admin deleted program ID: 11'),
(256, '2025-09-20 20:53:48', 'Admin admin updated Programs hero section text content.'),
(257, '2025-09-20 21:00:02', 'Admin admin updated Publications hero section text content.'),
(258, '2025-09-20 21:00:17', 'Admin admin updated Publications hero section text content.'),
(259, '2025-09-20 22:54:50', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.86 at 2025-09-21 00:54:50'),
(260, '2025-09-22 10:44:35', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-22 12:44:35'),
(261, '2025-09-22 12:50:30', 'Admin admin (ID: 1) logged in successfully from IP: 102.214.161.134 at 2025-09-22 14:50:30'),
(262, '2025-09-22 12:53:06', 'Admin admin uploaded new Thulani image: thulani_68d146b287066.jpg at 2025-09-22 14:53:06'),
(263, '2025-09-22 12:53:06', 'Admin admin updated Team page text content at 2025-09-22 14:53:06'),
(264, '2025-09-23 05:50:36', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-23 07:50:36'),
(265, '2025-09-23 09:02:51', 'Admin admin (ID: 1) logged in successfully from IP: 41.84.233.37 at 2025-09-23 11:02:51'),
(266, '2025-09-23 09:03:56', 'Admin admin uploaded new Thulani image: thulani_68d2627ca4ebd.jpg at 2025-09-23 11:03:56'),
(267, '2025-09-23 09:03:56', 'Admin admin updated Team page text content at 2025-09-23 11:03:56'),
(268, '2025-09-23 09:11:56', 'Admin admin updated Partner page hero text content.'),
(269, '2025-09-23 09:15:22', 'Admin admin updated program: Engaged Youth, Empowered & Inclusive Communities Project'),
(270, '2025-09-23 09:16:46', 'Admin admin updated program: Litsemba Rising Project'),
(271, '2025-09-23 09:17:39', 'Admin admin updated program: Read@Home'),
(272, '2025-09-23 09:18:21', 'Admin admin updated program: The Voluntary Medical Male Circumcision (VMMC)'),
(273, '2025-09-23 10:23:37', 'Admin admin (ID: 1) logged in successfully from IP: 41.84.233.37 at 2025-09-23 12:23:37'),
(274, '2025-09-23 10:32:39', 'Admin admin updated About page content at 2025-09-23 12:32:39 (New hero image: about_hero_68d27746ee949.jpg)'),
(275, '2025-09-23 10:37:09', 'Admin admin updated About page content at 2025-09-23 12:37:09 (New hero image: about_hero_68d278552d0e0.jpg)'),
(276, '2025-09-23 10:41:17', 'Admin admin updated program: Engaged Youth, Empowered & Inclusive Communities Project'),
(277, '2025-09-23 10:41:43', 'Admin admin updated program: Read@Home'),
(278, '2025-09-23 10:42:07', 'Admin admin updated program: The Voluntary Medical Male Circumcision (VMMC)'),
(279, '2025-09-23 10:44:39', 'Admin admin updated program: Young Woman Economic empowerment project (YWEE)'),
(280, '2025-09-23 11:22:23', 'Admin admin updated program: Young Woman Economic empowerment project (YWEE)'),
(281, '2025-09-23 11:29:37', 'Admin admin created gallery: Horticulture Learning exchange Visits (ID: 5)'),
(282, '2025-09-23 11:32:02', 'Admin admin uploaded 4 images to gallery: Horticulture Learning exchange Visits (ID: 5)'),
(283, '2025-09-23 11:44:16', 'Admin admin uploaded 5 images to gallery: Horticulture Learning exchange Visits (ID: 5)'),
(284, '2025-09-23 12:47:15', 'Admin admin updated program: Read@Home'),
(285, '2025-09-23 13:43:38', 'Admin admin (ID: 1) logged in successfully from IP: 41.215.151.122 at 2025-09-23 15:43:38'),
(286, '2025-09-23 13:44:27', 'Admin admin updated About page content at 2025-09-23 15:44:27 (New hero image: about_hero_68d2a43bcbf1b.jpg)'),
(287, '2025-09-23 13:45:13', 'Admin admin updated Internships page hero text content.'),
(288, '2025-09-23 13:45:37', 'Admin admin uploaded new Internships hero image: internships_hero_68d2a48190493.jpg'),
(289, '2025-09-23 13:45:37', 'Admin admin updated Internships page hero text content. New hero image: internships_hero_68d2a48190493.jpg'),
(290, '2025-09-23 13:45:37', 'Admin admin uploaded new Internships hero image: internships_hero_68d2a481e3f33.jpg'),
(291, '2025-09-23 13:45:37', 'Admin admin updated Internships page hero text content. New hero image: internships_hero_68d2a481e3f33.jpg'),
(292, '2025-09-23 13:46:08', 'Admin admin uploaded new Team hero image: team_hero_68d2a4a0a325d.jpg at 2025-09-23 15:46:08'),
(293, '2025-09-23 13:46:08', 'Admin admin updated Team page text content at 2025-09-23 15:46:08'),
(294, '2025-09-23 13:47:04', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68d2a4d838a10.jpg at 2025-09-23 15:47:04'),
(295, '2025-09-23 13:47:04', 'Admin admin updated Volunteer page hero text content at 2025-09-23 15:47:04'),
(296, '2025-09-23 15:54:11', 'Admin admin (ID: 1) logged in successfully from IP: 41.215.151.122 at 2025-09-23 17:54:11'),
(297, '2025-09-24 03:25:47', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.225 at 2025-09-24 05:25:47'),
(298, '2025-09-24 05:43:49', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-09-24 07:43:49'),
(299, '2025-09-24 06:07:40', 'Admin admin uploaded new Contact hero image: contact_hero_68d38aacecbfb.jpg at 2025-09-24 08:07:40'),
(300, '2025-09-24 06:07:40', 'Admin admin updated Contact page hero text content at 2025-09-24 08:07:40'),
(301, '2025-09-24 06:17:52', 'Admin admin updated Partner page hero text content at 2025-09-24 08:17:52'),
(302, '2025-09-24 06:23:36', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68d38e68a07a5.jpg at 2025-09-24 08:23:36'),
(303, '2025-09-24 06:23:36', 'Admin admin updated Volunteer page hero text content at 2025-09-24 08:23:36'),
(304, '2025-09-25 06:12:49', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-25 08:12:49'),
(305, '2025-09-25 06:19:17', 'Admin admin uploaded new Volunteer hero image: volunteer_hero_68d4dee52121e.jpg at 2025-09-25 08:19:17'),
(306, '2025-09-25 06:19:17', 'Admin admin updated Volunteer page hero text content at 2025-09-25 08:19:17'),
(307, '2025-09-25 07:02:06', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-09-25 09:02:06'),
(308, '2025-09-25 07:16:21', 'Admin admin updated Home page hero and counter content at 2025-09-25 09:16:21'),
(309, '2025-09-26 09:27:01', 'Admin admin (ID: 1) logged in successfully from IP: 41.215.151.209 at 2025-09-26 11:27:01'),
(310, '2025-09-26 09:28:03', 'Admin admin updated program: Litsemba Rising Project'),
(311, '2025-09-26 10:11:04', 'Admin admin created gallery: Litsemba Rising Project Launch (ID: 6)'),
(312, '2025-09-26 10:12:47', 'Admin admin uploaded 8 images to gallery: Litsemba Rising Project Launch (ID: 6)'),
(313, '2025-09-26 10:36:55', 'Admin admin uploaded 8 images to gallery: Litsemba Rising Project Launch (ID: 6)'),
(314, '2025-09-26 11:53:10', 'Admin admin (ID: 1) logged in successfully from IP: 41.84.251.200 at 2025-09-26 13:53:10'),
(315, '2025-09-26 11:53:36', 'Admin admin created gallery: Staff Team Building Sessions (ID: 7)'),
(316, '2025-09-26 11:55:05', 'Admin admin uploaded 6 images to gallery: Staff Team Building Sessions (ID: 7)'),
(317, '2025-09-26 11:55:38', 'Admin admin uploaded 6 images to gallery: Staff Team Building Sessions (ID: 7)'),
(318, '2025-09-26 12:02:33', 'Admin admin uploaded 6 images to gallery: Staff Team Building Sessions (ID: 7)'),
(319, '2025-09-26 12:03:53', 'Admin admin updated gallery ID: 6'),
(320, '2025-09-26 12:05:28', 'Admin admin updated gallery ID: 5'),
(321, '2025-09-26 12:06:17', 'Admin admin updated gallery ID: 7'),
(322, '2025-09-26 12:12:49', 'Admin admin uploaded new Blog hero image: blog_hero_68d68340b8b0e.jpg at 2025-09-26 14:12:49'),
(323, '2025-09-26 12:12:49', 'Admin admin updated Blog page hero text content at 2025-09-26 14:12:49'),
(324, '2025-09-26 12:13:33', 'Admin admin updated Blog page hero text content at 2025-09-26 14:13:33'),
(325, '2025-09-26 12:14:11', 'Admin admin uploaded new Blog hero image: blog_hero_68d6839350178.jpg at 2025-09-26 14:14:11'),
(326, '2025-09-26 12:14:11', 'Admin admin updated Blog page hero text content at 2025-09-26 14:14:11'),
(327, '2025-09-26 12:15:19', 'Admin admin uploaded new Blog hero image: blog_hero_68d683d75c86f.jpg at 2025-09-26 14:15:19'),
(328, '2025-09-26 12:15:19', 'Admin admin updated Blog page hero text content at 2025-09-26 14:15:19'),
(329, '2025-09-26 13:13:10', 'Admin admin (ID: 1) logged in successfully from IP: 41.84.251.200 at 2025-09-26 15:13:10'),
(330, '2025-09-26 13:13:38', 'Admin admin created gallery: Accelerating HIV Prevention Among Adolescent Boys and Young Men in Eswatini (ID: 8)'),
(331, '2025-09-26 13:14:33', 'Admin admin uploaded 5 images to gallery: Accelerating HIV Prevention Among Adolescent Boys and Young Men in Eswatini (ID: 8)'),
(332, '2025-09-26 13:19:02', 'Admin admin updated gallery ID: 8'),
(333, '2025-09-26 13:21:14', 'Admin admin updated gallery ID: 8'),
(334, '2025-09-26 13:50:16', 'Admin admin created gallery: Transforming Lives Through Horticulture and Broiler Production Training (ID: 9)'),
(335, '2025-09-26 13:54:19', 'Admin admin uploaded 7 images to gallery: Transforming Lives Through Horticulture and Broiler Production Training (ID: 9)'),
(336, '2025-09-27 18:31:19', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.15 at 2025-09-27 20:31:19'),
(337, '2025-09-27 22:52:31', 'Admin admin (ID: 1) logged in successfully from IP: 165.73.133.216 at 2025-09-28 00:52:31'),
(338, '2025-09-27 23:19:37', 'Admin admin updated details for image ID: 62'),
(339, '2025-09-27 23:20:45', 'Admin admin updated details for image ID: 62'),
(340, '2025-09-27 23:25:08', 'Admin admin updated details for image ID: 62'),
(341, '2025-09-27 23:25:32', 'Admin admin updated gallery hero section'),
(342, '2025-10-01 08:34:56', 'Admin admin (ID: 1) logged in successfully from IP: 41.84.239.241 at 2025-10-01 10:34:56'),
(343, '2025-10-01 08:35:29', 'Admin admin updated program: Young Women Economic empowerment project (YWEE)'),
(344, '2025-10-02 14:01:18', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.0.103 at 2025-10-02 16:01:18'),
(345, '2025-10-02 14:36:50', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-10-02 16:36:50'),
(346, '2025-10-02 14:38:51', 'Admin admin updated Volunteer page hero text content at 2025-10-02 16:38:51'),
(347, '2025-10-03 05:57:15', 'Admin admin (ID: 1) logged in successfully from IP: 41.204.7.83 at 2025-10-03 07:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `title`, `content`, `image_filename`, `sort_order`, `created_at`) VALUES
(1, 'Engaged Youth, Empowered & Inclusive Communities Project', '<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">The\r\nYouth Civic Engagement and Governance Strengthening Project, funded by the\r\nCommonwealth Foundation, is a strategic initiative designed to promote the\r\nactive participation of young people in local governance by revitalizing and\r\nenhancing existing engagement platforms. Implemented specifically within the\r\nManzini wards and targeting in-school youth, the project collaborates closely\r\nwith key partners including the Municipal Council of Manzini and the Ministry\r\nof Education and Training. Its core objectives encompass a comprehensive\r\nassessment of current youth participation levels, identification of barriers to\r\ninvolvement, and development of tailored, effective strategies to foster\r\nmeaningful and sustained youth engagement in governance processes. A hallmark\r\nof this initiative is the creation and implementation of a customized\r\ncurriculum, developed from assessment findings to address gaps and build the\r\ncapacity of both youth and the Municipal Council. Beyond curriculum delivery,\r\nthe project provides continuous support to ensure the practical application and\r\ninstitutionalization of acquired knowledge and best practices, thereby\r\nstrengthening the overall system for youth civic participation in the region.<o:p></o:p></span></p>', 'program_68d2794d7fc93.jpg', 1, '2025-08-22 12:43:04'),
(2, 'Read@Home', '<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">The\r\nRead@Home project, funded by the World Bank and implemented in partnership with\r\nEswatini’s Ministry of Education and Training (MoET), is a pioneering pilot\r\nintervention designed to enhance early learning outcomes by introducing young\r\nchildren to books and fostering their engagement with reading. Grounded in\r\nrigorous global research that shows early access to books and interactive\r\nbook-sharing substantially improve language development and literacy skills,\r\nthe project pursues several key objectives: strengthening caregivers’\r\nknowledge, skills, and confidence to support early literacy at home;\r\ncultivating a sustainable culture of reading and peer learning among\r\ncaregivers; increasing the availability of culturally relevant, age-appropriate\r\nreading materials; promoting children\'s literacy, language, and cognitive\r\ndevelopment; and building teacher capacity to effectively integrate literacy in\r\nearly childhood education settings. Targeting children aged 0–5 years and their\r\ncaregivers across four Tinkhundla (Maseyisini, Mayiwane, Mafutseni, and\r\nSithobela) spanning all regions of Eswatini, the initiative employs a\r\nhub-and-spoke model connecting Grade 0 schools (hubs) with pre-schools (spokes)\r\nin seven chiefdoms, also reaching out-of-school children. To date, Read@Home\r\nhas directly benefited 752 children and 610 caregivers, equipping families with\r\nessential tools and resources to nurture foundational literacy skills and\r\nsupport lifelong learning journeys, marking a significant milestone in\r\nstrengthening early childhood education and community empowerment in Eswatini.<o:p></o:p></span></p>', 'program_68d296cf95a67.jpg', 2, '2025-08-22 12:43:04'),
(3, 'The Voluntary Medical Male Circumcision (VMMC)', '<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">The\r\nVoluntary Medical Male Circumcision (VMMC) initiative, supported by leading\r\ndonors including PEPFAR, the Global Fund, and the CDC, operates in strong\r\npartnership with the Ministry of Health and global collaborators like CHAPS,\r\nICAP, EGPAF, PSI Eswatini, and Georgetown University. Since launching in 2009,\r\nthis vital HIV prevention program targets males aged 10 to 49 years, with a\r\nconcentrated effort on adolescents and young men aged 15 to 29.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">Our\r\nproject’s involvement and approach centers on driving demand for circumcision\r\nservices by tackling uptake barriers through comprehensive sensitization and\r\nmobilization campaigns. We engage not only youth and young men—the primary\r\nbeneficiaries—but also influential community stakeholders such as parents,\r\nguardians, and employers to ensure widespread awareness and acceptance.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">Operating\r\nacross Eswatini’s four regions—Hhohho, Lubombo, Shiselweni, and Manzini—the\r\nprogram reaches schools, workplaces, tertiary institutions, and key companies.\r\nOver the last decade, we have successfully connected more than 10,000 males to\r\nvital circumcision services. Our approach uniquely fosters community ownership\r\nby collaborating with rural authorities, shifting cultural perceptions, and\r\nemploying mobilizers sourced directly from local communities. This strategy\r\nenhances participation and secures long-term impact.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">As\r\na trusted implementing partner with extensive experience in multi-regional HIV\r\nprevention, the organization exemplifies commitment to innovation and\r\nexcellence. We continuously evolve strategies to overcome challenges related to\r\nservice uptake while advancing community-led solutions that embed the program\r\nwithin local tradition and leadership.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">Our\r\nmeasurable successes include significantly increasing service reach into\r\ndiverse settings and contributing meaningfully to Eswatini’s progress in HIV\r\nprevention. Strong alliances with the Ministry of Health and prominent partners\r\namplify our efforts, enabling us to leverage expertise and resources for\r\nmaximum program effectiveness and sustainability.<o:p></o:p></span></p>', 'program_68d2797f3174a.jpg', 3, '2025-08-22 12:43:04'),
(4, 'Litsemba Rising Project', '<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">The\r\nLitsemba Rising Project, anchored by the Campus Sexual Harassment Prevention\r\nIntervention (CSHP), is a groundbreaking three-year initiative (2023–2026)\r\ndedicated to promoting the welfare and protection of vulnerable children,\r\nyouth, caregivers, and families within Eswatini’s higher education sector.\r\nSupported by the UK Foreign, Commonwealth &amp; Development Office (FCDO)\r\nthrough the prestigious What Works to Prevent Violence: Innovation Grants (What\r\nWorks 2 Programme), this project exemplifies our commitment to fostering safe\r\nand inclusive environments where young people can thrive without fear of sexual\r\nharassment.<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"text-align:justify\"><span style=\"font-family:&quot;Times New Roman&quot;,serif\">In\r\npartnership with Women Unlimited Eswatini and in collaboration with the\r\nUniversity of Eswatini (UNESWA), through the Litsemba Rising we target systemic\r\nchange in Higher Education Institutions (HEIs) by empowering both students and\r\nstaff to participate in creating campuses free from violence and\r\ndiscrimination. The project’s comprehensive approach not only addresses\r\nimmediate safety concerns but also strengthens the broader campus climate,\r\nensuring lasting positive impacts on vulnerable youth and their communities.<o:p></o:p></span></p>', 'program_68d65ca31b020.jpg', 0, '2025-08-22 13:03:30'),
(6, 'Young Women Economic empowerment project (YWEE)', '<h2 dir=\"ltr\" style=\"line-height:1.3900000000000001;margin-top:8pt;margin-bottom:4pt;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: &quot;Times New Roman&quot;, serif; font-size: 12pt; white-space-collapse: preserve; text-align: justify;\">The Young Woman Economic Empowerment (YWEE) project was strategically designed to enhance the economic resilience of young rural women entrepreneurs, particularly those whose small-scale agribusinesses were adversely impacted by the COVID-19 pandemic. Funded by the German Federal Ministry through GIZ, with implementation support from GOPA Worldwide Consultants (GmbH). This initiative targeted women engaged primarily in horticulture and broiler production across eight constituencies within the Lubombo, Manzini, and Hhohho regions of the Kingdom of Eswatini.</span></h2><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Aligned with our mandate to improve the livelihoods of vulnerable children, youth, and their families, the project focused on strengthening the entrepreneurial capacity of these women—many of whom are sole providers for their households. In partnership with the Ministry of Agriculture, specifically the Departments of Agricultural Research and Veterinary and Livestock Services, beneficiaries were equipped with critical skills in financial literacy, market access, and technical expertise relevant to their agribusiness sectors.</span></p><p><span id=\"docs-internal-guid-3aaf39a3-7fff-656a-2c9a-b3a68563d5aa\"></span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Over 300 women benefitted from comprehensive training sessions, including practical field visits facilitating peer learning and exposure to established enterprises. This collaborative intervention not only enhanced the participants’ business sustainability but also contributed to broader socio-economic empowerment in rural communities.</span></p>', 'program_68d282eb06d51.jpg', 4, '2025-09-04 13:56:42'),
(7, 'SABELO SENSHA PROJECT', '<h2 dir=\"ltr\" style=\"line-height:1.3900000000000001;margin-top:8pt;margin-bottom:4pt;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: &quot;Times New Roman&quot;, serif; font-size: 12pt; white-space-collapse: preserve; text-align: justify;\">The SABELO SENSHA project, generously funded by PEPFAR, was implemented within the DREAMS Tinkhundla framework, targeting up to eight Tinkhundla across the Lubombo, Manzini, and Hhohho regions of Eswatini. This initiative was specifically designed to enhance the wellbeing of Orphans and Vulnerable Children (OVC), adolescents, and young women through comprehensive HIV prevention and support interventions, gender-based violence prevention and response, and livelihoods empowerment.</span></h2><p><span id=\"docs-internal-guid-8bef2550-7fff-20ca-8970-982c3f8f59ad\"></span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">In alignment with our mandate to improve the lives of vulnerable children, youth, and their families, the project was executed in close partnership with relevant government ministries, as well as international and local organizations, collectively reaching thousands of beneficiaries on an annual basis. Implementation was led by trained professionals with specialized expertise across the program components, supported at the community level by cadres who engaged directly with beneficiaries to provide tailored support. The project played a critical role in ensuring that children remained enrolled in school, received necessary treatment adherence support, and that vulnerable families and youth were provided with sustainable income-generating opportunities. Through this integrated approach, the project significantly contributed to the overall welfare and resilience of vulnerable populations within the targeted regions.</span></p>', 'program_68b99b8f8bc14.jpg', 5, '2025-09-04 13:57:17'),
(8, 'HIV Prevention Life Skills Education in Secondary Schools (LSE)', '<h2 dir=\"ltr\" style=\"line-height:1.3900000000000001;margin-top:8pt;margin-bottom:4pt;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: &quot;Times New Roman&quot;, serif; font-size: 12pt; white-space-collapse: preserve; text-align: justify;\">This project, generously supported by our donor partners, was implemented over two years in the Sithobela and Siphofaneni regions of Eswatini, targeting 1,800 at-risk adolescent girls. The initiative focused on strengthening educational retention among school-attending girls and providing alternative education platforms for adolescent wives and young mothers who have been forced to leave the formal school system. By addressing key vulnerabilities, the program aimed to enhance girls’ educational outcomes, increase their social protective assets, and transform community social norms surrounding girls’ education.</span></h2><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Aligned with our mandate to improve the lives of vulnerable children, youth, and their families, the core objectives of this program include:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Preventing school dropout by using an Early Warning System that identifies girls at risk and offered proactive support.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Enhancing social and protective assets through the facilitation of Protect Our Youth clubs.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Providing scholarships to sustain the education of girls vulnerable to discontinuing secondary education.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Conducting community sensitization campaigns to elevate the understanding and value of girls’ education within families and community structures.</span></p></li></ul><p><span id=\"docs-internal-guid-b3f909ba-7fff-ba81-5ef1-93196fbc9cf7\"></span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">In tandem with prevention strategies, the program also empowered teen mothers who have exited formal schooling by delivering non-formal education and mentorship opportunities. Early Childhood Stimulation training equips young mothers to foster optimal development for their children, ensuring a positive intergenerational impact. The success of this initiative was underpinned by strong collaboration with local community stakeholders, education authorities, and youth-focused organizations, ensuring a holistic and sustainable approach to breaking the cycle of poverty. By equipping girls and young women with education and life skills, this program contributes significantly to reducing their vulnerability to disease, including HIV and AIDS, and enhances their long-term economic opportunities, ultimately fostering resilient families and communities.</span></p>', 'program_68b99b9bbbb98.jpg', 6, '2025-09-04 13:58:15'),
(9, 'GO Girls Connect', '<h2 dir=\"ltr\" style=\"line-height:1.3900000000000001;margin-top:8pt;margin-bottom:4pt;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: &quot;Times New Roman&quot;, serif; font-size: 12pt; white-space-collapse: preserve; text-align: justify;\">Supported by key donors committed to advancing gender equality and youth empowerment, the Go Girls Connect! initiative was implemented across Eswatini, targeting vulnerable adolescent girls and young women. The program’s core mandate aligned with our commitment to improving the lives of vulnerable children, youth, and their families by fostering digital inclusion, strengthening protective mechanisms, and promoting gender rights advocacy.</span></h2><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">The program leveraged innovative mobile-based technology to enhance digital literacy and equip girls with critical life skills, protective assets, and access to resources. Central to its objectives was empowering young women to effectively challenge restrictive gender norms, advocate for their human rights, and build resilience against gender-based violence (GBV).</span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Key components of the program included:</span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Delivery of 30 evidence-based, human rights-focused digital sessions through the </span><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:italic;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Protect Our Youth</span><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\"> platform, reaching 1,650 adolescent girls. These sessions aimed to increase knowledge, boost civic engagement, and strengthen girls’ demand for and access to their rights.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Deployment of a self-administered GBV screening tool designed to trigger an immediate GBV Response Protocol, thereby connecting survivors to critical post-abuse services efficiently.</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:10pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Implementation of a robust Early Warning System, utilizing a self-administered screening tool to identify girls at risk of dropping out of secondary school and facilitating timely interventions to support school retention.</span></p></li></ul><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Our partnership with Cell-Ed, a leader in mobile learning solutions, was instrumental in developing and delivering this innovative program model. Together, we established a scalable and evidence-based approach that addresses the digital gender divide, promotes protective environments, and facilitates access to essential GBV services.</span></p><p><span id=\"docs-internal-guid-1af338f5-7fff-85c6-e121-6ab6a19ad24c\"></span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">By combining technology, rights-based education, and comprehensive support systems, Go Girls Connect! exemplified impactful collaboration that advances our organizational mission to uplift vulnerable girls and youth, ensuring they can thrive, advocate for their rights, and build healthier futures.</span></p>', 'program_68b99ba746e25.jpg', 7, '2025-09-04 13:58:49'),
(10, 'National Case Management System – Eswatini', '<h2 dir=\"ltr\" style=\"line-height:1.3900000000000001;margin-top:8pt;margin-bottom:4pt;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: &quot;Times New Roman&quot;, serif; font-size: 12pt; white-space-collapse: preserve; text-align: justify;\">The Organization supported by key development partners, the National Case Management System (NCMS) for Eswatini was developed in close collaboration with the Department of Social Welfare (DSW) to establish a harmonized, HIV-sensitive framework for social welfare service delivery. This initiative targeted vulnerable children, youth, and their families across Eswatini, with initial implementation focusing on 10 communities within the Lubombo and Shiselweni regions.</span></h2><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">The core objective of the NCMS is to enhance the quality and accessibility of social work services by strengthening referral pathways and coordination mechanisms among governmental agencies and civil society organizations. By integrating regional stakeholder forums and empowering community-based cadres such as Community Case Workers, the system ensures timely identification, referral, and continuous follow-up of vulnerable children to the appropriate services, contributing to effective case closure.</span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Underpinned by national policies and statutory regulations, the project successfully developed a comprehensive national framework, including standard operating procedures, a Social Worker Manual, and community worker handbooks to support case management. Capacity building efforts include tailored training programs and a coaching and mentoring plan to enable DSW social workers\' adoption and sustained use of the NCMS.</span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Strong partnerships with DSW facilitated quarterly regional stakeholder forums to improve care coordination and address implementation challenges. Furthermore, social workers were supported to train and supervise community volunteers, reinforcing service delivery at the grassroots level.</span></p><p><span id=\"docs-internal-guid-0be59669-7fff-0782-92ff-8b8444f48a86\"></span></p><p dir=\"ltr\" style=\"line-height:1.3900000000000001;text-align: justify;margin-top:0pt;margin-bottom:8pt;\"><span style=\"font-size:12pt;font-family:\'Times New Roman\',serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Through these collaborative efforts, the NCMS advances our mandate to improve the well-being and resilience of vulnerable children, youth, and families by establishing an integrated, sustainable social welfare system responsive to their needs.</span></p>', 'program_68b99be8cac70.jpg', 8, '2025-09-04 13:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(100) DEFAULT 'general',
  `sort_order` int(11) DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publications`
--

INSERT INTO `publications` (`id`, `title`, `description`, `filename`, `original_filename`, `file_type`, `file_size`, `category`, `sort_order`, `uploaded_by`, `uploaded_at`, `updated_at`) VALUES
(5, 'POY MANUAL Modules 1_6_FINAL-2', 'Protecting Our Youth Manual', 'publication_68c15b5e75f8b.pdf', '', 'pdf', 5540265, 'general', 0, 1, '2025-09-10 11:05:02', '2025-09-23 09:10:30'),
(6, 'LL Manual FINAL english', 'Lisango & Liguma Manual', 'publication_68c15b80bc776.pdf', '', 'pdf', 997172, 'general', 1, 1, '2025-09-10 11:05:36', '2025-09-23 09:08:34'),
(7, 'HORTICULTURE_JOB_AID', 'Horticulture Job Aid', 'publication_68c15b9521a33.pdf', '', 'pdf', 2348282, 'general', 2, 1, '2025-09-10 11:05:57', '2025-09-23 09:08:10'),
(8, 'YWEE BROILERS JOB AID', 'Broilers Production Job Aid', 'publication_68c15bac08c52.pdf', '', 'pdf', 1619342, 'general', 3, 1, '2025-09-10 11:06:20', '2025-09-23 09:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('about_approach_intro', 'We tackle complex challenges with comprehensive strategies, addressing root causes and fostering long-term resilience.'),
('about_approach_item_1_heading', 'HIV Prevention & Care'),
('about_approach_item_1_number', '01'),
('about_approach_item_1_text', 'Providing education, testing, treatment adherence support, and comprehensive care for those affected by HIV & AIDS.'),
('about_approach_item_2_heading', 'Child Protection'),
('about_approach_item_2_number', '02'),
('about_approach_item_2_text', 'Working to eliminate violence against children and address gender-based violence to create safer communities.'),
('about_approach_item_3_heading', 'Early Childhood Development'),
('about_approach_item_3_number', '03'),
('about_approach_item_3_text', 'Investing in the foundation of life through quality early childhood care and development education.'),
('about_approach_item_4_heading', 'Community Empowerment'),
('about_approach_item_4_number', '04'),
('about_approach_item_4_text', 'Strengthening families and communities through capacity building, income generation, and support programs.'),
('about_approach_item_5_heading', 'Policy Advocacy'),
('about_approach_item_5_number', '05'),
('about_approach_item_5_text', 'Engaging in strategic initiatives to influence and shape policy frameworks that benefit children and families.'),
('about_approach_item_6_heading', 'Strategic Partnerships'),
('about_approach_item_6_number', '06'),
('about_approach_item_6_text', 'Collaborating with government, NGOs, civil society, and donors like PEPFAR, OSISA, and the Global Fund to amplify impact.'),
('about_approach_title', 'Holistic Care, Lasting Impact'),
('about_hero_image', 'about_hero_68d2a43bcbf1b.jpg'),
('about_hero_title', 'Our Journey of Hope'),
('about_history_image', 'about_history_689da9997b668.jpg'),
('about_mission_text', 'To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment.'),
('about_mission_title', 'Our Mission'),
('about_stats_children_number', '14328'),
('about_stats_children_text', 'Children Supported'),
('about_stats_regions_number', '4'),
('about_stats_regions_text', 'Regions Impacted'),
('about_stats_staff_number', '15'),
('about_stats_staff_text', 'Dedicated Staff & Volunteers'),
('about_stats_title', ''),
('about_stats_years_number', '16'),
('about_stats_years_text', 'Years of Service'),
('about_story_heading', 'Where Necessity Met Compassion'),
('about_story_text_1', 'Established in 2008 in the Lubombo region of Eswatini, the Bantwana Initiative was born from a profound need and an unwavering commitment to action. Witnessing the challenges faced by orphaned and vulnerable children, youth, and their families affected by HIV & AIDS and poverty, we knew we had to respond.'),
('about_story_text_2', 'What started as a localized effort has grown into a national force for good, driven by our mission to enhance the well-being and resilience of those we serve through holistic care, protection, and empowerment.'),
('about_story_text_3', 'Our journey is one of collaboration, innovation, and relentless dedication to building a better future for every child.'),
('about_values_intro', ''),
('about_values_title', ''),
('about_vision_text', 'We envision a society where every child is healthy, safe, and empowered to realize their full potential in a nurturing and equitable environment.'),
('about_vision_title', 'Our Vision'),
('blog_hero_image', 'blog_hero_68d683d75c86f.jpg'),
('blog_hero_subtitle', 'Stay updated with our latest news and stories..'),
('blog_hero_title', 'Blog'),
('careers_hero_image', 'careers_hero_68b6e22b8e615.png'),
('careers_hero_subtitle', 'Join a team passionate about creating lasting change for children and families'),
('careers_hero_title', 'Build Your Career With Us'),
('contact_hero_image', 'contact_hero_68d38aacecbfb.jpg'),
('contact_hero_subtitle', 'We\'d love to hear from you. Reach out with any questions or comments.'),
('contact_hero_title', 'Get In Touch.'),
('donate_hero_image', 'donate_hero_68b99f9864023.jpg'),
('donate_hero_subtitle', 'Transparent, accountable, and impactful giving'),
('donate_hero_title', 'Invest in Our Futures'),
('donate_main_content', '<p>We are committed to using your generous support effectively and efficiently to maximize our impact on the lives of vulnerable children and families.</p><p>At Bantwana Initiative Eswatini, we believe in complete transparency. We ensure that your donation has the maximum possible impact. Our financial practices are guided by the highest standards of accountability.</p>'),
('donate_main_heading', 'Your Donation at Work'),
('donate_main_subheading', 'Transparency and Accountability'),
('gallery_hero_image', 'bg_6.jpg'),
('gallery_hero_subtitle', 'Explore our collection of impactful moments'),
('gallery_hero_title', 'Our Gallery'),
('home_counter_donate_text', 'Your contribution makes a direct impact. Help us continue our vital programs.'),
('home_counter_donate_title', 'Support Our Work'),
('home_counter_main_text', 'Served Over'),
('home_counter_number', '37661'),
('home_counter_unit', 'Children in 4 countries in Africa'),
('home_counter_volunteer_text', 'Give your time and skills to make a difference in our communities.'),
('home_counter_volunteer_title', 'Be a Volunteer'),
('home_hero_image', 'hero_68b99d863cdb6.jpg'),
('home_hero_subtitle', 'To enhance the well-being and resilience of vulnerable children, youth, and their families affected by HIV & AIDS and poverty through holistic care, protection, and empowerment.'),
('home_hero_title', 'Bantwana Initiative Eswatini'),
('internships_hero_image', 'internships_hero_68d2a481e3f33.jpg'),
('internships_hero_subtitle', 'Apply your academic knowledge in a real-world development setting'),
('internships_hero_title', 'Gain Experience, Make an Impact'),
('partner_hero_image', 'partner_hero_68cd1c8a47720.jpg'),
('partner_hero_subtitle', 'Join forces to create sustainable change for vulnerable children and families.'),
('partner_hero_title', 'Collaborate For Greater Impact'),
('programs_hero_image', 'programs_hero_68b99d4a18843.jpg'),
('programs_hero_subtitle', 'Building resilient futures for children, youth, and families through comprehensive support.'),
('programs_hero_title', 'Our Programs'),
('program_economic_image', 'bg_1.jpg'),
('program_education_image', 'bg_1.jpg'),
('program_health_image', 'bg_1.jpg'),
('program_social_image', 'bg_1.jpg'),
('program_youth_image', 'bg_1.jpg'),
('publications_hero_image', 'hero_68d2c2d41cf54.jpg'),
('publications_hero_subtitle', 'Access our reports, manuals, and resources'),
('publications_hero_title', 'Our Publications'),
('team_hero_image', 'team_hero_68d2a4a0a325d.jpg'),
('team_hero_subtitle', 'Meet the dedicated individuals driving our mission.'),
('team_hero_title', 'Our Team'),
('team_thulani_image', 'thulani_68d2627ca4ebd.jpg'),
('volunteer_hero_image', 'volunteer_hero_68d4dee52121e.jpg'),
('volunteer_hero_subtitle', 'Make a direct impact in the lives of vulnerable children and families'),
('volunteer_hero_title', 'Give Your Time');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@bantwana.co.sz', '$2y$10$6qz6pRaokeCL3XpvUkGRJ.E.FbDa/aKLVsoCZUFzihNevtX3huH5S', '2025-08-08 10:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `interests` varchar(255) DEFAULT NULL,
  `availability` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `contact_settings`
--
ALTER TABLE `contact_settings`
  ADD PRIMARY KEY (`setting_key`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gallery_id` (`gallery_id`);

--
-- Indexes for table `hero_sections`
--
ALTER TABLE `hero_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `filename` (`filename`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `hero_sections`
--
ALTER TABLE `hero_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internships`
--
ALTER TABLE `internships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `gallery_images_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
