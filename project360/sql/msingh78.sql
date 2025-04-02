-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 02, 2025 at 05:30 PM
-- Server version: 10.11.11-MariaDB-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `msingh78`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `full_name`, `email`, `phone`, `role`, `profile_image`, `password`) VALUES
(12345678, 'Mandeep Singh', 'meritmandeep35@gmail.com', '7785945506', 'Super-Admin', 'images/admin_12345678_1741560038.jpg', '$2y$10$Td8IOhyzHZSKwmpcptcdOeprkZdeoEULTZPS2QYTVGTPmWLPW4sUa'),
(87654321, 'Varun Garg', 'varungarg2000@gmail.com', '2505753539', 'admin', 'images/admin_87654321_1741912816.jpg', '$2y$10$34FM.po5PsG5qjUQurL8cOiXyIma3Wo1Gl0l4e1y3x4FcumAdWeli');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(50) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`user_id`, `product_id`, `quantity`) VALUES
(37, 201, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(72, 10078, 401, 1, 199.50),
(73, 10079, 300, 1, 349.50),
(74, 10080, 102, 1, 1100.00),
(75, 10081, 106, 1, 1200.00),
(76, 10082, 102, 1, 550.00),
(77, 10082, 202, 2, 749.50),
(78, 10083, 101, 1, 900.00),
(79, 10084, 403, 1, 299.00),
(80, 10085, 303, 108, 599.00),
(81, 10086, 205, 1, 2099.00),
(82, 10087, 205, 20, 2099.00),
(83, 10088, 205, 250, 2099.00),
(84, 10089, 401, 1, 399.00),
(85, 10090, 400, 1, 299.00),
(86, 10090, 401, 1, 399.00),
(87, 10091, 401, 1, 399.00),
(88, 10092, 401, 1, 399.00),
(89, 10093, 105, 1, 999.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_date` date DEFAULT NULL,
  `delivery_address` text NOT NULL,
  `payment_method` enum('Credit Card','PayPal','Cash on Delivery') NOT NULL,
  `status` enum('pending','shipped','delivered','canceled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `order_date`, `delivery_date`, `delivery_address`, `payment_method`, `status`) VALUES
(10076, 22, 1800.00, '2025-03-23 11:19:43', NULL, '106 1500 Terai Rd, 2, Kelowna, BC', 'Credit Card', 'pending'),
(10077, 22, 1800.00, '2025-03-23 11:32:05', NULL, '106 1500 Terai Rd, 1, Kelowna, BC', 'Credit Card', 'pending'),
(10078, 26, 214.50, '2025-03-23 19:30:42', NULL, '456 sugars ave, 2, Kelowna, BC', 'Credit Card', 'pending'),
(10079, 30, 364.50, '2025-03-23 21:05:57', NULL, '84 woodsend run , Brampton, ON', 'Credit Card', 'pending'),
(10080, 22, 1100.00, '2025-03-23 21:56:39', '2025-03-21', '106 1500 Terai Rd, 2, Kelowna, BC', 'Credit Card', 'delivered'),
(10081, 31, 1200.00, '2025-03-24 02:35:41', NULL, '675 , Kelowna, BC', 'Credit Card', 'pending'),
(10082, 32, 2049.00, '2025-03-24 03:16:10', NULL, '675 Gerstmar Road, Kelowna, BC', 'Credit Card', 'pending'),
(10083, 33, 900.00, '2025-03-24 20:11:53', NULL, '975 academy wY, Kelowna , BC', 'Credit Card', 'pending'),
(10084, 30, 314.00, '2025-03-25 04:06:46', NULL, '33 Tralee Street, Brampton, ON', 'Credit Card', 'pending'),
(10085, 30, 64692.00, '2025-03-25 04:10:38', NULL, '33 Tralee Street, Brampton, ON', 'Credit Card', 'pending'),
(10086, 30, 2099.00, '2025-03-25 04:12:54', NULL, '33 Tralee Street, Brampton, ON', 'Credit Card', 'pending'),
(10087, 30, 41980.00, '2025-03-25 04:14:35', NULL, '33 Tralee Street, Brampton, ON', 'Credit Card', 'pending'),
(10088, 30, 524750.00, '2025-03-25 04:15:58', NULL, '33 Tralee Street, Brampton, ON', 'Credit Card', 'pending'),
(10089, 30, 414.00, '2025-03-25 04:17:33', NULL, '33 Tralee Street, Brampton, ON', 'Credit Card', 'pending'),
(10090, 22, 698.00, '2025-03-28 18:18:36', NULL, '106 1500 Terai Rd, Kelowna, BC', 'Credit Card', 'pending'),
(10091, 22, 414.00, '2025-03-28 18:19:05', NULL, '106 1500 Terai Rd, Kelowna, BC', 'Credit Card', 'pending'),
(10092, 22, 414.00, '2025-03-28 18:19:56', NULL, '106 1500 Terai Rd, Kelowna, BC', 'Credit Card', 'pending'),
(10093, 22, 999.00, '2025-03-28 18:20:47', NULL, '106 1500 Terai Rd, 2, Kelowna, BC', 'Credit Card', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 22, 'ee30a820c7a0027ea7bbad82805c37efc98877a9e1e55a90b7bdc4bba34ac48b', '2025-03-23 06:43:14'),
(2, 22, '6bc7ec7938b8d152e62110124ef354de363f569d6fd71edae0c7d3e9c1446d0c', '2025-03-23 06:46:18'),
(3, 22, 'ee4a9dc4614caa076d7ec625d79ca4f1b9a4e8d81d06806ee02a147061d3f707', '2025-03-23 07:04:45'),
(4, 22, 'e3f04e3c8530502e7d35ce8951f86451950dc4a6a8f7295c2c82a9b4f845d769', '2025-03-23 07:05:39'),
(5, 22, '6c2ff8ab9a5797e3ed0c78eca3ab943812d0d39a0075bb8dc4561a06cdb91f6a', '2025-03-23 07:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL CHECK (`stock` >= 0),
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `category` varchar(100) NOT NULL,
  `images` text DEFAULT NULL,
  `best_seller` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `stock`, `price`, `category`, `images`, `best_seller`) VALUES
(101, 'IPhone 16 Pro Max', 'The iPhone 16 Pro Max features a stunning 6.9-inch Super Retina XDR display, advanced A18 Pro chip, and upgraded 48MP triple-camera system for exceptional photos and videos. With improved battery life, titanium design, and iOS 18 enhancements, it delivers top-tier performance, durability, and innovation for power users.', 20, 1800.00, 'Phones', 'uploads/1742685021_67df435d58331.png,uploads/1742685021_67df435d58600.png,uploads/1742685021_67df435d586c7.png', 0),
(102, 'IPhone 16', '​The iPhone 16 features a 6.1-inch Super Retina XDR OLED display, powered by the A18 chip with a 6-core CPU and 5-core GPU. It introduces a 48MP Fusion camera and a new Camera Control button for enhanced photography. The device offers improved battery life, supports Apple Intelligence for AI capabilities, and includes an Action Button for customizable functions', 20, 1100.00, 'Phones', 'uploads/1742685108_67df43b484833.png,uploads/1742685108_67df43b484970.jpg,uploads/1742685108_67df43b484a1a.jpg', 1),
(103, 'Motorola G85', 'The Motorola G85 is a stylish and affordable smartphone featuring a vibrant 6.5-inch Full HD+ display with a smooth 120Hz refresh rate. Powered by a reliable processor, it offers seamless performance for everyday tasks. Its long-lasting battery, clean Android experience, and capable dual cameras make it great value.', 20, 999.00, 'Phones', 'uploads/1742685201_67df4411f1939.jpg,uploads/1742685201_67df4411f1b9b.jpg,uploads/1742685201_67df4411f1e09.jpg', 0),
(104, 'Motorola Edge 50 Pro', 'The Motorola Edge 50 Pro is a premium smartphone featuring a stunning pOLED display, blazing-fast performance, and a sleek, curved design. It boasts a powerful triple camera system, 125W fast charging, and a 4,500mAh battery. Ideal for photography, gaming, and all-day productivity with clean Android experience.', 20, 1099.00, 'Phones', 'uploads/1742685278_67df445e7793b.png,uploads/1742685278_67df445e77c77.png,uploads/1742685278_67df445e77e84.png', 0),
(105, 'OnePlus 11', 'The OnePlus 11 is a premium smartphone that combines sleek design with powerful performance. Featuring a stunning AMOLED display, Snapdragon 8 Gen 2 processor, and Hasselblad-tuned triple camera system, it delivers exceptional speed and image quality. Its long-lasting battery and fast charging make it perfect for everyday use.', 20, 999.00, 'Phones', 'uploads/1742685350_67df44a622dee.png,uploads/1742685350_67df44a622fd4.png,uploads/1742685350_67df44a6230c9.png', 1),
(106, 'Samsung S24 Ultra', 'The Samsung Galaxy S24 Ultra is a premium flagship smartphone featuring a 200MP main camera, Snapdragon 8 Gen 3 processor, and a stunning 6.8\\\" QHD+ AMOLED display. With built-in S Pen, advanced AI features, and up to 1TB storage, it delivers powerful performance and professional-grade photography.', 20, 1200.00, 'Phones', 'uploads/1742685421_67df44edaff5b.jpg,uploads/1742685421_67df44edb00c0.jpg,uploads/1742685421_67df44edb01ad.jpg', 0),
(107, 'Samsung Fold 6', 'The Samsung Galaxy Z Fold 6 is a cutting-edge foldable smartphone featuring a sleek design, powerful performance, and enhanced multitasking. With its expansive fold-out display, improved durability, and S Pen support, it seamlessly blends smartphone and tablet functionality, making it perfect for productivity, creativity, and immersive media experiences on the go.', 20, 1100.00, 'Phones', 'uploads/1742685475_67df452314e9a.jpg,uploads/1742685475_67df45231501e.jpg,uploads/1742685475_67df452318d26.jpg', 0),
(108, 'Sony Xperia XZ 1', 'The Sony Xperia XZ1 is a sleek and durable smartphone featuring a 5.2-inch Full HD HDR display, Snapdragon 835 processor, and 4GB RAM. It boasts a 19MP Motion Eye camera with super slow motion and 3D scanning. Running Android, it offers premium design, water resistance, and smooth performance.', 20, 899.00, 'Phones', 'uploads/1742685546_67df456a9c610.jpg,uploads/1742685546_67df456a9c735.jpg,uploads/1742685546_67df456a9c81b.jpg', 0),
(109, 'Xiaomi 14 Ultra', 'The Xiaomi 14 Ultra is a flagship smartphone featuring a Leica-tuned quad-camera system, Snapdragon 8 Gen 3 processor, and a stunning AMOLED display. It offers professional-grade photography, fast performance, and premium build quality. With long battery life and ultra-fast charging, it’s designed for power users and photography enthusiasts alike.', 20, 799.00, 'Phones', 'uploads/1742685627_67df45bba20d4.jpg,uploads/1742685627_67df45bba221c.jpg,uploads/1742685627_67df45bba22e5.jpg', 0),
(110, 'Google Pixel 9', 'The Google Pixel 9 is the next-generation flagship smartphone from Google, offering cutting-edge AI features, an upgraded Tensor chipset, and a refined design. With its advanced camera system, vibrant OLED display, and seamless Android experience, the Pixel 9 delivers top-tier performance, security, and intelligent user personalization like never before.', 20, 1099.00, 'Phones', 'uploads/1742686076_67df477cbd955.jpg,uploads/1742686076_67df477cbda8b.jpg,uploads/1742686076_67df477cbdb41.jpg', 0),
(200, 'ASUS Zenbook S16', 'The ASUS Zenbook S16 is a sleek and powerful ultrabook designed for productivity and portability. With a 16-inch high-resolution display, it offers vibrant visuals and wide viewing angles. Powered by the latest Intel processors, it features long battery life, fast charging, and a lightweight design, making it ideal for professionals on the go.', 20, 1899.00, 'Computers', 'uploads/1742722141_67dfd45dead74.jpg,uploads/1742722141_67dfd45deaf12.jpg,uploads/1742722141_67dfd45deb01e.jpg', 0),
(201, 'Dell XPS', 'The Dell XPS series offers premium laptops known for their sleek design, high-performance capabilities, and stunning displays. With options featuring Intel processors, ultra-sharp 4K or Full HD screens, and long battery life, the XPS delivers power and style. Perfect for professionals, creatives, and everyday users seeking reliability and portability.', 20, 1799.00, 'Computers', 'uploads/1742722206_67dfd49e61972.jpg,uploads/1742722206_67dfd49e61b0f.jpg,uploads/1742722206_67dfd49e61cc0.jpg', 0),
(202, 'HP Omen 14', 'The HP Omen 14 is a gaming laptop designed for high-performance gaming and multitasking. Featuring a 14-inch Full HD display with a high refresh rate, it ensures smooth gameplay. Powered by Intel or AMD processors, dedicated graphics, and customizable cooling, the Omen 14 offers an immersive gaming experience in a compact form.', 20, 1499.00, 'Computers', 'uploads/1742722254_67dfd4ceed332.jpg,uploads/1742722254_67dfd4ceed495.jpg,uploads/1742722254_67dfd4ceed58b.jpg', 0),
(203, 'Lenovo Slim 7i', 'The Lenovo Slim 7i is a premium ultrabook combining powerful performance with a sleek, lightweight design. Featuring Intel\\\\\\\'s latest processors, a vibrant 14-inch display, and long battery life, it’s perfect for professionals and students. With its fast charging and slim profile, it delivers exceptional portability without compromising performance.', 20, 1399.00, 'Computers', 'uploads/1742722526_67dfd5de36037.jpg,uploads/1742722526_67dfd5de36252.jpg,uploads/1742722526_67dfd5de363fa.jpg', 0),
(204, 'HP Stream 14', 'The HP Stream 14 is an affordable and lightweight laptop designed for everyday computing tasks. Featuring a 14-inch HD display, it offers reliable performance for web browsing, word processing, and streaming. With cloud-based storage and long battery life, it\\\'s a great option for students or casual users seeking a budget-friendly laptop.', 20, 1599.00, 'Computers', 'uploads/1742722567_67dfd6074b5c4.jpg,uploads/1742722567_67dfd6074b760.jpg,uploads/1742722567_67dfd6074b998.jpg', 0),
(205, 'Macbook Air 15', 'The MacBook Air 15 is a lightweight and powerful laptop from Apple, featuring a 15-inch Retina display with True Tone for vibrant visuals. Powered by the Apple M2 chip, it delivers fast performance and long battery life. Its sleek, fanless design makes it ideal for productivity, portability, and everyday tasks.', 20, 2099.00, 'Computers', 'uploads/1742722633_67dfd6492ea4f.jpg,uploads/1742722633_67dfd6492ebf5.jpg,uploads/1742722633_67dfd6492ed28.jpg', 0),
(206, 'Microsoft Surface Pro', 'The Microsoft Surface Pro is a versatile 2-in-1 device that functions as both a laptop and a tablet. Featuring a detachable keyboard and a high-resolution touchscreen, it offers flexible productivity. Powered by Intel processors, it delivers excellent performance for work, media consumption, and creativity, with long battery life for on-the-go use.', 20, 1999.00, 'Computers', 'uploads/1742722702_67dfd68e73260.jpg,uploads/1742722702_67dfd68e73430.jpg,uploads/1742722702_67dfd68e7354b.jpg', 0),
(300, 'Apple Watch SE', 'The Apple Watch SE is an affordable yet feature-rich smartwatch that offers essential health and fitness tracking, including heart rate monitoring, sleep tracking, and fitness goals. It also provides notifications, calls, and text messages directly on your wrist. With a sleek design and long battery life, it\\\\\\\'s perfect for everyday wear and activity.', 20, 699.00, 'SmartWatch', 'uploads/1742722862_67dfd72ef357e.jpg,uploads/1742722863_67dfd72f00251.jpg,uploads/1742722863_67dfd72f004d3.jpg', 0),
(301, 'Apple Ultra 2 ', 'The Apple Ultra 2 is an advanced smartwatch designed for high-performance and fitness enthusiasts. Featuring a larger, brighter display and enhanced health sensors, it offers real-time monitoring of heart rate, blood oxygen, and activity. With robust GPS tracking, water resistance, and long battery life, it\\\\\\\'s perfect for athletes and outdoor adventurers.', 20, 799.00, 'SmartWatch', 'uploads/1742722799_67dfd6efb7be3.jpg,uploads/1742722799_67dfd6efb7d90.jpg,uploads/1742722799_67dfd6efb7f3e.jpg', 0),
(302, 'Garmin Smartwatch Kids', 'The Garmin Smartwatch for Kids is a kid-friendly wearable designed for safety and fun. Featuring GPS tracking, activity monitoring, and customizable watch faces, it helps parents stay connected with their children. ', 20, 499.00, 'SmartWatch', 'uploads/1742722982_67dfd7a6ece6e.jpg,uploads/1742722982_67dfd7a6ecff5.jpg,uploads/1742722982_67dfd7a6ed0f8.jpg', 0),
(303, 'Gard Pro Ultra 2', 'The Gard Pro Ultra 2 is a high-performance smart wearable designed for outdoor enthusiasts and athletes. It features advanced health monitoring capabilities such as heart rate, blood oxygen levels, and sleep tracking. ', 20, 599.00, 'SmartWatch', 'uploads/1742722920_67dfd76836c8e.jpg,uploads/1742722920_67dfd76836e8c.jpg,uploads/1742722920_67dfd76836fc9.jpg', 0),
(304, 'Google Fitbit Charge 6', 'The Google Fitbit Charge 6 is a fitness tracker designed to monitor health and activity with advanced features. It offers continuous heart rate monitoring, GPS tracking, sleep analysis, and stress management tools. With improved battery life, a sleek design, and smart notifications, it\\\'s perfect for those looking to track their fitness progress and overall wellness.', 20, 699.00, 'SmartWatch', 'uploads/1742723040_67dfd7e060c8f.jpg,uploads/1742723040_67dfd7e060e51.jpg,uploads/1742723040_67dfd7e060ff3.jpg', 0),
(305, 'Google Pixel Watch 2', 'The Google Pixel Watch 2 is a stylish and functional smartwatch that integrates seamlessly with Google services. Featuring fitness and health tracking, including heart rate monitoring, GPS, and sleep analysis, it offers a premium experience. With a sleek design, long battery life, and integration with Google Assistant, it\\\'s perfect for Android users.', 20, 649.00, 'SmartWatch', 'uploads/1742723091_67dfd813ac823.jpg,uploads/1742723091_67dfd813ac9a4.jpg,uploads/1742723091_67dfd813acaf8.jpg', 0),
(306, 'Huawei Band 8', 'The Huawei Band 8 is a sleek and lightweight fitness tracker designed for daily health monitoring. It features heart rate tracking, sleep analysis, step counting, and SpO2 monitoring. With a vibrant AMOLED display, long battery life, and water resistance, it’s perfect for those looking to stay active and track their wellness on the go.', 20, 449.00, 'SmartWatch', 'uploads/1742723153_67dfd851af623.jpg,uploads/1742723153_67dfd851af792.jpg,uploads/1742723153_67dfd851af92b.jpg', 0),
(307, 'Lige Military Smartwatch', 'The Lige Military Smartwatch is a rugged, durable smartwatch designed for outdoor enthusiasts and athletes. Featuring a strong, military-grade build, it offers fitness tracking, heart rate monitoring, GPS navigation, and waterproof capabilities. With a long-lasting battery and customizable watch faces, it\\\'s perfect for those who need a reliable device for adventure and daily activities.', 20, 899.00, 'SmartWatch', 'uploads/1742723230_67dfd89e4d8d2.jpg,uploads/1742723230_67dfd89e4da31.jpg,uploads/1742723230_67dfd89e4dba6.jpg', 0),
(308, 'Samsung Galaxy Watch 2', 'The Samsung Galaxy Watch 2 is a versatile smartwatch offering advanced health and fitness tracking features, including heart rate monitoring, sleep tracking, and GPS. It boasts a sleek design, long battery life, and seamless integration with Android devices.', 20, 649.00, 'SmartWatch', 'uploads/1742757774_67e05f8e66595.jpg,uploads/1742757774_67e05f8e666ff.jpg,uploads/1742757774_67e05f8e667d2.jpg', 0),
(309, 'Samsung Gear SM', 'The Samsung Gear SM is a smartwatch designed for fitness tracking and convenience. Featuring heart rate monitoring, step counting, and sleep tracking, it helps users stay on top of their health. With a durable design, customizable watch faces, and integration with Android devices, it offers a balance of functionality and style.', 20, 599.00, 'SmartWatch', 'uploads/1742723338_67dfd90a95402.jpg,uploads/1742723338_67dfd90a9559c.jpg,uploads/1742723338_67dfd90a95730.jpg', 0),
(400, 'Boat Earplugs', 'Boat Earplugs are high-quality, noise-canceling earplugs designed for comfort and protection. Ideal for both casual and active use, they provide effective hearing protection in noisy environments. ', 20, 299.00, 'HeadPhones', 'uploads/1742757730_67e05f62d8b8d.jpg,uploads/1742757730_67e05f62d8d23.jpg,uploads/1742757730_67e05f62d8db5.jpg', 0),
(401, 'Beats Solo 4', 'The Beats Solo 4 is a pair of over-ear wireless headphones designed for premium sound quality and comfort. Featuring the signature Beats bass, long-lasting battery life, and a lightweight, foldable design, they provide an immersive listening experience. With built-in Apple H1 chip, they offer seamless connectivity with Apple devices and easy controls.', 20, 399.00, 'HeadPhones', 'uploads/1742723482_67dfd99a5175a.jpg,uploads/1742723482_67dfd99a51905.jpg,uploads/1742723482_67dfd99a51a1d.jpg', 1),
(402, 'JBL Tune', 'The JBL Tune series offers a range of wireless headphones and earbuds known for their rich sound and comfortable fit. Equipped with powerful bass, long battery life, and Bluetooth connectivity, they provide an immersive listening experience. Lightweight and portable, they are perfect for music lovers on the go.', 20, 499.00, 'HeadPhones', 'uploads/1742723552_67dfd9e0440f7.jpg,uploads/1742723552_67dfd9e044288.jpg,uploads/1742723552_67dfd9e04442d.jpg', 0),
(403, 'Samsung Galaxy Buds', 'Samsung Galaxy Buds are wireless earbuds designed for high-quality audio and comfort. With excellent sound performance, long battery life, and a secure fit, they offer an immersive listening experience. ', 20, 299.00, 'HeadPhones', 'uploads/1742723619_67dfda2380308.jpeg,uploads/1742723619_67dfda238052c.jpeg,uploads/1742723619_67dfda23806db.jpeg', 0),
(404, 'Apple Airpod Pro', 'The Apple AirPods Pro are premium wireless earbuds that deliver exceptional sound quality with active noise cancellation. Featuring a customizable fit with silicone tips, they offer a secure and comfortable experience. With transparency mode, spatial audio, and seamless integration with Apple devices, they are perfect for immersive listening and hands-free calls.', 20, 399.00, 'HeadPhones', 'uploads/1742723673_67dfda59af845.jpg,uploads/1742723673_67dfda59af9d2.jpg,uploads/1742723673_67dfda59afd53.jpg', 0),
(405, 'Apple Airpod Max', 'The Apple AirPods Max are high-end over-ear headphones designed for superior sound quality and comfort. With active noise cancellation, spatial audio, and adaptive EQ, they provide an immersive listening experience. Featuring a premium build, long battery life, and seamless integration with Apple devices, they offer both luxury and functionality for audiophiles.', 20, 699.00, 'HeadPhones', 'uploads/1742723734_67dfda965fd74.jpg,uploads/1742723734_67dfda965ff3d.jpg,uploads/1742723734_67dfda9660107.jpg', 0),
(500, 'Apple Vision Pro', 'The Apple Vision Pro is a revolutionary mixed-reality headset that blends digital content with the physical world. With a high-resolution display, spatial audio, and advanced sensors, it offers immersive experiences for gaming, entertainment, and productivity. Its sleek design, powerful performance, and intuitive controls redefine how we interact with technology.', 20, 1999.00, 'Gaming', 'uploads/1742721782_67dfd2f689181.jpg,uploads/1742721782_67dfd2f6895cc.jpg,uploads/1742721782_67dfd2f6897be.jpg', 0),
(501, 'PS5 ', 'The PlayStation 5 (PS5) is a next-gen gaming console by Sony, offering ultra-fast load times, stunning 4K graphics, and immersive gameplay. Powered by the custom AMD processor, it supports ray tracing, high frame rates, and a redesigned controller with adaptive triggers for a more tactile gaming experience.', 20, 999.00, 'Gaming', 'uploads/1742721877_67dfd3553390f.jpg,uploads/1742721877_67dfd35533aa3.jpg,uploads/1742721877_67dfd35533bc0.jpg', 1),
(502, 'Moga Pro Controller', 'The MOGA Pro Controller is a Bluetooth-enabled gaming controller designed for mobile devices. Offering ergonomic design, responsive buttons, and analog sticks, it provides a console-like gaming experience on smartphones and tablets. Compatible with both Android and iOS, it\\\'s perfect for enhancing mobile gaming with precise controls and comfort.', 20, 349.00, 'Gaming', 'uploads/1742721937_67dfd391d8c1c.jpg,uploads/1742721937_67dfd391d8e08.jpg,uploads/1742721937_67dfd391dd7c7.jpg', 0),
(503, 'Logitech Raching Wheel', 'The Logitech Racing Wheel is a high-performance gaming wheel designed for racing simulators. Featuring precise force feedback, responsive pedals, and a durable design, it offers an immersive racing experience. Compatible with PC and consoles, it provides realistic control and enhanced immersion for racing enthusiasts and gamers looking to improve their skills.', 20, 799.00, 'Gaming', 'uploads/1742721993_67dfd3c97631c.jpg,uploads/1742721993_67dfd3c976514.jpg,uploads/1742721993_67dfd3c976661.jpg', 0),
(504, 'HL Direct Gaming Chair', 'The HL Direct Gaming Chair is a comfortable and ergonomic chair designed for long gaming sessions. Featuring adjustable armrests, lumbar support, and a reclining function, it ensures optimal posture and support. Its high-quality materials, sturdy frame, and sleek design provide both durability and style for avid gamers and professionals.', 20, 899.00, 'Gaming', 'uploads/1742722055_67dfd4073de78.jpg,uploads/1742722055_67dfd4073e026.jpg,uploads/1742722055_67dfd4073e1bf.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(13, 102, 22, 5, 'Great products and MV provides a fantastic shopping experience!', '2025-03-23 18:08:09'),
(15, 401, 26, 5, 'Great sound quality and reasonable price', '2025-03-23 19:29:27'),
(16, 401, 22, 5, 'Great product', '2025-03-23 22:02:30'),
(17, 400, 22, 5, 'Great', '2025-03-25 22:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`, `profile_image`, `first_name`, `last_name`, `status`) VALUES
(22, 'meritmandeep35', 'meritmandeep35@gmail.com', '$2y$10$51sWUNAYWKYxUEjWzwJsu.w7w8Isd///rDfaqjdx23dknqBAOuoHW', 'uploads/admin_12345678_1741560038.jpg', 'Mandeep', 'Singh', 'active'),
(24, 'ron123', 'ron123@gmail.com', '$2y$10$kG6Yh.ViamPh0GABL..GN.QirIFdcBbbgU3gCWcAmgMlskUkKYdBO', 'uploads/1741921968_admin_87654321_1741912816.jpg', 'Ron', 'Olsvik', 'active'),
(25, 'rupinderk', 'kmsgroup1720@gmail.com', '$2y$10$a6duPbjav9zk2ZmIrZ2XduQWHxJwZcxLuXKh4L5drF5fkPP8mAJCS', 'uploads/1742070969_admin_12345678_1741560038.jpg', 'Rupinder', 'Kaur', 'active'),
(26, 'RimpleC', 'rupinder.man345@gmail.com', '$2y$10$tmBx6JStftUeSLXnsIs7qegu5t05YkjSwlDEpxXy8PJclAHO2l0ES', 'uploads/IMG_2451.jpeg', 'Rimple', 'Chahal', 'active'),
(27, 'sansidhu60001', 'sansidhu60001@gmail.com', '$2y$10$kcrn4Q0nOZy8AA6qA6cuAehyl.i/R3Cg918SDIg..B7kNE5KRM3L2', 'uploads/1742758801_image.jpg', 'Sanpreet', 'Kaur', 'active'),
(28, 'Gurbirchahal16', 'gurbir.chahal132@gmail.com', '$2y$10$Ik3WCFnG09E49zQz1R5u8evdQk6cUufL27Sfmt7wePGFtmeLnda8a', 'uploads/1742759054_image.jpg', 'Gurbir', 'Chahal', 'active'),
(29, 'Sewakchahal', 'sansidhu0456@icloud.com', '$2y$10$fAet9GvO.3sEMM.mSxa5r.htafBKtE1K7r.C1kQzk9kv.PAV3SUyi', 'uploads/1742759147_image.jpg', 'Sewak', 'Chahal', 'active'),
(30, 'Vinay', 'vinayvashishat729@gmail.com', '$2y$10$KYBFPMfybstRw9xjfuY3CeUcg6Qylr1sERw7m0M8rDzc5OjCALs/G', 'uploads/1742763827_1001061991.jpg', 'Vinay', 'Kumar', 'active'),
(31, 'vgarg28', 'gargvarun2000@gmail.com', '$2y$10$0JHZ0o/AD4mJ1rSA32TwUOZzM.j9KxFmuX5isC0LlSfmV52Bf6kSK', 'uploads/1742778999_admin_87654321_1741912816.jpg', 'Varun', 'Garg', NULL),
(32, 'vgarg2025', 'varunokanagan@gmail.com', '$2y$10$.FeZbIS5kksD.xDeWMzYQ.ezRT6hxZmWh5E3GCGzr0ze82owrTzDK', 'uploads/1742786083_IMG_3187.png', 'Varun', 'Garg', NULL),
(33, 'Bee', 'sarab4space@gmail.com', '$2y$10$FaGuKCqphKr2OOqBynd8uuE2bhQTaPr9Bu6n9rqfn2sRdTU9JLhEy', 'uploads/1742847049_IMG_0629.jpeg', 'Sarab', 'Aulakh', NULL),
(34, 'Akashb1235', 'akashb1235@gmail.com', '$2y$10$8IXGNTSJ5lVgx771cqVp9.BQuolRn5g9Jgcmha74sTQg4Eh4CVHUa', 'uploads/1742854561_image.jpg', 'Akash', 'Bashal', NULL),
(35, 'nsema04', 'nsema04@gmail.com', '$2y$10$h5eO4RCf5EoawJziQmNl3OUz0hkTCu5hjQr1IhN3pEUR3G0cl3Mw6', 'uploads/1742929693_IMG_3988.jpeg', 'Noah', 'Semashkewich', NULL),
(36, 'meritmandeep3535', 'ajaibbhangu84271@gmail.com', '$2y$10$tlilNnymg79MfmueZ6x70uV48IsCKnDfwVuAJF.3Ja1nyjcBPOEce', 'uploads/1743183112_Screenshot 2025-03-23 223945.png', 'Ajaib', 'Singh', NULL),
(37, 'testuser', 'testuser@gmail.com', '$2y$10$f.en91qQllFeTZbX5uy2ne5kG/ppZiZLavARXF1KKS7L7Ez6jvj92', 'uploads/1743472113_banner-2.jpg', 'Test', 'User', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`user_id`, `product_id`) VALUES
(22, 102),
(22, 105),
(26, 401),
(32, 102),
(37, 201);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_reviews_product` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_username` (`user_name`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10094;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
