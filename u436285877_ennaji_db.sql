-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 10, 2026 at 05:50 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u436285877_ennaji_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(80) DEFAULT '',
  `name` varchar(200) NOT NULL,
  `tag` varchar(50) DEFAULT '',
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT '',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category`, `brand`, `name`, `tag`, `price`, `old_price`, `description`, `features`, `image_url`, `is_featured`, `created_at`) VALUES
(1, 'cameras', 'Hikvision', 'DS-2CD2143G2-I', 'Bestseller', 850.00, 1100.00, NULL, '4MP|IR 30m|IP67|WDR 120dB|H.265+', '', 1, '2026-06-08 22:59:13'),
(2, 'cameras', 'Hikvision', 'DS-2CD2T47G2-L', 'ColorVu', 1200.00, NULL, NULL, '4MP|ColorVu 24/7|IP67|Micro intégré|PoE', '', 1, '2026-06-08 22:59:13'),
(3, 'cameras', 'Dahua', 'IPC-HDW2441T-S', '', 750.00, 900.00, NULL, '4MP|IR 30m|SMD Plus|IP67|H.265+', '', 0, '2026-06-08 22:59:13'),
(4, 'cameras', 'Dahua', 'IPC-HFW3849T1-AS-PV', 'TiOC', 1400.00, NULL, NULL, '8MP|Dissuasion active|Sirène+LED|Full-Color', '', 1, '2026-06-08 22:59:13'),
(5, 'cameras', 'Ezviz', 'C6N Pro', '', 450.00, NULL, NULL, '2MP|Pan & Tilt|Suivi auto|WiFi|Vision nocturne', '', 0, '2026-06-08 22:59:13'),
(6, 'cameras', 'Ezviz', 'C3X', 'Double objectif', 950.00, 1100.00, NULL, '2MP|Double objectif|IA|WiFi|IP67|Audio', '', 0, '2026-06-08 22:59:13'),
(7, 'dvr_nvr', 'Hikvision', 'DS-7608NI-K2/8P', 'PoE intégré', 2800.00, 3200.00, NULL, '8ch|8 PoE|8MP|H.265+|2 SATA|HDMI 4K', '', 1, '2026-06-08 22:59:13'),
(8, 'dvr_nvr', 'Hikvision', 'DS-7204HQHI-K1', '', 1200.00, NULL, NULL, '4ch|Turbo HD|4MP|H.265 Pro+|1 SATA', '', 0, '2026-06-08 22:59:13'),
(9, 'dvr_nvr', 'Dahua', 'NVR4216-16P-4KS2/L', '', 3500.00, NULL, NULL, '16ch|16 PoE|4K|AI|2 SATA|SMD Plus', '', 0, '2026-06-08 22:59:13'),
(10, 'dvr_nvr', 'Dahua', 'XVR5108HS-4KL-I3', '', 2200.00, 2600.00, NULL, '8ch|4K|AI+|H.265+|1 SATA|IoT', '', 0, '2026-06-08 22:59:13'),
(11, 'access', 'ZKTeco', 'ProFace X', 'Premium', 4500.00, NULL, NULL, 'Reconnaissance faciale|Anti-spoofing|Masque|Fièvre|TCP/IP', '', 1, '2026-06-08 22:59:13'),
(12, 'access', 'ZKTeco', 'SpeedFace-V5L', '', 3200.00, 3800.00, NULL, 'Reconnaissance faciale|Paume|5\"|TCP/IP|WiFi', '', 0, '2026-06-08 22:59:13'),
(13, 'access', 'ZKTeco', 'K40 Pro', '', 1800.00, NULL, NULL, 'Empreinte|RFID|Clavier|TCP/IP|USB|3000 users', '', 0, '2026-06-08 22:59:13'),
(14, 'access', 'ZKTeco', 'ZK-AC2260', '', 2200.00, NULL, NULL, '2 portes|Anti-passback|Wiegand|RS485|TCP/IP', '', 0, '2026-06-08 22:59:13'),
(15, 'access', 'Hikvision', 'DS-K1T342MWX', '', 3800.00, NULL, NULL, 'Reconnaissance faciale|MinMoe|WiFi|Lecteur carte|7\"', '', 0, '2026-06-08 22:59:13'),
(16, 'videophone', 'Hikvision', 'DS-KIS602', 'Kit complet', 3200.00, 3800.00, NULL, 'Kit IP|Moniteur 7\"|Caméra 2MP|PoE|App mobile', '', 0, '2026-06-08 22:59:13'),
(17, 'videophone', 'Dahua', 'KTD01(F)', '', 2800.00, NULL, NULL, 'Platine ext.|2MP|Anti-vandale|IP65|Lecteur carte', '', 0, '2026-06-08 22:59:13'),
(18, 'videophone', 'Generic', 'Portier WiFi Smart', '', 1200.00, 1500.00, NULL, 'WiFi|1080p|Vision nocturne|App mobile|2 voies', '', 0, '2026-06-08 22:59:13'),
(19, 'alarme', 'Ajax', 'Hub 2 Plus Kit', 'Premium', 6500.00, NULL, NULL, 'Centrale|2 détecteurs|Clavier|Sirène|4G/WiFi/Ethernet', '', 1, '2026-06-08 22:59:13'),
(20, 'alarme', 'Ajax', 'MotionProtect Plus', '', 850.00, NULL, NULL, 'Détecteur|Anti-animal|Immunité|RF 1700m|5 ans batterie', '', 0, '2026-06-08 22:59:13'),
(21, 'alarme', 'Hikvision', 'AX PRO Kit', '', 4200.00, 4800.00, NULL, 'Centrale|3 détecteurs|Sirène|WiFi/4G|App Hik-Connect', '', 0, '2026-06-08 22:59:13'),
(22, 'reseau', 'TP-Link', 'EAP670', 'WiFi 6', 2200.00, NULL, NULL, 'WiFi 6 AX|3600Mbps|PoE|Omada|MU-MIMO|160MHz', '', 0, '2026-06-08 22:59:13'),
(23, 'reseau', 'TP-Link', 'TL-SG1016PE', '', 2400.00, 2800.00, NULL, 'Switch 16 ports|PoE+ 150W|Gigabit|Rackable 19\"', '', 0, '2026-06-08 22:59:13'),
(24, 'reseau', 'Ubiquiti', 'U6-Pro', 'Pro', 3200.00, NULL, NULL, 'WiFi 6|5.3Gbps|PoE|UniFi|160MHz|300+ clients', '', 0, '2026-06-08 22:59:13'),
(25, 'reseau', 'Ubiquiti', 'USW-Pro-24-PoE', '', 5500.00, NULL, NULL, 'Switch 24 ports|PoE+|400W|10G SFP+|Layer 3|UniFi', '', 0, '2026-06-08 22:59:13'),
(26, 'pc', 'Dell', 'OptiPlex 7010 Micro', '', 8500.00, NULL, NULL, 'i5-13500T|16GB|512GB SSD|WiFi 6E|Windows 11 Pro', '', 0, '2026-06-08 22:59:13'),
(27, 'pc', 'Lenovo', 'ThinkPad E14 Gen 5', '', 9800.00, 11000.00, NULL, 'i7-1355U|16GB|512GB|14\" FHD|Clavier FR|Fingerprint', '', 0, '2026-06-08 22:59:13'),
(28, 'pc', 'Apple', 'Mac Mini M2', 'Populaire', 7200.00, NULL, NULL, 'Apple M2|8GB|256GB SSD|macOS|Compact', '', 0, '2026-06-08 22:59:13'),
(29, 'cablage', 'Generic', 'Câble CAT6 UTP 305m', '', 1200.00, NULL, NULL, 'Cat6|UTP|Cuivre|305m|23AWG|Boîte déroulante', '', 0, '2026-06-08 22:59:13'),
(30, 'cablage', 'Generic', 'Kit Rack 6U mural', '', 1800.00, 2100.00, NULL, 'Rack 6U|Mural|600x450mm|Ventilé|Serrure|Noir', '', 0, '2026-06-08 22:59:13'),
(31, 'cameras', 'Dahua', 'IPC-HDW1230T1-S5', 'Prix Rabat-Sale', 520.00, 650.00, 'Camera dome IP Dahua pour boutique, bureau et maison.', '2MP|IR 30m|IP67|H.265|PoE', '', 1, '2026-06-08 22:59:22'),
(32, 'cameras', 'Dahua', 'IPC-HFW1431S1-S4', 'Pro', 690.00, 850.00, 'Camera tube IP Dahua robuste pour parking, facade et depot.', '4MP|IR 30m|WDR|IP67|PoE', '', 1, '2026-06-08 22:59:22'),
(33, 'cameras', 'Dahua', 'IPC-HDW3849H-AS-PV', 'TiOC', 1650.00, 1900.00, 'Camera Dahua active deterrence avec lumiere, sirene et audio.', '8MP|Full-Color|Sirene|Micro|IA', '', 1, '2026-06-08 22:59:22'),
(34, 'cameras', 'Serto', 'ST-IP4M-DOME', 'Economique', 390.00, NULL, 'Camera IP Serto 4MP pour installation simple et budget maitrise.', '4MP|IR|IP66|H.265|Mobile', '', 0, '2026-06-08 22:59:22'),
(35, 'cameras', 'Serto', 'ST-WIFI-PTZ', 'WiFi', 480.00, 590.00, 'Camera WiFi motorisee pour maison, commerce et surveillance interieure.', 'WiFi|PTZ|Audio|Vision nocturne|Application mobile', '', 0, '2026-06-08 22:59:22'),
(36, 'dvr_nvr', 'Dahua', 'NVR4108HS-8P-4KS2/L', '8 PoE', 2450.00, 2850.00, 'Enregistreur Dahua 8 canaux PoE pour installation cameras IP.', '8ch|8 PoE|4K|H.265+|1 SATA|Mobile', '', 1, '2026-06-08 22:59:22'),
(37, 'dvr_nvr', 'Serto', 'ST-NVR08P', 'Pack camera', 1450.00, NULL, 'NVR Serto pour petits projets de surveillance.', '8ch|PoE|H.265|HDMI|Application', '', 0, '2026-06-08 22:59:22'),
(38, 'access', 'Serto', 'ST-AC100 RFID', 'Porte acces', 950.00, 1200.00, 'Lecteur de controle d acces Serto pour porte bureau ou local.', 'RFID|Clavier|Relais porte|12V|Anti-arrachement', '', 1, '2026-06-08 22:59:22'),
(39, 'access', 'Serto', 'ST-FACE200', 'Facial', 2100.00, 2500.00, 'Terminal facial Serto pour entree personnel et pointage.', 'Reconnaissance faciale|RFID|TCP/IP|USB|Rapports', '', 1, '2026-06-08 22:59:22'),
(40, 'access', 'ZKTeco', 'F18', 'Empreinte', 2400.00, 2800.00, 'Terminal ZKTeco fiable pour controle d acces professionnel.', 'Empreinte|RFID|TCP/IP|Wiegand|3000 empreintes', '', 0, '2026-06-08 22:59:22'),
(41, 'videophone', 'Dahua', 'VTO2202F-P + VTH2421FW-P', 'Kit IP', 3600.00, 4200.00, 'Kit portier video IP Dahua pour villa, cabinet ou bureau.', 'Platine IP|Moniteur 7 pouces|PoE|App mobile|RFID', '', 1, '2026-06-08 22:59:22'),
(42, 'videophone', 'Serto', 'ST-VD7-WIFI', 'Portier', 1350.00, 1600.00, 'Portier video WiFi avec moniteur pour maison et petit commerce.', 'Ecran 7 pouces|WiFi|Vision nocturne|Ouverture porte', '', 0, '2026-06-08 22:59:22'),
(43, 'reseau', 'TP-Link', 'TL-SG1008MP', 'PoE+', 1350.00, 1600.00, 'Switch PoE pour cameras IP et points d acces WiFi.', '8 ports Gigabit|PoE+|153W|Plug and play', '', 1, '2026-06-08 22:59:22'),
(44, 'reseau', 'TP-Link', 'ER605 Omada', 'VPN', 1050.00, NULL, 'Routeur professionnel pour bureaux, VPN et gestion multi-WAN.', 'VPN|Multi-WAN|Omada|Firewall|Gigabit', '', 0, '2026-06-08 22:59:22'),
(45, 'cablage', 'Generic', 'Installation point reseau RJ45', 'Service', 250.00, NULL, 'Point reseau complet pour camera, poste de travail ou imprimante.', 'Cable CAT6|Prise murale|Testeur|Etiquetage', '', 0, '2026-06-08 22:59:22'),
(46, 'alarme', 'Ajax', 'DoorProtect', 'Ouverture', 520.00, NULL, 'Detecteur ouverture porte/fenetre pour systeme Ajax.', 'Sans fil|Anti-sabotage|Batterie longue duree|Application', '', 0, '2026-06-08 22:59:22'),
(47, 'cameras', 'Hikvision', 'DS-2CD1043G2-LIU', 'ColorVu', 980.00, 1150.00, 'Camera Hikvision ColorVu avec audio pour surveillance exterieure.', '4MP|ColorVu|Micro|IP67|H.265+', '', 0, '2026-06-09 18:10:22'),
(48, 'access', 'Dahua', 'ASI6213J-FT', 'Facial Pro', 3600.00, 4100.00, 'Terminal Dahua avec reconnaissance faciale et controle de temperature selon disponibilite.', 'Facial|RFID|TCP/IP|Ecran tactile|Wiegand', '', 0, '2026-06-09 18:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `slug`, `description`, `is_active`) VALUES
(1, 'Création de Sites Web', 'web', 'Sites vitrine, e-commerce et applications web sur mesure', 1),
(2, 'Matériel Informatique', 'materiel', 'Vente et installation de matériel IT professionnel', 1),
(3, 'Agents IA', 'ia', 'Solutions d\'intelligence artificielle et automatisation', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
