-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2025 at 10:46 AM
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
-- Database: `service24/7`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catering`
--

CREATE TABLE `catering` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item1` int(11) NOT NULL DEFAULT 0,
  `item2` int(11) NOT NULL DEFAULT 0,
  `item3` int(11) NOT NULL DEFAULT 0,
  `total_price` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chef`
--

CREATE TABLE `chef` (
  `id` int(11) NOT NULL,
  `chinese` tinyint(4) DEFAULT 0,
  `western` tinyint(4) DEFAULT 0,
  `domestic` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chef`
--

INSERT INTO `chef` (`id`, `chinese`, `western`, `domestic`) VALUES
(100, 1, 0, 1),
(101, 0, 1, 1),
(102, 1, 1, 1),
(103, 1, 1, 0),
(104, 0, 1, 1),
(105, 1, 0, 1),
(106, 0, 1, 1),
(107, 1, 1, 1),
(108, 1, 1, 0),
(109, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cleaning`
--

CREATE TABLE `cleaning` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `cleaning_type` int(11) NOT NULL,
  `space` int(11) NOT NULL,
  `subscription` int(11) NOT NULL,
  `total_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `accept_status` int(11) NOT NULL,
  `notification_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `service_name`, `user_id`, `worker_id`, `time`, `accept_status`, `notification_status`) VALUES
(1, 'Pet Caring', 7, 9, '2025-01-05 06:27:45', 0, 0),
(2, 'Pet Caring', 6, 10, '2025-01-05 06:29:04', 0, 0),
(4, 'Pet Caring', 6, 7, '2025-01-07 04:43:01', 0, 0),
(5, 'Pet Caring', 6, 7, '2025-01-07 05:20:51', 0, 0),
(7, 'Pet Caring', 7, 11, '2025-01-09 16:45:44', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pet_caring`
--

CREATE TABLE `pet_caring` (
  `id` int(11) NOT NULL,
  `pet_type` varchar(50) NOT NULL,
  `place_price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet_caring`
--

INSERT INTO `pet_caring` (`id`, `pet_type`, `place_price`, `start_date`, `end_date`, `pickup_location`, `total_cost`, `order_id`) VALUES
(2, 'Bird', 80.00, '2025-01-05', '2025-01-07', 'rampura', 340.00, 1),
(3, 'Dog & Cat', 100.00, '2025-01-07', '2025-01-08', 'rampura', 300.00, 2),
(4, 'Fish', 70.00, '2025-01-08', '2025-01-09', 'wegg', 250.00, 4),
(5, 'Bird', 80.00, '2025-01-08', '2025-01-16', 'gtfftg6', 1520.00, 5),
(6, 'Dog & Cat', 100.00, '2025-01-10', '2025-01-23', 'rampura', 1400.00, 7);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `service_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `service_name`) VALUES
(0, 'pet caring'),
(1, 'electrician'),
(2, 'catering'),
(3, 'cleaning'),
(4, 'baby sitting');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `fullName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `fullName`, `username`, `email`, `password`) VALUES
(6, 'Arshad Md Adel', 'adel', 'adel@gmail.com', '$2y$10$kVuKYiOX8.epJEkhO/YgZOn8QyXCIjdw/cZTsTn0jy7G/LAVcGafm'),
(7, 'mahi', 'mahi', 'mahi@gmail.com', '$2y$10$hcGDq4pQohBt1Z1RevSAr.mVSApI3GQEQCqweoEZCwbeidW.g30Te');

-- --------------------------------------------------------

--
-- Table structure for table `worker`
--

CREATE TABLE `worker` (
  `id` int(11) NOT NULL,
  `nid` bigint(20) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `phone` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `work_type` int(11) NOT NULL,
  `detail` text NOT NULL,
  `rating` int(11) NOT NULL,
  `num_of_rating` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worker`
--

INSERT INTO `worker` (`id`, `nid`, `fullname`, `phone`, `email`, `password`, `work_type`, `detail`, `rating`, `num_of_rating`, `price`) VALUES
(1, 2132132, 'adel', 2131212, 'a1@gmail.com', 'qwewqeqwwqe', 1, 'qweqweqweqweqw', 4, 5, 100),
(2, 1000000001, 'John Doe', 1234567890, 'johndoe@example.com', 'password123', 1, 'Experienced carpenter with 5 years of expertise.', 5, 10, 200),
(3, 1000000002, 'Jane Smith', 2147483647, 'janesmith@example.com', 'securepass456', 1, 'Professional painter specializing in home interiors.', 4, 12, 150),
(4, 1000000003, 'Michael Johnson', 1231231234, 'michaelj@example.com', 'mike@123', 1, 'Plumber with excellent problem-solving skills.', 5, 7, 170),
(5, 1000000004, 'Emily Davis', 2147483647, 'emilyd@example.com', 'emilypass789', 1, 'Electrician with 10 years of experience.', 4, 30, 120),
(6, 1000000005, 'David Wilson', 2147483647, 'davidw@example.com', 'passw0rd', 1, 'All-round handyman for quick fixes.', 3, 6, 200),
(7, 1000000006, 'Alice Brown', 2147483647, 'aliceb@example.com', 'alice@123', 0, 'Experienced pet groomer with 8 years\' expertise.', 5, 15, 180),
(8, 1000000007, 'Robert Green', 2147483647, 'robertg@example.com', 'secure456', 0, 'Dog trainer specializing in behavior correction.', 4, 20, 150),
(9, 1000000008, 'Sophia Turner', 2147483647, 'sophiat@example.com', 'pass123', 0, 'Cat sitter providing personalized care services.', 5, 10, 130),
(10, 1000000009, 'James White', 2147483647, 'jamesw@example.com', 'james789', 0, 'Veterinary assistant with a passion for pets.', 4, 12, 200),
(11, 1000000010, 'Olivia Johnson', 2147483647, 'oliviaj@example.com', 'olivia2024', 0, 'Pet walker with flexible scheduling options.', 5, 18, 100),
(12, 1000000020, 'Rachel Green', 2147483647, 'rachel.g@example.com', 'cleanpro!', 3, 'Meticulous cleaner with a passion for hygiene.', 5, 30, 120),
(13, 1000000021, 'Monica Geller', 2147483647, 'monica.g@example.com', 'sparkleclean', 3, 'Deep cleaning specialist with years of experience.', 5, 25, 150),
(14, 1000000022, 'Jake Connor', 2147483647, 'jake.c@example.com', 'dirtbuster', 3, 'Efficient cleaner for home and office spaces.', 4, 18, 130),
(15, 1000000023, 'Sophia Carter', 2147483647, 'sophia.c@example.com', 'tidyhome!', 3, 'Professional house cleaner with a friendly demeanor.', 4, 22, 140),
(16, 1000000024, 'Liam Gray', 2147483647, 'liam.g@example.com', 'shineit456', 3, 'Cleaner specializing in eco-friendly solutions.', 5, 20, 160),
(17, 1000000025, 'Olivia Martinez', 2147483647, 'olivia.m@example.com', 'nannycare123', 4, 'Reliable babysitter with CPR certification.', 5, 28, 180),
(18, 1000000026, 'Mason White', 2147483647, 'mason.w@example.com', 'kidfriend456', 4, 'Experienced nanny with excellent references.', 5, 30, 200),
(19, 1000000027, 'Ava Brown', 2147483647, 'ava.b@example.com', 'babylove789', 4, 'Part-time babysitter available for evening care.', 4, 15, 170),
(20, 1000000028, 'Noah Wilson', 1234509876, 'noah.w@example.com', 'trustycare', 4, 'Skilled babysitter with a warm personality.', 4, 20, 190),
(21, 1000000029, 'Isabella Thompson', 2147483647, 'isabella.t@example.com', 'sittersafe', 4, 'Babysitter with flexible hours and great reviews.', 5, 25, 210),
(100, 1001, 'John Doe', 1234567890, 'johndoe@example.com', 'password123', 2, 'Expert in plumbing and repair', 5, 20, 50),
(101, 1002, 'Jane Smith', 1234567891, 'janesmith@example.com', 'password123', 2, 'Professional electrician', 5, 30, 60),
(102, 1003, 'Michael Brown', 1234567892, 'michaelbrown@example.com', 'password123', 2, 'Carpentry and woodwork specialist', 4, 15, 70),
(103, 1004, 'Emily Davis', 1234567893, 'emilydavis@example.com', 'password123', 2, 'House cleaning and organizing', 5, 40, 40),
(104, 1005, 'David Wilson', 1234567894, 'davidwilson@example.com', 'password123', 2, 'Painting and wall repair', 5, 25, 55),
(105, 1006, 'Sarah Johnson', 1234567895, 'sarahjohnson@example.com', 'password123', 2, 'Gardening and landscaping', 5, 35, 65),
(106, 1007, 'Chris Lee', 1234567896, 'chrislee@example.com', 'password123', 2, 'Air conditioning repair', 4, 22, 75),
(107, 1008, 'Anna Taylor', 1234567897, 'annataylor@example.com', 'password123', 2, 'Childcare and babysitting', 5, 28, 45),
(108, 1009, 'Robert Moore', 1234567898, 'robertmoore@example.com', 'password123', 2, 'Furniture assembly and repair', 4, 18, 60),
(109, 1010, 'Laura Scott', 1234567899, 'laurascott@example.com', 'password123', 2, 'Appliance repair', 5, 32, 70);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chef`
--
ALTER TABLE `chef`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- Indexes for table `pet_caring`
--
ALTER TABLE `pet_caring`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`,`email`);

--
-- Indexes for table `worker`
--
ALTER TABLE `worker`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pet_caring`
--
ALTER TABLE `pet_caring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `worker`
--
ALTER TABLE `worker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `worker` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pet_caring`
--
ALTER TABLE `pet_caring`
  ADD CONSTRAINT `pet_caring_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
