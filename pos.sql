-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 02:18 PM
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
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `created_at`, `receipt_number`) VALUES
(1, 2, 492.00, '2024-10-28 09:47:25', ''),
(2, 2, 492.00, '2024-10-28 09:47:49', ''),
(3, 2, 492.00, '2024-10-28 09:49:10', ''),
(4, 2, 2220.00, '2024-10-28 09:52:36', ''),
(5, 2, 1332.00, '2024-10-28 09:56:24', ''),
(6, 2, 2108.00, '2024-10-28 09:58:10', ''),
(7, 2, 2108.00, '2024-10-28 09:59:27', ''),
(8, 2, 2197.00, '2024-10-28 10:00:36', ''),
(9, 2, 0.00, '2024-10-28 10:00:39', ''),
(10, 2, 1220.00, '2024-10-28 10:03:08', ''),
(11, 2, 976.00, '2024-10-28 10:06:10', ''),
(12, 2, 110.00, '2024-10-28 10:06:48', ''),
(13, 2, 9453.00, '2024-10-28 10:38:03', ''),
(14, 2, 532.00, '2024-10-28 10:38:12', ''),
(15, 2, 44.00, '2024-10-28 10:39:13', ''),
(16, 2, 4328.00, '2024-10-28 11:58:37', ''),
(17, 2, 110.00, '2024-10-28 11:59:10', ''),
(18, 2, 220.00, '2024-10-28 12:08:40', ''),
(19, 2, 88.00, '2024-10-28 12:13:10', ''),
(20, 2, 308.00, '2024-10-28 12:20:15', ''),
(21, 2, 21715.00, '2024-10-28 13:08:36', ''),
(22, 2, 0.00, '2024-10-28 13:08:44', ''),
(23, 2, 22.00, '2024-10-28 13:08:55', ''),
(24, 2, 2220.00, '2024-10-28 13:10:29', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `description`) VALUES
(6, 'เสื้อ', 40.00, 27, 'ราวเสื้อยืด'),
(9, 'เสื้อแขนยาว', 120.00, 19, 'เสื้อแขนยาวงานญี่ปุ่น'),
(10, 'กาเกงขาสั้น', 80.00, 18, 'งานญี่ปุ่นมือสอง'),
(11, 'เสื้อแฟรี่', 80.00, 14, 'งานมือสอง'),
(12, 'หมวก', 40.00, 34, 'งานมือสอง'),
(13, 'เสื้อผ้าร่ม', 150.00, 8, 'งานเกาหลี');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `order_id`, `total_amount`, `created_at`, `date`) VALUES
(1, 10, 1220.00, '2024-10-28 10:03:08', '2024-10-28 18:43:01'),
(2, 11, 976.00, '2024-10-28 10:06:10', '2024-10-28 18:43:01'),
(3, 12, 110.00, '2024-10-28 10:06:48', '2024-10-28 18:43:01'),
(4, 13, 9453.00, '2024-10-28 10:38:03', '2024-10-28 18:43:01'),
(5, 14, 532.00, '2024-10-28 10:38:12', '2024-10-28 18:43:01'),
(6, 15, 44.00, '2024-10-28 10:39:13', '2024-10-28 18:43:01'),
(10, 16, 4328.00, '2024-10-28 11:58:37', '2024-10-28 18:58:37'),
(11, 17, 110.00, '2024-10-28 11:59:10', '2024-10-28 18:59:10'),
(12, 18, 220.00, '2024-10-28 12:08:40', '2024-10-28 19:08:40'),
(13, 19, 88.00, '2024-10-28 12:13:10', '2024-10-28 19:13:10'),
(14, 20, 308.00, '2024-10-28 12:20:15', '2024-10-28 19:20:15'),
(15, 21, 21715.00, '2024-10-28 13:08:36', '2024-10-28 20:08:36'),
(16, 22, 0.00, '2024-10-28 13:08:44', '2024-10-28 20:08:44'),
(17, 23, 22.00, '2024-10-28 13:08:55', '2024-10-28 20:08:55'),
(18, 24, 2220.00, '2024-10-28 13:10:29', '2024-10-28 20:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', '2024-10-28 06:54:48'),
(2, 'staff', '$2y$10$D6hfJm.KptX3S7l3dWHFKeQn.TSftQovpK6F5RO7UNp6.k9HjBvI.', 'staff', '2024-10-28 06:54:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
