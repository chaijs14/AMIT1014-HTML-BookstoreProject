-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 12:43 PM
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
-- Database: `com3_2o_buy`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `created_at`) VALUES
(1, 'Fiction', '2026-04-16 17:21:54'),
(2, 'Comedy', '2026-04-16 17:21:54'),
(3, 'Novels', '2026-04-16 17:21:54'),
(4, 'Fantasy', '2026-04-16 17:21:54'),
(5, 'Romance', '2026-04-16 17:21:54'),
(6, 'Mystery', '2026-04-16 17:21:54'),
(7, 'Travel', '2026-04-16 17:21:54'),
(8, 'Notebook', '2026-04-16 17:21:54'),
(9, 'Reference', '2026-04-16 17:21:54'),
(10, 'Textbook', '2026-04-16 17:21:54'),
(13, 'Horror', '2026-04-19 17:33:31');

-- --------------------------------------------------------

--
-- Table structure for table `email_verification`
--

CREATE TABLE `email_verification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

CREATE TABLE `holiday` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `order_status`, `payment_date`, `created_at`) VALUES
(1, 2, 111.80, 'Rejected', NULL, '2026-03-28 14:26:44'),
(2, 2, 55.80, 'Approved', NULL, '2026-04-13 01:40:11'),
(3, 2, 55.80, 'Approved', NULL, '2026-04-15 00:18:21'),
(4, 2, 55.80, 'Approved', NULL, '2026-04-15 08:42:52'),
(5, 2, 55.80, 'Approved', NULL, '2026-04-15 09:24:38'),
(6, 2, 165.50, 'Approved', NULL, '2026-04-15 10:37:07'),
(7, 2, 49.00, 'Approved', NULL, '2026-04-16 00:22:27'),
(8, 14, 210.00, 'Pending', NULL, '2026-04-16 18:48:57'),
(9, 14, 143.00, 'Approved', '2026-04-18 21:04:45', '2026-04-18 21:00:27'),
(10, 2, 140.00, 'Approved', '2026-04-18 22:34:36', '2026-04-18 22:32:45'),
(11, 14, 166.00, 'Approved', '2026-04-18 22:51:40', '2026-04-18 22:50:29'),
(12, 14, 92.00, 'Approved', NULL, '2026-04-18 23:11:24'),
(13, 2, 160.00, 'Approved', NULL, '2026-04-18 23:11:57'),
(14, 2, 286.00, 'Approved', '2026-04-19 16:11:27', '2026-04-19 15:56:16'),
(15, 2, 70.00, 'Rejected', NULL, '2026-04-19 16:29:39'),
(16, 20, 103.00, 'Rejected', NULL, '2026-04-19 18:11:30');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 6, 1, 55.80),
(2, 1, 7, 1, 34.50),
(3, 1, 16, 1, 21.50),
(4, 2, 6, 1, 55.80),
(5, 3, 6, 1, 55.80),
(6, 4, 6, 1, 55.80),
(7, 5, 6, 1, 55.80),
(8, 6, 27, 1, 97.50),
(9, 6, 49, 1, 68.00),
(10, 7, 41, 1, 49.00),
(11, 8, 24, 1, 28.00),
(12, 8, 15, 1, 64.00),
(13, 8, 40, 1, 80.00),
(14, 8, 11, 1, 38.00),
(15, 9, 22, 1, 56.00),
(16, 9, 13, 1, 87.00),
(17, 10, 12, 1, 67.00),
(18, 10, 28, 1, 31.00),
(19, 10, 2, 1, 42.00),
(20, 11, 31, 1, 12.00),
(21, 11, 41, 1, 49.00),
(22, 11, 38, 1, 75.00),
(23, 11, 1, 1, 30.00),
(24, 12, 6, 1, 70.00),
(25, 12, 5, 1, 22.00),
(26, 13, 46, 1, 40.00),
(27, 13, 48, 1, 120.00),
(28, 14, 49, 2, 68.00),
(29, 14, 38, 2, 75.00),
(30, 15, 6, 1, 70.00),
(31, 16, 6, 1, 70.00),
(32, 16, 43, 1, 33.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(3, 2, '3b64ff4e37d51426eeb68bc27713d88aa78c237671a7da4e9d1cfad1b9fa809b', '2026-04-18 15:28:35', '2026-04-18 20:28:35'),
(5, 15, 'eb0677b90ea31bdc1f540f833aa2036ab0f58a3e0d4fdf8af69f4cf9bde74f3d', '2026-04-18 15:34:35', '2026-04-18 20:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT 'https://via.placeholder.com/150x200?text=Book+Cover',
  `category` varchar(50) DEFAULT 'Books'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `title`, `author`, `price`, `image_url`, `category`) VALUES
(1, 'The Midnight Library', 'Matt Haig', 30.00, 'midnight.png', 'Fiction'),
(2, 'Where the Crawdads Sing', 'Delia Owens', 42.00, 'crawdads.png', 'Fiction'),
(3, 'Circe', 'Madeline Miller', 17.00, 'circe.png', 'Fiction'),
(4, 'The Night Circus', 'Erin Morgenstern', 25.00, 'circus.png', 'Fiction'),
(5, 'Bossypants', 'Tina Fey', 22.00, 'bossypants.png', 'Comedy'),
(6, 'Born a Crime', 'Trevor Noah', 70.00, 'crime.png', 'Comedy'),
(7, 'Me Talk Pretty One Day', 'David Sedaris', 34.50, 'oneday.png', 'Comedy'),
(8, 'Yearbook', 'Allyson Braithwaite Condie', 38.00, 'yearbook.png', 'Comedy'),
(9, 'The Great Gatsby', 'F. Scott Fitzgerald', 18.50, 'gatsby.png', 'Novels'),
(10, 'To Kill a Mockingbird', 'Harper Lee', 12.00, 'mockingbird.png', 'Novels'),
(11, '1984', 'George Orwell', 38.00, '1984.png', 'Novels'),
(12, 'Little Women', 'Louisa May Alcott', 67.00, 'women.png', 'Novels'),
(13, 'The Name of the Wind', 'Patrick Rothfuss', 87.00, 'wind.png', 'Fantasy'),
(14, 'The Way of Kings', 'Brandon Sanderson', 45.00, 'kings.png', 'Fantasy'),
(15, 'A Game of Thrones', 'George R.R. Martin', 64.00, 'thrones.png', 'Fantasy'),
(16, 'The Lord Of The Ring: The Fellowship of the Ring', 'J.R.R. Tolkien', 21.50, 'ring.png', 'Fantasy'),
(17, 'The Love Hypothesis', 'Ali Hazelwood', 15.00, 'hypothesis.png', 'Romance'),
(18, 'It Ends with Us', 'Colleen Hoover', 26.00, 'ends.png', 'Romance'),
(19, 'The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', 90.00, 'hugo.png', 'Romance'),
(20, 'Red, White & Royal Blue', 'Casey McQuiston', 45.50, 'red.png', 'Romance'),
(21, 'The Silent Patient', 'Alex Michaelides', 77.00, 'patient.png', 'Mystery'),
(22, 'Gone Girl', 'Gillian Flynn', 56.00, 'girl.png', 'Mystery'),
(23, 'The Guest List', 'Lucy Foley', 72.00, 'guest.png', 'Mystery'),
(24, 'And Then There Were None', 'Agatha Christie', 28.00, 'none.png', 'Mystery'),
(25, 'Lonely Planet: Japan', 'Lonely Planet', 48.00, 'japan.png', 'Travel'),
(26, 'Rick Steves: Italy', 'Rick Steves', 56.70, 'italy.png', 'Travel'),
(27, 'Fodor’s Essential France', 'Fodor', 97.50, 'fodor.png', 'Travel'),
(28, 'DK Eyewitness: London', 'DK Publishing', 31.00, 'london.png', 'Travel'),
(29, 'Classic Lined Journal', 'Moleskine', 19.95, 'journal.png', 'Notebook'),
(30, 'Dot Grid A5 Hardcover', 'Leuchtturm', 8.50, 'hardcover.png', 'Notebook'),
(31, 'Soft Cover Pocket Book', 'Rhodia', 12.00, 'book.png', 'Notebook'),
(32, 'Spiral Bound Project Pad', 'Oxford', 5.00, 'pad.png', 'Notebook'),
(33, 'Merriam-Webster Dictionary', 'Merriam-Webster', 23.00, 'merriam.png', 'Reference'),
(34, 'The Elements of Style', 'Strunk & White', 12.50, 'elements.png', 'Reference'),
(35, 'National Geographic Atlas', 'Nat Geo', 45.00, 'geo.png', 'Reference'),
(36, 'Oxford English Grammar', 'Oxford', 60.00, 'eng.png', 'Reference'),
(37, 'Biology: A Global Approach', 'Campbell', 85.00, 'bio.png', 'Textbook'),
(38, 'Principles of Economics', 'Mankiw', 75.00, 'eko.png', 'Textbook'),
(39, 'Introduction to Algorithms', 'CLRS', 90.00, 'algorithms.png', 'Textbook'),
(40, 'Calculus: Early Transcendentals', 'Stewart', 80.00, 'calculus.png', 'Textbook'),
(41, 'To The Bright Edge of the World', 'Eowyn Ivey', 49.00, 'world.png', 'Fiction'),
(42, 'Really Good, Actually', 'Monica Heisey', 35.00, 'read.png', 'Comedy'),
(43, 'Pride and Prejudice', 'Jane Austen', 33.00, 'pride.png', 'Novels'),
(44, 'The Darkness That Comes Before', 'R. Scott Bakker', 65.00, 'before.png', 'Fantasy'),
(45, 'Dr. Alpha Male', 'Anjell', 27.00, 'male.png', 'Romance'),
(46, 'Malaysian Murders and Mysteries', 'Martin Vengadesan', 40.00, 'murder.png', 'Mystery'),
(47, 'National Geo Traveler: China', 'National Geographic', 18.00, 'china.png', 'Travel'),
(48, 'English for Everyone', 'DK', 120.00, 'english.png', 'Reference'),
(49, 'HTML & CSS Essentials for Dummies', 'McFedries Paul', 68.00, 'html.png', 'Textbook'),
(50, 'Access to History for Cambridge International Modern Europe 1750-1921', 'Nicholas Fellows', 65.00, 'history.png', 'Textbook');

-- --------------------------------------------------------

--
-- Table structure for table `remember_me`
--

CREATE TABLE `remember_me` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_me`
--

