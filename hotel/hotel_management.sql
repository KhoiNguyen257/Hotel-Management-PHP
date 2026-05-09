-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 08, 2026 lúc 06:11 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hotel_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `status` enum('Reserved','CheckedIn','CheckedOut','Cancelled') DEFAULT 'Reserved',
  `total_price` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`booking_id`, `customer_id`, `room_id`, `user_id`, `check_in`, `check_out`, `status`, `total_price`, `notes`, `created_at`) VALUES
(1, 1, 6, 6, '2026-05-07', '2026-05-14', 'CheckedOut', 3500000.00, NULL, '2026-05-08 00:25:47'),
(2, 1, 21, 6, '2026-05-07', '2026-05-12', 'CheckedOut', 4000000.00, NULL, '2026-05-08 00:46:20'),
(3, 1, 48, 8, '2026-05-08', '2026-05-14', 'CheckedOut', 9000000.00, NULL, '2026-05-08 22:10:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_services`
--

CREATE TABLE `booking_services` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_services`
--

INSERT INTO `booking_services` (`id`, `booking_id`, `service_id`, `quantity`, `subtotal`, `added_at`) VALUES
(1, 1, 3, 1, 300000.00, '2026-05-08 00:26:19'),
(2, 1, 1, 1, 150000.00, '2026-05-08 00:26:24'),
(3, 3, 3, 1, 300000.00, '2026-05-08 22:10:33'),
(4, 3, 4, 1, 500000.00, '2026-05-08 22:10:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `id_number` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `id_number`, `phone`, `email`, `nationality`, `created_at`) VALUES
(1, 'Anh Tú', '989898989', '923423432424', 'tu@gmail.com', 'Việt Nam', '2026-05-08 00:25:47'),
(2, 'Duy Khang', '12345678', '66666666', 'khang@gmai.com', 'Việt Nam', '2026-05-08 00:46:20'),
(3, 'Alexandre', '99999999', '0908686868', 'alexandre@gmail.com', 'Nga', '2026-05-08 22:10:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `subtotal_room` decimal(10,2) DEFAULT NULL,
  `subtotal_services` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT 10.00,
  `tax_amount` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('Cash','Card','Transfer') DEFAULT NULL,
  `payment_status` enum('Pending','Paid') DEFAULT 'Paid',
  `issued_at` datetime DEFAULT current_timestamp(),
  `issued_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `booking_id`, `subtotal_room`, `subtotal_services`, `tax_rate`, `tax_amount`, `grand_total`, `payment_method`, `payment_status`, `issued_at`, `issued_by`) VALUES
(1, 1, 3500000.00, 450000.00, 0.10, 395000.00, 4345000.00, 'Cash', 'Paid', '2026-05-08 00:26:38', 6),
(2, 2, 4000000.00, 0.00, 0.10, 400000.00, 4400000.00, 'Cash', 'Paid', '2026-05-08 00:46:56', 6),
(3, 3, 9000000.00, 800000.00, 0.10, 980000.00, 10780000.00, 'Cash', 'Paid', '2026-05-08 22:15:54', 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `price_per_night` decimal(10,2) DEFAULT NULL,
  `status` enum('Available','Reserved','Occupied','Cleaning') DEFAULT 'Available',
  `floor` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `type_id`, `price_per_night`, `status`, `floor`, `description`) VALUES
