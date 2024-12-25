-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2024 at 02:55 PM
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
-- Database: `agriculture_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(20) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_password`) VALUES
(1, 'admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `c_id` int(11) NOT NULL,
  `c_name` varchar(100) NOT NULL,
  `c_mobile` varchar(100) NOT NULL,
  `c_email` varchar(100) NOT NULL,
  `c_address` varchar(500) NOT NULL,
  `c_message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`c_id`, `c_name`, `c_mobile`, `c_email`, `c_address`, `c_message`) VALUES
(6, 'vaishnavid', '9878749887', 'vaishnavi.19cs109@sode-edu.in', 'udupi', 'its working'),
(8, 'sfs', '7676445273', 'naik97059@gmail.com', 'Mangalore Karnataka', 'dfbvdfb'),
(9, 'root', '8765434565', 'fvsvsd@gjdjn.com', 'Mangalore Karnataka', 'fwfwffwf');

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `Crop_id` int(11) NOT NULL,
  `Crop_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`Crop_id`, `Crop_name`, `created_at`) VALUES
(1, 'tomato', '2024-12-13 12:03:09'),
(2, 'urad', '2024-12-13 12:30:11'),
(3, 'potato', '2024-12-13 13:40:22'),
(4, 'jowar', '2024-12-13 13:53:09'),
(5, 'cotton', '2024-12-13 13:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `custlogin`
--

CREATE TABLE `custlogin` (
  `cust_id` int(20) NOT NULL,
  `cust_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `pincode` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `otp` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custlogin`
--

INSERT INTO `custlogin` (`cust_id`, `cust_name`, `password`, `email`, `address`, `city`, `pincode`, `state`, `phone_no`, `otp`) VALUES
(1, 'customer', 'password', 'agricultureportal01@gmail.com', 'Udupi, Bantakal', 'Mysore', '576210', 'Karnataka', '9878787898', 73647);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `mobile_no` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_replies`
--

CREATE TABLE `discussion_replies` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `farmer_email` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_replies`
--

INSERT INTO `discussion_replies` (`id`, `discussion_id`, `farmer_email`, `content`, `created_at`) VALUES
(1, 1, 'agricultureportal01@gmail.com', 'mlmfd', '2024-12-02 16:22:45'),
(2, 1, 'agricultureportal01@gmail.com', 'mmmm', '2024-12-02 16:23:46'),
(3, 1, 'agricultureportal01@gmail.com', 'mmmm', '2024-12-02 16:26:15'),
(4, 2, 'agricultureportal01@gmail.com', 'fbfdbdf', '2024-12-02 16:26:54'),
(5, 2, 'agricultureportal01@gmail.com', 'KFGMFK', '2024-12-04 04:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `disease_details`
--

CREATE TABLE `disease_details` (
  `id` int(11) NOT NULL,
  `disease_id` int(11) DEFAULT NULL,
  `detail_type` enum('precaution','remedy','treatment','additional_info') NOT NULL,
  `detail_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `DistCode` int(11) NOT NULL,
  `StCode` int(11) DEFAULT NULL,
  `DistrictName` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`DistCode`, `StCode`, `DistrictName`) VALUES
(1, 1, 'North and Middle Andama'),
(2, 1, 'South Andama'),
(3, 1, 'Nicobar'),
(4, 2, 'Anantapur'),
(5, 2, 'Chittoor'),
(6, 2, 'East Godavari'),
(7, 2, 'Guntur'),
(8, 2, 'Krishna'),
(9, 2, 'Kurnool'),
(10, 2, 'Prakasam'),
(11, 2, 'Srikakulam'),
(12, 2, 'Sri Potti Sri Ramulu Nellore'),
(13, 2, 'Vishakhapatnam'),
(14, 2, 'Vizianagaram'),
(15, 2, 'West Godavari'),
(16, 2, 'Cudappah'),
(17, 3, 'Anjaw'),
(18, 3, 'Changlang'),
(19, 3, 'East Siang'),
(20, 3, 'East Kameng'),
(21, 3, 'Kurung Kumey'),
(22, 3, 'Lohit'),
(23, 3, 'Lower Dibang Valley'),
(24, 3, 'Lower Subansiri'),
(25, 3, 'Papum Pare'),
(26, 3, 'Tawang'),
(27, 3, 'Tirap'),
(28, 3, 'Dibang Valley'),
(29, 3, 'Upper Siang'),
(30, 3, 'Upper Subansiri'),
(31, 3, 'West Kameng'),
(32, 3, 'West Siang'),
(33, 4, 'Baksa'),
(34, 4, 'Barpeta'),
(35, 4, 'Bongaigao'),
(36, 4, 'Cachar'),
(37, 4, 'Chirang'),
(38, 4, 'Darrang'),
(39, 4, 'Dhemaji'),
(40, 4, 'Dima Hasao'),
(41, 4, 'Dhubri'),
(42, 4, 'Dibrugarh'),
(43, 4, 'Goalpara'),
(44, 4, 'Golaghat'),
(45, 4, 'Hailakandi'),
(46, 4, 'Jorhat'),
(47, 4, 'Kamrup'),
(48, 4, 'Kamrup Metropolita'),
(49, 4, 'Karbi Anglong'),
(50, 4, 'Karimganj'),
(51, 4, 'Kokrajhar'),
(52, 4, 'Lakhimpur'),
(53, 4, 'Morigao'),
(54, 4, 'Nagao'),
(55, 4, 'Nalbari'),
(56, 4, 'Sivasagar'),
(57, 4, 'Sonitpur'),
(58, 4, 'Tinsukia'),
(59, 4, 'Udalguri'),
(60, 5, 'Araria'),
(61, 5, 'Arwal'),
(62, 5, 'Aurangabad'),
(63, 5, 'Banka'),
(64, 5, 'Begusarai'),
(65, 5, 'Bhagalpur'),
(66, 5, 'Bhojpur'),
(67, 5, 'Buxar'),
(68, 5, 'Darbhanga'),
(69, 5, 'East Champara'),
(70, 5, 'Gaya'),
(71, 5, 'Gopalganj'),
(72, 5, 'Jamui'),
(73, 5, 'Jehanabad'),
(74, 5, 'Kaimur'),
(75, 5, 'Katihar'),
(76, 5, 'Khagaria'),
(77, 5, 'Kishanganj'),
(78, 5, 'Lakhisarai'),
(79, 5, 'Madhepura'),
(80, 5, 'Madhubani'),
(81, 5, 'Munger'),
(82, 5, 'Muzaffarpur'),
(83, 5, 'Nalanda'),
(84, 5, 'Nawada'),
(85, 5, 'Patna'),
(86, 5, 'Purnia'),
(87, 5, 'Rohtas'),
(88, 5, 'Saharsa'),
(89, 5, 'Samastipur'),
(90, 5, 'Sara'),
(91, 5, 'Sheikhpura'),
(92, 5, 'Sheohar'),
(93, 5, 'Sitamarhi'),
(94, 5, 'Siwa'),
(95, 5, 'Supaul'),
(96, 5, 'Vaishali'),
(97, 5, 'West Champara'),
(98, 6, 'Chandigarh'),
(99, 7, 'Bastar'),
(100, 7, 'Bijapur'),
(101, 7, 'Bilaspur'),
(102, 7, 'Dantewada'),
(103, 7, 'Dhamtari'),
(104, 7, 'Durg'),
(105, 7, 'Jashpur'),
(106, 7, 'Janjgir-Champa'),
(107, 7, 'Korba'),
(108, 7, 'Koriya'),
(109, 7, 'Kanker'),
(110, 7, 'Kabirdham (formerly Kawardha);'),
(111, 7, 'Mahasamund'),
(112, 7, 'Narayanpur'),
(113, 7, 'Raigarh'),
(114, 7, 'Rajnandgao'),
(115, 7, 'Raipur'),
(116, 7, 'Surajpur'),
(117, 8, 'Dadra and Nagar Haveli'),
(118, 9, 'Dama'),
(119, 9, 'Diu'),
(120, 10, 'Central Delhi'),
(121, 10, 'East Delhi'),
(122, 10, 'New Delhi'),
(123, 10, 'North Delhi'),
(124, 10, 'North East Delhi'),
(125, 10, 'North West Delhi'),
(126, 10, 'South Delhi'),
(127, 10, 'South West Delhi'),
(128, 10, 'West Delhi'),
(129, 11, 'North Goa'),
(130, 11, 'South Goa'),
(131, 12, 'Ahmedabad'),
(132, 12, 'Amreli'),
(133, 12, 'Anand'),
(134, 12, 'Aravalli'),
(135, 12, 'Banaskantha'),
(136, 12, 'Bharuch'),
(137, 12, 'Bhavnagar'),
(138, 12, 'Dahod'),
(139, 12, 'Dang'),
(140, 12, 'Gandhinagar'),
(141, 12, 'Jamnagar'),
(142, 12, 'Junagadh'),
(143, 12, 'Kutch'),
(144, 12, 'Kheda'),
(145, 12, 'Mehsana'),
(146, 12, 'Narmada'),
(147, 12, 'Navsari'),
(148, 12, 'Pata'),
(149, 12, 'Panchmahal'),
(150, 12, 'Porbandar'),
(151, 12, 'Rajkot'),
(152, 12, 'Sabarkantha'),
(153, 12, 'Surendranagar'),
(154, 12, 'Surat'),
(155, 12, 'Tapi'),
(156, 12, 'Vadodara'),
(157, 12, 'Valsad'),
(158, 13, 'Ambala'),
(159, 13, 'Bhiwani'),
(160, 13, 'Faridabad'),
(161, 13, 'Fatehabad'),
(162, 13, 'Gurgao'),
(163, 13, 'Hissar'),
(164, 13, 'Jhajjar'),
(165, 13, 'Jind'),
(166, 13, 'Karnal'),
(167, 13, 'Kaithal'),
(168, 13, 'Kurukshetra'),
(169, 13, 'Mahendragarh'),
(170, 13, 'Mewat'),
(171, 13, 'Palwal'),
(172, 13, 'Panchkula'),
(173, 13, 'Panipat'),
(174, 13, 'Rewari'),
(175, 13, 'Rohtak'),
(176, 13, 'Sirsa'),
(177, 13, 'Sonipat'),
(178, 13, 'Yamuna Nagar'),
(179, 14, 'Bilaspur'),
(180, 14, 'Chamba'),
(181, 14, 'Hamirpur'),
(182, 14, 'Kangra'),
(183, 14, 'Kinnaur'),
(184, 14, 'Kullu'),
(185, 14, 'Lahaul and Spiti'),
(186, 14, 'Mandi'),
(187, 14, 'Shimla'),
(188, 14, 'Sirmaur'),
(189, 14, 'Sola'),
(190, 14, 'Una'),
(191, 15, 'Anantnag'),
(192, 15, 'Badgam'),
(193, 15, 'Bandipora'),
(194, 15, 'Baramulla'),
(195, 15, 'Doda'),
(196, 15, 'Ganderbal'),
(197, 15, 'Jammu'),
(198, 15, 'Kargil'),
(199, 15, 'Kathua'),
(200, 15, 'Kishtwar'),
(202, 15, 'Kupwara'),
(203, 15, 'Kulgam'),
(204, 15, 'Leh'),
(205, 15, 'Poonch'),
(206, 15, 'Pulwama'),
(207, 15, 'Rajouri'),
(208, 15, 'Ramba'),
(209, 15, 'Reasi'),
(210, 15, 'Samba'),
(211, 15, 'Shopia'),
(212, 15, 'Srinagar'),
(213, 15, 'Udhampur'),
(214, 16, 'Bokaro'),
(215, 16, 'Chatra'),
(216, 16, 'Deoghar'),
(217, 16, 'Dhanbad'),
(218, 16, 'Dumka'),
(219, 16, 'East Singhbhum'),
(220, 16, 'Garhwa'),
(221, 16, 'Giridih'),
(222, 16, 'Godda'),
(223, 16, 'Gumla'),
(224, 16, 'Hazaribag'),
(225, 16, 'Jamtara'),
(226, 16, 'Khunti'),
(227, 16, 'Koderma'),
(228, 16, 'Latehar'),
(229, 16, 'Lohardaga'),
(230, 16, 'Pakur'),
(231, 16, 'Palamu'),
(232, 16, 'Ramgarh'),
(233, 16, 'Ranchi'),
(234, 16, 'Sahibganj'),
(235, 16, 'Seraikela Kharsawa'),
(236, 16, 'Simdega'),
(237, 16, 'West Singhbhum'),
(238, 17, 'Bagalkot'),
(239, 17, 'Bengaluru'),
(241, 17, 'Belgaum'),
(242, 17, 'Bellary'),
(243, 17, 'Bidar'),
(244, 17, 'Bijapur'),
(245, 17, 'Chamrajnagar'),
(246, 17, 'Chikkamagaluru'),
(247, 17, 'Chikkaballapur'),
(248, 17, 'Chitradurga'),
(249, 17, 'Davangere'),
(250, 17, 'Dharwad'),
(251, 17, 'Mangalore'),
(252, 17, 'Gadag'),
(253, 17, 'Gulbarga'),
(254, 17, 'Hassan'),
(255, 17, 'Haveri'),
(256, 17, 'Kodagu'),
(257, 17, 'Kolar'),
(258, 17, 'Koppal'),
(259, 17, 'Mandya'),
(260, 17, 'Mysore'),
(261, 17, 'Raichur'),
(262, 17, 'Shimoga'),
(263, 17, 'Tumkur'),
(264, 17, 'Udupi'),
(265, 17, 'Uttara Kannada'),
(266, 17, 'Ramnagar'),
(267, 17, 'Yadgir'),
(268, 18, 'Alappuzha'),
(269, 18, 'Ernakulam'),
(270, 18, 'Idukki'),
(271, 18, 'Kannur'),
(272, 18, 'Kasaragod'),
(273, 18, 'Kollam'),
(274, 18, 'Kottayam'),
(275, 18, 'Kozhikode'),
(276, 18, 'Malappuram'),
(277, 18, 'Palakkad'),
(278, 18, 'Pathanamthitta'),
(279, 18, 'Thrissur'),
(280, 18, 'Thiruvananthapuram'),
(281, 18, 'Wayanad'),
(282, 19, 'Lakshadweep'),
(283, 20, 'Agar'),
(284, 20, 'Alirajpur'),
(285, 20, 'Anuppur'),
(286, 20, 'Ashok Nagar'),
(287, 20, 'Balaghat'),
(288, 20, 'Barwani'),
(289, 20, 'Betul'),
(290, 20, 'Bhind'),
(291, 20, 'Bhopal'),
(292, 20, 'Burhanpur'),
(293, 20, 'Chhatarpur'),
(294, 20, 'Chhindwara'),
(295, 20, 'Damoh'),
(296, 20, 'Datia'),
(297, 20, 'Dewas'),
(298, 20, 'Dhar'),
(299, 20, 'Dindori'),
(300, 20, 'Guna'),
(301, 20, 'Gwalior'),
(302, 20, 'Harda'),
(303, 20, 'Hoshangabad'),
(304, 20, 'Indore'),
(305, 20, 'Jabalpur'),
(306, 20, 'Jhabua'),
(307, 20, 'Katni'),
(308, 20, 'Khandwa (East Nimar);'),
(309, 20, 'Khargone (West Nimar);'),
(310, 20, 'Mandla'),
(311, 20, 'Mandsaur'),
(312, 20, 'Morena'),
(313, 20, 'Narsinghpur'),
(314, 20, 'Neemuch'),
(315, 20, 'Panna'),
(316, 20, 'Raise'),
(317, 20, 'Rajgarh'),
(318, 20, 'Ratlam'),
(319, 20, 'Rewa'),
(320, 20, 'Sagar'),
(321, 20, 'Satna'),
(322, 20, 'Sehore'),
(323, 20, 'Seoni'),
(324, 20, 'Shahdol'),
(325, 20, 'Shajapur'),
(326, 20, 'Sheopur'),
(327, 20, 'Shivpuri'),
(328, 20, 'Sidhi'),
(329, 20, 'Singrauli'),
(330, 20, 'Tikamgarh'),
(331, 20, 'Ujjai'),
(332, 20, 'Umaria'),
(333, 20, 'Vidisha'),
(334, 21, 'Ahmednagar'),
(335, 21, 'Akola'),
(336, 21, 'Amravati'),
(337, 21, 'Aurangabad'),
(338, 21, 'Beed'),
(339, 21, 'Bhandara'),
(340, 21, 'Buldhana'),
(341, 21, 'Chandrapur'),
(342, 21, 'Dhule'),
(343, 21, 'Gadchiroli'),
(344, 21, 'Gondia'),
(345, 21, 'Hingoli'),
(346, 21, 'Jalgao'),
(347, 21, 'Jalna'),
(348, 21, 'Kolhapur'),
(349, 21, 'Latur'),
(350, 21, 'Mumbai City'),
(351, 21, 'Mumbai suburba'),
(352, 21, 'Nanded'),
(353, 21, 'Nandurbar'),
(354, 21, 'Nagpur'),
(355, 21, 'Nashik'),
(356, 21, 'Osmanabad'),
(357, 21, 'Parbhani'),
(358, 21, 'Pune'),
(359, 21, 'Raigad'),
(360, 21, 'Ratnagiri'),
(361, 21, 'Sangli'),
(362, 21, 'Satara'),
(363, 21, 'Sindhudurg'),
(364, 21, 'Solapur'),
(365, 21, 'Thane'),
(366, 21, 'Wardha'),
(367, 21, 'Washim'),
(368, 21, 'Yavatmal'),
(369, 22, 'Bishnupur'),
(370, 22, 'Churachandpur'),
(371, 22, 'Chandel'),
(372, 22, 'Imphal East'),
(373, 22, 'Senapati'),
(374, 22, 'Tamenglong'),
(375, 22, 'Thoubal'),
(376, 22, 'Ukhrul'),
(377, 22, 'Imphal West'),
(378, 23, 'East Garo Hills'),
(379, 23, 'East Khasi Hills'),
(380, 23, 'Jaintia Hills'),
(381, 23, 'Ri Bhoi'),
(382, 23, 'South Garo Hills'),
(383, 23, 'West Garo Hills'),
(384, 23, 'West Khasi Hills'),
(385, 24, 'Aizawl'),
(386, 24, 'Champhai'),
(387, 24, 'Kolasib'),
(388, 24, 'Lawngtlai'),
(389, 24, 'Lunglei'),
(390, 24, 'Mamit'),
(391, 24, 'Saiha'),
(392, 24, 'Serchhip'),
(393, 25, 'Dimapur'),
(394, 25, 'Kiphire'),
(395, 25, 'Kohima'),
(396, 25, 'Longleng'),
(397, 25, 'Mokokchung'),
(398, 25, 'Mo'),
(399, 25, 'Pere'),
(400, 25, 'Phek'),
(401, 25, 'Tuensang'),
(402, 25, 'Wokha'),
(403, 25, 'Zunheboto'),
(404, 26, 'Angul'),
(405, 26, 'Boudh (Bauda);'),
(406, 26, 'Bhadrak'),
(407, 26, 'Balangir'),
(408, 26, 'Bargarh (Baragarh);'),
(409, 26, 'Balasore'),
(410, 26, 'Cuttack'),
(411, 26, 'Debagarh (Deogarh);'),
(412, 26, 'Dhenkanal'),
(413, 26, 'Ganjam'),
(414, 26, 'Gajapati'),
(415, 26, 'Jharsuguda'),
(416, 26, 'Jajpur'),
(417, 26, 'Jagatsinghpur'),
(418, 26, 'Khordha'),
(419, 26, 'Kendujhar (Keonjhar);'),
(420, 26, 'Kalahandi'),
(421, 26, 'Kandhamal'),
(422, 26, 'Koraput'),
(423, 26, 'Kendrapara'),
(424, 26, 'Malkangiri'),
(425, 26, 'Mayurbhanj'),
(426, 26, 'Nabarangpur'),
(427, 26, 'Nuapada'),
(428, 26, 'Nayagarh'),
(429, 26, 'Puri'),
(430, 26, 'Rayagada'),
(431, 26, 'Sambalpur'),
(432, 26, 'Subarnapur (Sonepur);'),
(433, 26, 'Sundergarh'),
(434, 27, 'Karaikal'),
(435, 27, 'Mahe'),
(436, 27, 'Pondicherry'),
(437, 27, 'Yanam'),
(438, 28, 'Amritsar'),
(439, 28, 'Barnala'),
(440, 28, 'Bathinda'),
(441, 28, 'Firozpur'),
(442, 28, 'Faridkot'),
(443, 28, 'Fatehgarh Sahib'),
(444, 28, 'Fazilka'),
(445, 28, 'Gurdaspur'),
(446, 28, 'Hoshiarpur'),
(447, 28, 'Jalandhar'),
(448, 28, 'Kapurthala'),
(449, 28, 'Ludhiana'),
(450, 28, 'Mansa'),
(451, 28, 'Moga'),
(452, 28, 'Sri Muktsar Sahib'),
(453, 28, 'Pathankot'),
(454, 28, 'Patiala'),
(455, 28, 'Rupnagar'),
(456, 28, 'Ajitgarh (Mohali);'),
(457, 28, 'Sangrur'),
(458, 28, 'Shahid Bhagat Singh Nagar'),
(459, 28, 'Tarn Tara'),
(460, 29, 'Ajmer'),
(461, 29, 'Alwar'),
(462, 29, 'Bikaner'),
(463, 29, 'Barmer'),
(464, 29, 'Banswara'),
(465, 29, 'Bharatpur'),
(466, 29, 'Bara'),
(467, 29, 'Bundi'),
(468, 29, 'Bhilwara'),
(469, 29, 'Churu'),
(470, 29, 'Chittorgarh'),
(471, 29, 'Dausa'),
(472, 29, 'Dholpur'),
(473, 29, 'Dungapur'),
(474, 29, 'Ganganagar'),
(475, 29, 'Hanumangarh'),
(476, 29, 'Jhunjhunu'),
(477, 29, 'Jalore'),
(478, 29, 'Jodhpur'),
(479, 29, 'Jaipur'),
(480, 29, 'Jaisalmer'),
(481, 29, 'Jhalawar'),
(482, 29, 'Karauli'),
(483, 29, 'Kota'),
(484, 29, 'Nagaur'),
(485, 29, 'Pali'),
(486, 29, 'Pratapgarh'),
(487, 29, 'Rajsamand'),
(488, 29, 'Sikar'),
(489, 29, 'Sawai Madhopur'),
(490, 29, 'Sirohi'),
(491, 29, 'Tonk'),
(492, 29, 'Udaipur'),
(493, 30, 'East Sikkim'),
(494, 30, 'North Sikkim'),
(495, 30, 'South Sikkim'),
(496, 30, 'West Sikkim'),
(497, 31, 'Ariyalur'),
(498, 31, 'Chennai'),
(499, 31, 'Coimbatore'),
(500, 31, 'Cuddalore'),
(501, 31, 'Dharmapuri'),
(502, 31, 'Dindigul'),
(503, 31, 'Erode'),
(504, 31, 'Kanchipuram'),
(505, 31, 'Kanyakumari'),
(506, 31, 'Karur'),
(507, 31, 'Krishnagiri'),
(508, 31, 'Madurai'),
(509, 31, 'Nagapattinam'),
(510, 31, 'Nilgiris'),
(511, 31, 'Namakkal'),
(512, 31, 'Perambalur'),
(513, 31, 'Pudukkottai'),
(514, 31, 'Ramanathapuram'),
(515, 31, 'Salem'),
(516, 31, 'Sivaganga'),
(517, 31, 'Tirupur'),
(518, 31, 'Tiruchirappalli'),
(519, 31, 'Theni'),
(520, 31, 'Tirunelveli'),
(521, 31, 'Thanjavur'),
(522, 31, 'Thoothukudi'),
(523, 31, 'Tiruvallur'),
(524, 31, 'Tiruvarur'),
(525, 31, 'Tiruvannamalai'),
(526, 31, 'Vellore'),
(527, 31, 'Viluppuram'),
(528, 31, 'Virudhunagar'),
(529, 32, 'Adilabad'),
(530, 32, 'Hyderabad'),
(531, 32, 'Karimnagar'),
(532, 32, 'Khammam'),
(533, 32, 'Mahbubnagar'),
(534, 32, 'Medak'),
(535, 32, 'Nalgonda'),
(536, 32, 'Nizamabad'),
(537, 32, 'Ranga Reddy'),
(538, 32, 'Warangal'),
(539, 33, 'Dhalai'),
(540, 33, 'North Tripura'),
(541, 33, 'South Tripura'),
(542, 33, 'Khowai'),
(543, 33, 'West Tripura'),
(544, 35, 'Agra'),
(545, 35, 'Aligarh'),
(546, 35, 'Allahabad'),
(547, 35, 'Ambedkar Nagar'),
(548, 35, 'Auraiya'),
(549, 35, 'Azamgarh'),
(550, 35, 'Bagpat'),
(551, 35, 'Bahraich'),
(552, 35, 'Ballia'),
(553, 35, 'Balrampur'),
(554, 35, 'Banda'),
(555, 35, 'Barabanki'),
(556, 35, 'Bareilly'),
(557, 35, 'Basti'),
(558, 35, 'Bijnor'),
(559, 35, 'Budau'),
(560, 35, 'Bulandshahr'),
(561, 35, 'Chandauli'),
(562, 35, 'Amethi (Chhatrapati Shahuji Maharaj Nagar)'),
(563, 35, 'Chitrakoot'),
(564, 35, 'Deoria'),
(565, 35, 'Etah'),
(566, 35, 'Etawah'),
(567, 35, 'Faizabad'),
(568, 35, 'Farrukhabad'),
(569, 35, 'Fatehpur'),
(570, 35, 'Firozabad'),
(571, 35, 'Gautam Buddh Nagar'),
(572, 35, 'Ghaziabad'),
(573, 35, 'Ghazipur'),
(574, 35, 'Gonda'),
(575, 35, 'Gorakhpur'),
(576, 35, 'Hamirpur'),
(577, 35, 'Hardoi'),
(578, 35, 'Hathras (Mahamaya Nagar);'),
(579, 35, 'Jalau'),
(580, 35, 'Jaunpur'),
(581, 35, 'Jhansi'),
(582, 35, 'Jyotiba Phule Nagar'),
(583, 35, 'Kannauj'),
(584, 35, 'Kanpur Dehat (Ramabai Nagar);'),
(585, 35, 'Kanpur Nagar'),
(586, 35, 'Kanshi Ram Nagar'),
(587, 35, 'Kaushambi'),
(588, 35, 'Kushinagar'),
(589, 35, 'Lakhimpur Kheri'),
(590, 35, 'Lalitpur'),
(591, 35, 'Lucknow'),
(592, 35, 'Maharajganj'),
(593, 35, 'Mahoba'),
(594, 35, 'Mainpuri'),
(595, 35, 'Mathura'),
(596, 35, 'Mau'),
(597, 35, 'Meerut'),
(598, 35, 'Mirzapur'),
(599, 35, 'Moradabad'),
(600, 35, 'Muzaffarnagar'),
(601, 35, 'Panchsheel Nagar (Hapur);'),
(602, 35, 'Pilibhit'),
(603, 35, 'Pratapgarh'),
(604, 35, 'Raebareli'),
(605, 35, 'Rampur'),
(606, 35, 'Saharanpur'),
(607, 35, 'Sambhal(Bheem Nagar);'),
(608, 35, 'Sant Kabir Nagar'),
(609, 35, 'Sant Ravidas Nagar'),
(610, 35, 'Shahjahanpur'),
(611, 35, 'Shamli'),
(612, 35, 'Shravasti'),
(613, 35, 'Siddharthnagar'),
(614, 35, 'Sitapur'),
(615, 35, 'Sonbhadra'),
(616, 35, 'Sultanpur'),
(617, 35, 'Unnao'),
(618, 35, 'Varanasi'),
(619, 34, 'Almora'),
(620, 34, 'Bageshwar'),
(621, 34, 'Chamoli'),
(622, 34, 'Champawat'),
(623, 34, 'Dehradu'),
(624, 34, 'Haridwar'),
(625, 34, 'Nainital'),
(626, 34, 'Pauri Garhwal'),
(627, 34, 'Pithoragarh'),
(628, 34, 'Rudraprayag'),
(629, 34, 'Tehri Garhwal'),
(630, 34, 'Udham Singh Nagar'),
(631, 34, 'Uttarkashi'),
(632, 36, 'Bankura'),
(633, 36, 'Bardhama'),
(634, 36, 'Birbhum'),
(635, 36, 'Cooch Behar'),
(636, 36, 'Dakshin Dinajpur'),
(637, 36, 'Darjeeling'),
(638, 36, 'Hooghly'),
(639, 36, 'Howrah'),
(640, 36, 'Jalpaiguri'),
(641, 36, 'Kolkata'),
(642, 36, 'Maldah'),
(643, 36, 'Murshidabad'),
(644, 36, 'Nadia'),
(645, 36, 'North 24 Parganas'),
(646, 36, 'Paschim Medinipur'),
(647, 36, 'Purba Medinipur'),
(648, 36, 'Purulia'),
(649, 36, 'South 24 Parganas'),
(650, 36, 'Uttar Dinajpur');

-- --------------------------------------------------------

--
-- Table structure for table `farmer`
--

CREATE TABLE `farmer` (
  `farmer_id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `mobile_no` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farmerlogin`
--

CREATE TABLE `farmerlogin` (
  `farmer_id` int(11) NOT NULL,
  `farmer_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_no` varchar(50) NOT NULL,
  `F_gender` varchar(255) NOT NULL,
  `F_birthday` varchar(255) NOT NULL,
  `F_State` varchar(255) NOT NULL,
  `F_District` varchar(255) NOT NULL,
  `F_Location` varchar(255) NOT NULL,
  `otp` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `farmerlogin`
--

INSERT INTO `farmerlogin` (`farmer_id`, `farmer_name`, `password`, `email`, `phone_no`, `F_gender`, `F_birthday`, `F_State`, `F_District`, `F_Location`, `otp`) VALUES
(44, 'manoj', 'password', 'agricultureportal01@gmail.com', '9878987898', 'Male', '2001-09-22', '', '', 'Bantakal', 0),
(61, 'Manoj Ishwar Naik', 'Mnb@123', 'lenevos307@gmail.com', '07676445273', '', '', '17', 'Uttara Kannada', 'Mangalore Karnataka', 0),
(62, 'chetan', 'Mnb@123', 'naik97059@gmail.com', '07676445273', 'Male', '2003-02-18', '17', 'Uttara Kannada', 'Mangalore Karnataka', 0);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_crops_trade`
--

CREATE TABLE `farmer_crops_trade` (
  `trade_id` int(11) NOT NULL,
  `farmer_fkid` int(50) NOT NULL,
  `Trade_crop` varchar(255) NOT NULL,
  `Crop_quantity` double NOT NULL,
  `costperkg` int(11) NOT NULL,
  `msp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `farmer_crops_trade`
--

INSERT INTO `farmer_crops_trade` (`trade_id`, `farmer_fkid`, `Trade_crop`, `Crop_quantity`, `costperkg`, `msp`) VALUES
(104, 44, 'arhar', 0, 39, 60),
(105, 44, 'bajra', 0, 15, 45),
(108, 44, 'soyabean', 34, 56, 78),
(122, 44, 'gram', 90000, 89, 134),
(123, 61, 'wheat', 545477, 65, 99),
(125, 61, 'cotton', 50183, 56, 93),
(127, 44, 'tomato', 218, 40, 60),
(129, 61, 'bajra', 653636, 45, 45),
(130, 44, 'cotton', 56, 67, 93);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_discussions`
--

CREATE TABLE `farmer_discussions` (
  `id` int(11) NOT NULL,
  `farmer_email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_discussions`
--

INSERT INTO `farmer_discussions` (`id`, `farmer_email`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 'agricultureportal01@gmail.com', 'manoj', 'fdjdif', '2024-12-02 16:22:37', '2024-12-02 16:22:37'),
(2, 'agricultureportal01@gmail.com', 'gnnn', 'gfnfgnfgn', '2024-12-02 16:26:48', '2024-12-02 16:26:48'),
(3, 'agricultureportal01@gmail.com', 'NJKFGF', 'KERMGKERMGK', '2024-12-04 04:51:00', '2024-12-04 04:51:00');

-- --------------------------------------------------------

--
-- Table structure for table `farmer_history`
--

CREATE TABLE `farmer_history` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `farmer_crop` varchar(255) NOT NULL,
  `farmer_quantity` decimal(10,2) NOT NULL,
  `farmer_price` decimal(10,2) NOT NULL,
  `date` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_history`
--

INSERT INTO `farmer_history` (`id`, `farmer_id`, `farmer_crop`, `farmer_quantity`, `farmer_price`, `date`) VALUES
(1, 61, 'cotton', 5655.00, 424125.00, '07/12/2024'),
(2, 61, 'cotton', 5452.00, 408900.00, '08/12/2024');

-- --------------------------------------------------------

--
-- Table structure for table `farmtube_categories`
--

CREATE TABLE `farmtube_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmtube_categories`
--

INSERT INTO `farmtube_categories` (`id`, `name`) VALUES
(1, 'Crop Farming'),
(2, 'Animal Husbandry'),
(3, 'Organic Farming'),
(4, 'Farm Equipment'),
(5, 'Pest Control'),
(6, 'Irrigation Techniques'),
(7, 'Soil Management'),
(8, 'Harvest Tips'),
(9, 'Marketing Tips'),
(10, 'Success Stories');

-- --------------------------------------------------------

--
-- Table structure for table `farmtube_comments`
--

CREATE TABLE `farmtube_comments` (
  `id` int(11) NOT NULL,
  `postedBy` varchar(50) NOT NULL,
  `userType` enum('farmer','customer','admin') NOT NULL,
  `videoId` int(11) NOT NULL,
  `responseTo` int(11) DEFAULT NULL,
  `body` text NOT NULL,
  `datePosted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmtube_comments`
--

INSERT INTO `farmtube_comments` (`id`, `postedBy`, `userType`, `videoId`, `responseTo`, `body`, `datePosted`) VALUES
(1, 'agricultureportal01@gmail.com', 'farmer', 1, NULL, 'hi', '2024-12-01 13:54:48'),
(2, 'agricultureportal01@gmail.com', 'farmer', 1, NULL, 'hdh', '2024-12-01 13:54:57'),
(3, 'agricultureportal01@gmail.com', 'farmer', 2, NULL, 'hi\\r\\n', '2024-12-01 14:07:42'),
(4, 'agricultureportal01@gmail.com', 'customer', 2, NULL, 'gdg', '2024-12-01 17:01:12'),
(5, 'agricultureportal01@gmail.com', 'customer', 2, NULL, 'ffasf', '2024-12-01 17:03:07'),
(6, 'agricultureportal01@gmail.com', 'customer', 1, NULL, 'dcca', '2024-12-01 17:37:09'),
(7, 'agricultureportal01@gmail.com', 'customer', 3, NULL, 'jklmk;f', '2024-12-01 18:01:24'),
(8, 'agricultureportal01@gmail.com', 'customer', 3, NULL, 'fsfdsf', '2024-12-01 18:02:14'),
(9, 'agricultureportal01@gmail.com', 'customer', 1, NULL, 'dsgg', '2024-12-01 18:02:32'),
(10, 'agricultureportal01@gmail.com', 'customer', 3, NULL, 'ggggg', '2024-12-01 18:02:48'),
(11, 'agricultureportal01@gmail.com', 'customer', 3, NULL, 'bdbd', '2024-12-01 18:10:12'),
(12, 'agricultureportal01@gmail.com', 'customer', 3, NULL, 'thtrh', '2024-12-01 19:18:50'),
(13, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'cvvx', '2024-12-02 17:14:30'),
(14, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'hlkan', '2024-12-07 11:54:35'),
(15, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'manoj', '2024-12-07 11:56:43'),
(16, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'ndskfs', '2024-12-07 11:57:35'),
(17, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'kkdfsf', '2024-12-07 11:59:19'),
(18, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'jiki', '2024-12-07 12:00:28'),
(19, 'agricultureportal01@gmail.com', 'farmer', 1, NULL, 'dsgsg', '2024-12-07 12:02:32'),
(20, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'fhggfj', '2024-12-07 12:04:32'),
(21, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'anoj', '2024-12-07 12:04:42'),
(22, 'agricultureportal01@gmail.com', 'farmer', 3, NULL, 'hi', '2024-12-07 12:07:06'),
(23, 'agricultureportal01@gmail.com', 'farmer', 1, NULL, 'hi', '2024-12-07 12:10:43'),
(24, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'hi', '2024-12-07 12:17:06'),
(25, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'hii', '2024-12-07 12:21:09'),
(26, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'mnbhj', '2024-12-07 12:26:33'),
(27, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'husfh', '2024-12-07 12:37:24'),
(28, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'hi', '2024-12-07 12:40:00'),
(29, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'ghdgdf', '2024-12-07 12:56:47'),
(30, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'hii', '2024-12-07 13:48:48'),
(31, 'agricultureportal01@gmail.com', 'farmer', 4, NULL, 'hjksff', '2024-12-07 13:52:08'),
(32, 'agricultureportal01@gmail.com', 'customer', 4, NULL, 'hi', '2024-12-08 10:22:12'),
(33, 'agricultureportal01@gmail.com', 'customer', 4, NULL, 'hi', '2024-12-08 10:53:34'),
(34, 'lenevos307@gmail.com', 'farmer', 4, NULL, 'Hi', '2024-12-09 18:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `farmtube_likes`
--

CREATE TABLE `farmtube_likes` (
  `id` int(11) NOT NULL,
  `videoId` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `userType` enum('farmer','customer','admin') NOT NULL,
  `liked` tinyint(1) NOT NULL DEFAULT 0,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmtube_likes`
--

INSERT INTO `farmtube_likes` (`id`, `videoId`, `userId`, `userType`, `liked`, `dateCreated`) VALUES
(1, 4, 'agricultureportal01@gmail.com', 'farmer', 1, '2024-12-07 08:21:50'),
(3, 3, 'agricultureportal01@gmail.com', 'farmer', 1, '2024-12-07 08:23:38'),
(4, 2, 'agricultureportal01@gmail.com', 'farmer', 0, '2024-12-07 08:32:18'),
(5, 1, 'agricultureportal01@gmail.com', 'farmer', 0, '2024-12-07 08:32:23'),
(7, 4, 'agricultureportal01@gmail.com', 'customer', 1, '2024-12-08 05:04:32'),
(8, 4, 'lenevos307@gmail.com', 'farmer', 1, '2024-12-09 13:20:58');

-- --------------------------------------------------------

--
-- Table structure for table `farmtube_thumbnails`
--

CREATE TABLE `farmtube_thumbnails` (
  `id` int(11) NOT NULL,
  `videoId` int(11) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `selected` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farmtube_videos`
--

CREATE TABLE `farmtube_videos` (
  `id` int(11) NOT NULL,
  `uploadedBy` varchar(50) NOT NULL,
  `userType` enum('farmer','customer','admin') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `privacy` int(11) NOT NULL DEFAULT 1,
  `filePath` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `uploadDate` datetime NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `duration` varchar(10) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `dislikes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmtube_videos`
--

INSERT INTO `farmtube_videos` (`id`, `uploadedBy`, `userType`, `title`, `description`, `privacy`, `filePath`, `category`, `uploadDate`, `views`, `duration`, `thumbnail`, `likes`, `dislikes`) VALUES
(1, 'agricultureportal01@gmail.com', 'farmer', 'tet', 'wtwyw', 1, '1733041288_Recording 2024-10-04 145738.mp4', 'Livestock', '2024-12-01 13:51:28', 33, '0:11', '1733041288_image.jpg', 0, 1),
(2, 'agricultureportal01@gmail.com', 'farmer', 'manoj', 'manoj description', 1, '1733041606_Recording 2024-10-04 145738.mp4', 'Tips & Tricks', '2024-12-01 13:56:46', 46, '0:11', '1733041606_WhatsApp Image 2024-11-30 at 13.25.05_47e5946d.jpg', 0, 1),
(3, 'agricultureportal01@gmail.com', 'farmer', 'grwg', 'gdgfgxg', 1, '1733054297_Recording 2024-10-04 145738.mp4', 'Livestock', '2024-12-01 17:28:17', 64, '0:11', '1733054297_image (1).jpg', 1, 0),
(4, 'agricultureportal01@gmail.com', 'farmer', 'fssdfsdf', 'fwfwfw', 1, '1733139858_Recording 2024-10-04 145738.mp4', 'Crop Farming', '2024-12-02 17:14:18', 66, '0:11', '1733139858_Screenshot 2024-09-14 103106.png', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL,
  `payment_method` enum('cod','online') DEFAULT 'cod',
  `payment_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `delivery_address` text DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `total_amount`, `order_date`, `payment_method`, `payment_id`, `status`, `delivery_address`, `phone`, `created_at`, `updated_at`) VALUES
(20, 'ORD675c2654bad18', 1, 120.00, '2024-12-13 13:19:32', 'cod', NULL, 'processing', 'Mangalore Karnataka', '7676445273', '2024-12-13 12:19:32', '2024-12-14 13:56:04'),
(21, 'ORD675c27fd468cd', 1, 1360.00, '2024-12-13 13:26:37', 'cod', NULL, 'processing', 'Mangalore Karnataka', '7676445273', '2024-12-13 12:26:37', '2024-12-14 13:56:02'),
(22, 'ORD675c29093980e', 1, 1935.00, '2024-12-13 18:01:05', 'cod', NULL, 'completed', 'Mangalore Karnataka', '7676445273', '2024-12-13 12:31:05', '2024-12-14 13:56:43'),
(23, 'ORD675c394ee10b5', 1, 1760.00, '2024-12-13 19:10:30', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 13:40:30', '2024-12-13 13:52:28'),
(24, 'ORD675c3c521309e', 1, 20976.00, '2024-12-13 19:23:22', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 13:53:22', '2024-12-13 13:56:04'),
(25, 'ORD675c3db305cf4', 1, 1904.00, '2024-12-13 19:29:15', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 13:59:15', '2024-12-13 13:59:25'),
(26, 'ORD675c3eef4a42c', 1, 168.00, '2024-12-13 19:34:31', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 14:04:31', '2024-12-13 14:04:44'),
(27, 'ORD675c4002cbf19', 1, 224.00, '2024-12-13 19:39:06', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 14:09:06', '2024-12-13 14:10:24'),
(28, 'ORD675c43024495a', 1, 224.00, '2024-12-13 19:51:54', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 14:21:54', '2024-12-13 14:22:03'),
(29, 'ORD675c43dec77e9', 1, 224.00, '2024-12-13 19:55:34', 'cod', NULL, '', 'Mangalore Karnataka', '7676445273', '2024-12-13 14:25:34', '2024-12-13 14:25:43'),
(30, 'ORD675c4589d75f3', 1, 168.00, '2024-12-13 20:02:41', 'cod', NULL, 'completed', 'Mangalore Karnataka', '7676445273', '2024-12-13 14:32:41', '2024-12-13 14:36:02'),
(31, 'ORD675c4963f059a', 1, 224.00, '2024-12-13 20:19:07', 'cod', NULL, 'processing', 'Mangalore Karnataka', '7676445273', '2024-12-13 14:49:07', '2024-12-13 14:49:20'),
(32, 'ORD675d8e393c9a7', 1, 40.00, '2024-12-14 19:25:05', 'cod', NULL, 'completed', 'Mangalore Karnataka', '4676445273', '2024-12-14 13:55:05', '2024-12-14 13:56:41');

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `crop_name` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`id`, `order_id`, `customer_id`, `farmer_id`, `crop_name`, `quantity`, `price`, `total_price`, `order_date`, `created_at`) VALUES
(1, 'ORD675c2287a48a2', 1, 44, 'tomato', 1.00, 40.00, 40.00, '2024-12-13 17:33:19', '2024-12-13 12:03:19'),
(2, 'ORD675c22c4eb581', 1, 44, 'tomato', 2.00, 40.00, 80.00, '2024-12-13 17:34:20', '2024-12-13 12:04:20'),
(3, 'ORD675c2654bad18', 1, 44, 'tomato', 3.00, 40.00, 120.00, '2024-12-13 17:49:32', '2024-12-13 12:19:32'),
(4, 'ORD675c27fd468cd', 1, 44, 'tomato', 34.00, 40.00, 1360.00, '2024-12-13 17:56:37', '2024-12-13 12:26:37'),
(5, 'ORD675c29093980e', 1, 44, 'urad', 43.00, 45.00, 1935.00, '2024-12-13 18:01:05', '2024-12-13 12:31:05'),
(6, 'ORD675c394ee10b5', 1, 61, 'potato', 44.00, 40.00, 1760.00, '2024-12-13 19:10:30', '2024-12-13 13:40:30'),
(7, 'ORD675c3c521309e', 1, 61, 'jowar', 456.00, 46.00, 20976.00, '2024-12-13 19:23:22', '2024-12-13 13:53:22'),
(8, 'ORD675c3db305cf4', 1, 61, 'cotton', 34.00, 56.00, 1904.00, '2024-12-13 19:29:15', '2024-12-13 13:59:15'),
(9, 'ORD675c3eef4a42c', 1, 61, 'cotton', 3.00, 56.00, 168.00, '2024-12-13 19:34:31', '2024-12-13 14:04:31'),
(10, 'ORD675c4002cbf19', 1, 61, 'cotton', 4.00, 56.00, 224.00, '2024-12-13 19:39:06', '2024-12-13 14:09:06'),
(11, 'ORD675c43024495a', 1, 61, 'cotton', 4.00, 56.00, 224.00, '2024-12-13 19:51:54', '2024-12-13 14:21:54'),
(12, 'ORD675c43dec77e9', 1, 61, 'cotton', 4.00, 56.00, 224.00, '2024-12-13 19:55:34', '2024-12-13 14:25:34'),
(13, 'ORD675c4589d75f3', 1, 61, 'cotton', 3.00, 56.00, 168.00, '2024-12-13 20:02:41', '2024-12-13 14:32:41'),
(14, 'ORD675c4963f059a', 1, 61, 'cotton', 4.00, 56.00, 224.00, '2024-12-13 20:19:07', '2024-12-13 14:49:07'),
(15, 'ORD675d8e393c9a7', 1, 44, 'tomato', 1.00, 40.00, 40.00, '2024-12-14 19:25:05', '2024-12-14 13:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `crop_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plant_diseases`
--

CREATE TABLE `plant_diseases` (
  `id` int(11) NOT NULL,
  `disease_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plant_disease_detection`
--

CREATE TABLE `plant_disease_detection` (
  `id` int(11) NOT NULL,
  `farmer_email` varchar(255) NOT NULL,
  `disease_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `confidence_score` float DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `detection_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plant_disease_detection`
--

INSERT INTO `plant_disease_detection` (`id`, `farmer_email`, `disease_name`, `description`, `confidence_score`, `image_path`, `detection_date`) VALUES
(7, 'agricultureportal01@gmail.com', 'Unknown Disease', 'A fungal disease affecting leaves causing brown spots and withering.', 92, 'uploads/plant_diseases/675ddf0e1d574_citrus-canker-20190106-aphis-chrp-0039.jpg', '2024-12-14 19:40:03'),
(8, 'agricultureportal01@gmail.com', 'Unknown Disease', 'A fungal disease affecting leaves causing brown spots and withering.', 92, 'uploads/plant_diseases/675de09fc9747_images.jpg', '2024-12-14 19:46:43'),
(9, 'agricultureportal01@gmail.com', 'Unknown Disease', 'A fungal disease affecting leaves causing brown spots and withering.', 92, 'uploads/plant_diseases/675de0dc4bfbc_disease-models-pear.jpg', '2024-12-14 19:47:43'),
(10, 'agricultureportal01@gmail.com', 'Unknown Disease', 'The analysis suggests abnormal plant conditions requiring attention.', 0, 'uploads/plant_diseases/675de169132a8_disease-models-pear.jpg', '2024-12-14 19:50:04'),
(11, 'agricultureportal01@gmail.com', 'can also affect the leaves and stems', 'The analysis suggests abnormal plant conditions requiring attention.', 85, 'uploads/plant_diseases/675de1bd2efb1_disease-models-pear.jpg', '2024-12-14 19:51:28'),
(12, 'agricultureportal01@gmail.com', 'can make the fruit ugly and reduce its quality, making it hard to sell', 'of a fungal disease', 85, 'uploads/plant_diseases/675de21eac319_disease-models-pear.jpg', '2024-12-14 19:53:06'),
(13, 'agricultureportal01@gmail.com', '', '', 0, 'uploads/plant_diseases/675de2d5dd87b_disease-models-pear.jpg', '2024-12-14 19:56:09'),
(14, 'agricultureportal01@gmail.com', '', NULL, 0, 'uploads/plant_diseases/675de33444919_disease-models-pear.jpg', '2024-12-14 19:57:45'),
(15, 'agricultureportal01@gmail.com', 'The pear fruit in the image shows signs of a fungal disease', 'The pear fruit in the image shows signs of a fungal disease.  It\'s likely **Pear Scab**.\n\nPear scab makes the fruit look bumpy and scabby, with brown or dark spots. The spots are often covered in a whitish or grayish powder.  This disease can make the fruit smaller and less tasty, and sometimes it can even cause the fruit to fall off the tree early.  You should consult an agricultural expert or use a suitable fungicide to control this disease.  Early treatment is important.\n', 85, 'uploads/plant_diseases/675de41e6f586_disease-models-pear.jpg', '2024-12-14 20:01:38'),
(16, 'agricultureportal01@gmail.com', 'Healthy', 'The papaya fruit in the image shows signs of disease.  It\'s likely **Papaya Ringspot Virus (PRSV)**.\n\nSimple explanation for the farmer:\n\nThis papaya fruit is sick.  It has PRSV disease.  This virus makes the fruit look pale, patchy, and sometimes bumpy.  It can also affect the whole plant, making it weak and not produce good fruits.  There is no cure, so you need to prevent it.  Use healthy seeds, remove and destroy sick plants, and control the insects that spread the virus (aphids).  You might also consider planting resistant varieties if available in your area.\n', 85, 'uploads/plant_diseases/6760394c513a5_images.jpg', '2024-12-16 14:29:36'),
(17, 'agricultureportal01@gmail.com', 'Therefore, I cannot analyze it for plant diseases', 'That\'s a picture of a person, not a plant.  Therefore, I cannot analyze it for plant diseases.  To get a plant disease diagnosis, please provide a clear image of the affected plant.\n', 85, 'uploads/plant_diseases/6760395c4d7b7_WhatsApp Image 2024-11-18 at 18.57.41_93ccd3cc.jpg', '2024-12-16 14:29:51'),
(18, 'agricultureportal01@gmail.com', 'The papaya fruit in the image shows signs of disease', 'The papaya fruit in the image shows signs of disease.  It\'s likely suffering from **Papaya Ringspot Virus (PRSV)**.\n\nSimple explanation for the farmer:  The papaya fruit is showing a pale, whitish, blotchy skin. This is a sign of a serious virus called Papaya Ringspot.  This virus makes the fruit look bad and can stop it from growing properly.  It can also spread to other papaya plants.  You need to remove the affected fruit and plant immediately.  There is no cure, so preventing it from spreading is very important.  Talk to your local agricultural officer for advice on managing this disease in your field.\n', 85, 'uploads/plant_diseases/676039a9e8bfc_images.jpg', '2024-12-16 14:31:09'),
(19, 'agricultureportal01@gmail.com', 'The papaya fruit in the image shows signs of disease', 'The papaya fruit in the image shows signs of disease.  It looks like it might have **Papaya Ringspot Virus (PRSV)**.\n\nSimple explanation for the farmer:\n\nThis papaya fruit is sick.  It has a virus called Papaya Ringspot.  This virus makes the fruit look pale, patchy, and sometimes bumpy.  The whole plant can get sick, not just the fruit.  The virus spreads easily from plant to plant.  You need to be careful.  If you see more fruits like this, or if the leaves are also looking bad, you should talk to an agriculture officer or someone who knows about papaya diseases. They can help you find ways to stop the virus from spreading and save your other papaya plants.\n', 85, 'uploads/plant_diseases/67603b880abb0_images.jpg', '2024-12-16 14:39:07'),
(20, 'agricultureportal01@gmail.com', 'The papaya fruit in the image shows signs of disease', 'The papaya fruit in the image shows signs of disease.  It\'s likely **Papaya Ringspot Virus (PRSV)**.\n\nSimple explanation for the farmer:\n\nThis papaya fruit is sick.  It has PRSV, a virus.  The fruit looks pale and patchy,  like it\'s got white spots.  PRSV makes the fruits small and ugly, and it can spread to other papaya plants easily.  You need to remove the sick fruit and plant immediately.  Also, you should talk to your local agriculture officer for advice on how to stop the disease from spreading to your other papaya plants.  They might suggest some good medicine for your plants.\n', 85, 'uploads/plant_diseases/67603c745b0eb_images.jpg', '2024-12-16 14:43:03'),
(21, 'agricultureportal01@gmail.com', 'Healthy', 'The papaya fruit in the image shows signs of disease.  It\'s likely **Papaya Ringspot Virus (PRSV)**.\n\nSimple explanation for the farmer:\n\nThis papaya fruit is sick.  It has a virus called Papaya Ringspot.  This virus makes the fruit look pale, patchy, and sometimes bumpy. It can also make the whole plant weak and not give many fruits.  There is no cure for this virus, so you need to be careful.  Buy healthy plants from a good nursery, and if you see this problem, remove the sick plants quickly to stop it from spreading to the others.  You might want to talk to your local agriculture officer for more advice.\n', 85, 'uploads/plant_diseases/67603d19eca92_images.jpg', '2024-12-16 14:45:48'),
(22, 'agricultureportal01@gmail.com', 'The papaya fruit in the image shows signs of disease', 'The papaya fruit in the image shows signs of disease.  It\'s likely **Papaya Ringspot Virus (PRSV)**.\n\nSimple explanation for the farmer:\n\nThis papaya fruit is sick.  It has PRSV, a virus.  This virus makes the fruit look pale and patchy, sometimes with a ring-like pattern (though not very clear in this image).  The fruit won\'t grow properly and will probably not be tasty.  PRSV can spread to other papaya plants easily.  You should remove the sick fruit and plant immediately.  Talk to your local agriculture officer for advice on how to prevent this disease in your other papaya plants.  They might suggest using disease-resistant varieties or other control measures.\n', 85, 'uploads/plant_diseases/67603da347e98_images.jpg', '2024-12-16 14:48:06'),
(23, 'agricultureportal01@gmail.com', 'The papaya fruit in the image shows signs of disease', 'The papaya fruit in the image shows signs of disease.  It\'s difficult to pinpoint the *exact* disease from just an image, but the symptoms strongly suggest **papaya anthracnose** or a similar fungal infection.\nHere\'s an analysis based on the likely diagnosis:\n**Disease Name and Brief Description:**  The papaya fruit shows symptoms consistent with Anthracnose (a fungal disease).  Anthracnose causes sunken, dark lesions (spots) on the fruit, often starting small and expanding.  These lesions can be grayish-white or light brown with darker margins, as seen in the image.  It can affect the fruit, leaves, and stems.  In simple Indian English:  ये आम पपीते पे एक फंगस का रोग है, जिससे फल पे काले धब्बे पड़ जाते हैं और सड़न शुरू हो जाती है।\n', 85, 'uploads/plant_diseases/67603e423c0d1_images.jpg', '2024-12-16 14:50:49'),
(24, 'agricultureportal01@gmail.com', 'The papaya fruits in the image show signs of disease', 'The papaya fruits in the image show signs of disease.  It\'s difficult to pinpoint the exact disease from just the image, but the symptoms strongly suggest **papaya ringspot virus (PRSV)** or a similar viral infection, potentially combined with some fungal rot in the ripening fruits.\nHere\'s an analysis following your requested format:\nDisease Name and Brief Description (in simple Indian English):**\nThe papayas likely have *Papaya Ringspot Virus (PRSV)*.  Yeh ek virus hai jo papaya ke ped ko bahut nuksan pahunchata hai.  Isse papayon par daag, mote-mote dhabbe aur unka aakar bigadna jaise lakshan dikhte hain.  Aise papayon ka rang bhi badal sakta hai aur woh jaldi kharab ho jaate hain.  Kacchi papayon par bhi yeh lakshan dikh sakte hain.  Kabhi kabhi, yeh fungal infection ke saath bhi ho sakta hai, jisse papayon mein sadi lag jati hai.\n', 85, 'uploads/plant_diseases/67604ca9e0d6f_PapayaNelson8.jpg', '2024-12-16 15:52:18'),
(25, 'agricultureportal01@gmail.com', '**Disease Name and Brief Description:**  Soybean Frogeye Leaf Spot (Hindi: सोयाबीन फ्रॉगआई लीफ स्पॉट)', 'The image shows a soybean leaf exhibiting symptoms consistent with **Soybean Frogeye Leaf Spot**.\n**Disease Name and Brief Description:**  Soybean Frogeye Leaf Spot (Hindi: सोयाबीन फ्रॉगआई लीफ स्पॉट).  Yeh ek fungal bimari hai jismein patton pe chhote, bhoore, ghole hue daag dikhte hain.  Yeh daag baad mein bade ho jaate hain aur unke beech mein ek halka rang ka hissa hota hai.  Aakhir mein, patte murjha jaate hain aur gir jaate hain. (This is a fungal disease where small, brown, sunken spots appear on the leaves. These spots later enlarge and have a lighter colored center. Eventually, leaves wither and fall.)\n', 85, 'uploads/plant_diseases/676124c543a2b_leave-with-fungus.jpg', '2024-12-17 07:14:20'),
(26, 'agricultureportal01@gmail.com', '**Disease Name and Brief Description:**  Soybean Frogeye Leaf Spot (Hindi: सोयाबीन में फ्रॉगआई लीफ स्पॉट)', 'The image shows a soybean leaf exhibiting symptoms consistent with **Soybean Frogeye Leaf Spot**.\n**Disease Name and Brief Description:**  Soybean Frogeye Leaf Spot (Hindi: सोयाबीन में फ्रॉगआई लीफ स्पॉट).  Yeh ek fungal bimari hai jismein patton pe chhote, bhoore-kale daag ban jaate hain jo menhde ki aankhon jaise dikhte hain.  Yeh daag baad mein bade ho jaate hain aur patte ka rang badal dete hain.  (This is a fungal disease where small, brown-black spots appear on the leaves, resembling frog eyes. These spots later enlarge and change the leaf color.)\n', 85, 'uploads/plant_diseases/67612504147d9_leave-with-fungus.jpg', '2024-12-17 07:15:22'),
(27, 'agricultureportal01@gmail.com', 'The image shows rose leaves exhibiting symptoms consistent with several potential diseases, making a definitive diagnosis from just an image difficult', 'The image shows rose leaves exhibiting symptoms consistent with several potential diseases, making a definitive diagnosis from just an image difficult.  The yellowing, browning, and black spots suggest a combination of issues, possibly including fungal and/or physiological problems.  Let\'s address some possibilities:\nPossible Disease Scenarios:**\nIt\'s important to note that this is not a definitive diagnosis, and a proper identification requires a closer examination by a plant pathologist or experienced gardener.\nScenario 1:  Combination of Black Spot and Leaf Spot Diseases (most likely)**\n**Disease Name and Brief Description:**  The leaves show signs of *Black spot* (a fungal disease causing black spots) and possibly other leaf spot diseases (fungal or bacterial).  Black spot makes leaves yellow and drop prematurely.  Other leaf spots can cause similar symptoms.\n', 85, 'uploads/plant_diseases/676ae54c77e2c_download.jpg', '2024-12-24 16:46:11');

-- --------------------------------------------------------

--
-- Table structure for table `production_approx`
--

CREATE TABLE `production_approx` (
  `crop` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_kg` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `production_approx`
--

INSERT INTO `production_approx` (`crop`, `quantity`, `price_per_kg`) VALUES
('bajra', 653636, 0.00),
('barley', 3, 35.00),
('cotton', 46718, 75.00),
('gram', 90000, 65.00),
('jowar', 6, 40.00),
('jute', 0, 55.00),
('lentil', 0, 95.00),
('maize', 0, 50.00),
('moong', 0, 80.00),
('potato', 55, 0.00),
('ragi', 157, 45.00),
('rice', 45, 60.00),
('soyabean', 0, 70.00),
('tomato', 300, 0.00),
('urad', 842, 85.00),
('wheat', 502035, 55.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_history`
--

CREATE TABLE `purchase_history` (
  `id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `crop_name` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `purchase_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `StCode` int(11) NOT NULL,
  `StateName` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`StCode`, `StateName`) VALUES
(1, 'Andaman and Nicobar Island (UT)'),
(2, 'Andhra Pradesh'),
(3, 'Arunachal Pradesh'),
(4, 'Assam'),
(5, 'Bihar'),
(6, 'Chandigarh (UT)'),
(7, 'Chhattisgarh'),
(8, 'Dadra and Nagar Haveli (UT)'),
(9, 'Daman and Diu (UT)'),
(10, 'Delhi (NCT)'),
(11, 'Goa'),
(12, 'Gujarat'),
(13, 'Haryana'),
(14, 'Himachal Pradesh'),
(15, 'Jammu and Kashmir'),
(16, 'Jharkhand'),
(17, 'Karnataka'),
(18, 'Kerala'),
(19, 'Lakshadweep (UT)'),
(20, 'Madhya Pradesh'),
(21, 'Maharashtra'),
(22, 'Manipur'),
(23, 'Meghalaya'),
(24, 'Mizoram'),
(25, 'Nagaland'),
(26, 'Odisha'),
(27, 'Puducherry (UT)'),
(28, 'Punjab'),
(29, 'Rajastha'),
(30, 'Sikkim'),
(31, 'Tamil Nadu'),
(32, 'Telangana'),
(33, 'Tripura'),
(34, 'Uttarakhand'),
(35, 'Uttar Pradesh'),
(36, 'West Bengal');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_name` (`admin_name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`cust_id`,`farmer_id`,`crop_id`),
  ADD KEY `farmer_id` (`farmer_id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`Crop_id`),
  ADD UNIQUE KEY `unique_crop_name` (`Crop_name`);

--
-- Indexes for table `custlogin`
--
ALTER TABLE `custlogin`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `idx_farmer_email` (`farmer_email`);

--
-- Indexes for table `disease_details`
--
ALTER TABLE `disease_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disease_id` (`disease_id`);

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`DistCode`),
  ADD KEY `StCode` (`StCode`);

--
-- Indexes for table `farmer`
--
ALTER TABLE `farmer`
  ADD PRIMARY KEY (`farmer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `farmerlogin`
--
ALTER TABLE `farmerlogin`
  ADD PRIMARY KEY (`farmer_id`);

--
-- Indexes for table `farmer_crops_trade`
--
ALTER TABLE `farmer_crops_trade`
  ADD PRIMARY KEY (`trade_id`),
  ADD KEY `farmer_fkid` (`farmer_fkid`);

--
-- Indexes for table `farmer_discussions`
--
ALTER TABLE `farmer_discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_farmer_email` (`farmer_email`);

--
-- Indexes for table `farmer_history`
--
ALTER TABLE `farmer_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_farmerhistory_farmer` (`farmer_id`);

--
-- Indexes for table `farmtube_categories`
--
ALTER TABLE `farmtube_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmtube_comments`
--
ALTER TABLE `farmtube_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `videoId` (`videoId`);

--
-- Indexes for table `farmtube_likes`
--
ALTER TABLE `farmtube_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`videoId`,`userId`,`userType`);

--
-- Indexes for table `farmtube_thumbnails`
--
ALTER TABLE `farmtube_thumbnails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `videoId` (`videoId`);

--
-- Indexes for table `farmtube_videos`
--
ALTER TABLE `farmtube_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `fk_orders_customer` (`customer_id`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitems_order` (`order_id`);

--
-- Indexes for table `plant_diseases`
--
ALTER TABLE `plant_diseases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plant_disease_detection`
--
ALTER TABLE `plant_disease_detection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_email_idx` (`farmer_email`);

--
-- Indexes for table `production_approx`
--
ALTER TABLE `production_approx`
  ADD PRIMARY KEY (`crop`);

--
-- Indexes for table `purchase_history`
--
ALTER TABLE `purchase_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`StCode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `Crop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `custlogin`
--
ALTER TABLE `custlogin`
  MODIFY `cust_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `disease_details`
--
ALTER TABLE `disease_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `DistCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=651;

--
-- AUTO_INCREMENT for table `farmer`
--
ALTER TABLE `farmer`
  MODIFY `farmer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farmerlogin`
--
ALTER TABLE `farmerlogin`
  MODIFY `farmer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `farmer_crops_trade`
--
ALTER TABLE `farmer_crops_trade`
  MODIFY `trade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `farmer_discussions`
--
ALTER TABLE `farmer_discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `farmer_history`
--
ALTER TABLE `farmer_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `farmtube_categories`
--
ALTER TABLE `farmtube_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `farmtube_comments`
--
ALTER TABLE `farmtube_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `farmtube_likes`
--
ALTER TABLE `farmtube_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `farmtube_thumbnails`
--
ALTER TABLE `farmtube_thumbnails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farmtube_videos`
--
ALTER TABLE `farmtube_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `plant_diseases`
--
ALTER TABLE `plant_diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plant_disease_detection`
--
ALTER TABLE `plant_disease_detection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `purchase_history`
--
ALTER TABLE `purchase_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `StCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `custlogin` (`cust_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`farmer_id`) REFERENCES `farmerlogin` (`farmer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`Crop_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `discussion_replies`
--
ALTER TABLE `discussion_replies`
  ADD CONSTRAINT `discussion_replies_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `farmer_discussions` (`id`);

--
-- Constraints for table `disease_details`
--
ALTER TABLE `disease_details`
  ADD CONSTRAINT `disease_details_ibfk_1` FOREIGN KEY (`disease_id`) REFERENCES `plant_diseases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmer_crops_trade`
--
ALTER TABLE `farmer_crops_trade`
  ADD CONSTRAINT `farmer_crops_trade_ibfk_1` FOREIGN KEY (`farmer_fkid`) REFERENCES `farmerlogin` (`farmer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `farmer_history`
--
ALTER TABLE `farmer_history`
  ADD CONSTRAINT `fk_farmerhistory_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmerlogin` (`farmer_id`) ON DELETE CASCADE;

--
-- Constraints for table `farmtube_comments`
--
ALTER TABLE `farmtube_comments`
  ADD CONSTRAINT `farmtube_comments_ibfk_1` FOREIGN KEY (`videoId`) REFERENCES `farmtube_videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmtube_likes`
--
ALTER TABLE `farmtube_likes`
  ADD CONSTRAINT `farmtube_likes_ibfk_1` FOREIGN KEY (`videoId`) REFERENCES `farmtube_videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmtube_thumbnails`
--
ALTER TABLE `farmtube_thumbnails`
  ADD CONSTRAINT `farmtube_thumbnails_ibfk_1` FOREIGN KEY (`videoId`) REFERENCES `farmtube_videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `custlogin` (`cust_id`);

--
-- Constraints for table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `custlogin` (`cust_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_history_ibfk_2` FOREIGN KEY (`farmer_id`) REFERENCES `farmerlogin` (`farmer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_history`
--
ALTER TABLE `purchase_history`
  ADD CONSTRAINT `purchase_history_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `custlogin` (`cust_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