INSERT INTO `remember_me` (`id`, `user_id`, `selector`, `token_hash`, `expires_at`) VALUES
(1, 1, 'ebf1036fb8337759', '4b4b104f543c82db9ace16418ce39167242e6c7771e9913563a98b2319811205', '2026-04-27 07:03:24'),
(7, 2, '', 'f0ed942968613cd90296bdf1af56753afd24245be199f9e86bb7fa3468ab5be2', '2026-05-18 16:32:31'),
(8, 14, '', 'b0cf0f394efa891a962c6f6d6612f753f3731078d1f169be358786751cd94e5a', '2026-05-18 17:11:05');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `role` enum('Admin','Member') DEFAULT 'Member',
  `is_verified` tinyint(1) DEFAULT 0,
  `profile_photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `Fail_login` int(11) DEFAULT 0,
  `last_fail_login` datetime DEFAULT NULL,
  `Lock_until` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `password_hash`, `user_name`, `role`, `is_verified`, `profile_photo`, `is_active`, `Fail_login`, `last_fail_login`, `Lock_until`, `created_at`) VALUES
(1, 'chaijs@admin.com', '$2y$10$NR7022labNdx6Vk/e1oIxei2rAFTEew9kwhRfaqz2cLCU7eLwS0qS', 'Jia Sheng Chai', 'Admin', 1, 'user_1_1776183226.png', 1, 0, NULL, NULL, '2026-03-28 14:02:57'),
(2, 'chaijs@gmail.com', '$2y$10$uzshRy3NC1IiLcaeYabATuft01E.xw0g9nxppRxMmP2TLDDvm3XGS', 'lunatics', 'Member', 1, 'user_2_1774678764.webp', 1, 0, NULL, NULL, '2026-03-28 14:08:32'),
(13, 'jonathan1@admin.com', '$2y$10$ZOJXl/t6xKbdU.EHzycbe.F/WVXuvg0YmuhomViKTSv88NEDpYFju', 'Jonathan', 'Admin', 1, NULL, 1, 0, NULL, NULL, '2026-04-16 18:34:28'),
(14, 'lewisham@gmail.com', '$2y$10$D/jz.Jh10/oDtjpAs59a4eIbCo52CS8zWRaQPwSxbrozaU1Ou5nuy', 'Lewis Hamilton', 'Member', 1, 'user_14_1776336967.png', 1, 0, NULL, NULL, '2026-04-16 18:34:56'),
(15, 'john@admin.com', '$2y$10$F0V.hLnbZAytWFSaT2tMQ.bcOY2aX.vXD5.GQpCTs1Wk80ulG4JQi', 'John', 'Admin', 1, NULL, 1, 0, NULL, NULL, '2026-04-17 11:36:33'),
(17, 'wangwang@admin.com', '$2y$10$Nljqr2du8aJY4mqHMd7C9.QBwDHxF1v0OA73.2t6iMeCeHbCXecoO', 'WANG HONG QIAN', 'Admin', 1, 'user_17_1776521371.jpg', 1, 3, NULL, '2026-04-18 17:07:33', '2026-04-18 21:31:20'),
(18, 'maxverstappen@gmail.com', '$2y$10$.7/i2/CjTyaoF1daDY5qOeaC.Ys3qyhk0PndxVIx11zGIfzgDt3Sy', 'Max Verstappen', 'Member', 1, NULL, 1, 0, NULL, NULL, '2026-04-19 17:39:58'),
(20, 'asenna@gmail.com', '$2y$10$hbECqBxuUK8alXCHp3wyz.aCulVJUhJHlyS5retBVKcuG6fwHPgMm', 'Ayrton Senna', 'Member', 1, NULL, 1, 0, NULL, NULL, '2026-04-19 17:48:51');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(4, 14, 6, '2026-04-18 22:53:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `email_verification`
--
ALTER TABLE `email_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `remember_me`
--
ALTER TABLE `remember_me`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wish` (`user_id`,`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `email_verification`
--
ALTER TABLE `email_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `remember_me`
--
ALTER TABLE `remember_me`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_verification`
--
ALTER TABLE `email_verification`
  ADD CONSTRAINT `email_verification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `remember_me`
--
ALTER TABLE `remember_me`
  ADD CONSTRAINT `remember_me_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