(6, '101', 1, 500000.00, 'Available', 1, NULL),
(7, '102', 1, 500000.00, 'Available', 1, NULL),
(8, '103', 1, 500000.00, 'Available', 1, NULL),
(9, '104', 1, 500000.00, 'Available', 1, NULL),
(10, '105', 1, 500000.00, 'Available', 1, NULL),
(11, '106', 1, 500000.00, 'Available', 1, NULL),
(12, '107', 1, 500000.00, 'Available', 1, NULL),
(13, '108', 1, 500000.00, 'Available', 1, NULL),
(14, '109', 1, 500000.00, 'Available', 1, NULL),
(15, '201', 1, 500000.00, 'Available', 2, NULL),
(16, '202', 1, 500000.00, 'Available', 2, NULL),
(17, '203', 1, 500000.00, 'Available', 2, NULL),
(18, '204', 1, 500000.00, 'Available', 2, NULL),
(19, '205', 1, 500000.00, 'Available', 2, NULL),
(20, '206', 1, 500000.00, 'Available', 2, NULL),
(21, '207', 2, 800000.00, 'Available', 2, NULL),
(22, '208', 2, 800000.00, 'Available', 2, NULL),
(23, '209', 2, 800000.00, 'Available', 2, NULL),
(24, '301', 2, 800000.00, 'Available', 3, NULL),
(25, '302', 2, 800000.00, 'Available', 3, NULL),
(26, '303', 2, 800000.00, 'Available', 3, NULL),
(27, '304', 2, 800000.00, 'Available', 3, NULL),
(28, '305', 2, 800000.00, 'Available', 3, NULL),
(29, '306', 2, 800000.00, 'Available', 3, NULL),
(30, '307', 2, 800000.00, 'Available', 3, NULL),
(31, '308', 2, 800000.00, 'Available', 3, NULL),
(32, '309', 2, 800000.00, 'Available', 3, NULL),
(33, '401', 2, 800000.00, 'Available', 4, NULL),
(34, '402', 2, 800000.00, 'Available', 4, NULL),
(35, '403', 2, 800000.00, 'Available', 4, NULL),
(36, '404', 3, 1500000.00, 'Available', 4, NULL),
(37, '405', 3, 1500000.00, 'Available', 4, NULL),
(38, '406', 3, 1500000.00, 'Available', 4, NULL),
(39, '407', 3, 1500000.00, 'Available', 4, NULL),
(40, '408', 3, 1500000.00, 'Available', 4, NULL),
(41, '409', 3, 1500000.00, 'Available', 4, NULL),
(42, '501', 3, 1500000.00, 'Available', 5, NULL),
(43, '502', 3, 1500000.00, 'Available', 5, NULL),
(44, '503', 3, 1500000.00, 'Available', 5, NULL),
(45, '504', 3, 1500000.00, 'Available', 5, NULL),
(46, '505', 3, 1500000.00, 'Available', 5, NULL),
(47, '506', 3, 1500000.00, 'Available', 5, NULL),
(48, '507', 3, 1500000.00, 'Cleaning', 5, NULL),
(49, '508', 3, 1500000.00, 'Available', 5, NULL),
(50, '509', 3, 1500000.00, 'Available', 5, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_types`
--

CREATE TABLE `room_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `amenities` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `room_types`
--

INSERT INTO `room_types` (`type_id`, `type_name`, `capacity`, `base_price`, `amenities`) VALUES
(1, 'Standard', 2, 500000.00, 'TV, Wifi, Điều hòa'),
(2, 'Deluxe', 2, 800000.00, 'TV, Wifi, Minibar, Ban công'),
(3, 'Suite', 4, 1500000.00, 'TV, Wifi, Phòng khách, Ban công rộng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `price`, `category`, `description`, `is_available`) VALUES
(1, 'Ăn sáng (Buffet)', 150000.00, 'Food', 'Buffet sáng tại nhà hàng', 1),
(2, 'Giặt ủi', 50000.00, 'Laundry', 'Giặt sấy quần áo tính theo kg', 1),
(3, 'Đưa đón sân bay', 300000.00, 'Transport', 'Xe 4 chỗ đưa hoặc đón sân bay', 1),
(4, 'Massage & Spa', 500000.00, 'Spa', 'Liệu trình massage thư giãn 60 phút', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('manager','receptionist','admin') DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `full_name`, `email`, `is_active`, `created_at`) VALUES
(6, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', 'Quản lý Alex', 'alex@gmail.com', 1, '2026-05-07 23:07:16'),
(7, 'tuan', '$2y$10$TvdMQobr/d2bi3rz0pbZNu1oCtmDIEQ23wqMboZR173m5/aqaw/ra', 'receptionist', 'Anh Tuấn ', 'tuan@gmail.com', 1, '2026-05-07 23:08:33'),
(8, 'nguyen', '$2y$10$0eHyFvgZm/kroiG8MAa2TOWiSPuxR7OkhbsEbElBqY5C/omVmjAci', 'manager', 'khôi nguyên', 'nguyen@gmail.com', 1, '2026-05-07 23:09:49'),
(9, 'khang', '$2y$10$Gb4ru9WWJ/cGjVzBqeb4ouxMdz.LRQMLWfAflhIESRiYsxLNM4hLa', 'receptionist', 'Duy Khang', 'khang@gmai.com', 1, '2026-05-07 23:19:54');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `booking_services`
--
ALTER TABLE `booking_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `id_number` (`id_number`);

--
-- Chỉ mục cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `type_id` (`type_id`);

--
-- Chỉ mục cho bảng `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `booking_services`
--
ALTER TABLE `booking_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT cho bảng `room_types`
--
ALTER TABLE `room_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `booking_services`
--
ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Các ràng buộc cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `room_types` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
