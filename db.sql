-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2025 at 05:45 AM
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
-- Database: `spk_ahp_rs`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alternatif`
--

INSERT INTO `alternatif` (`id`, `nama`, `created_at`) VALUES
(15, 'Ruang Pendaftaran IGD', '2025-08-01 03:39:34'),
(16, 'Ruang Pendaftaran Ruang Jalan', '2025-08-01 03:39:48'),
(17, 'Ruang Rawat Inap', '2025-08-01 03:40:09');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_ahp`
--

CREATE TABLE `hasil_ahp` (
  `id` int(11) NOT NULL,
  `kriteria_id` int(11) DEFAULT NULL,
  `sub_kriteria_id` int(11) DEFAULT NULL,
  `alternatif_id` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `bobot_kriteria` float DEFAULT NULL,
  `bobot_sub_kriteria` float DEFAULT NULL,
  `bobot_alternatif` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `created_at`) VALUES
(1, 'Sangat Baik', '2025-07-31 14:51:16'),
(2, 'Baik', '2025-07-31 14:51:16'),
(3, 'Buruk', '2025-07-31 14:51:16'),
(4, 'Sangat Buruk', '2025-07-31 14:51:16'),
(5, 'Cukup', '2025-08-01 03:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `kode`, `nama`, `created_at`) VALUES
(5, 'C1', 'Pelayanan Medis', '2025-08-01 02:18:29'),
(6, 'C2', 'Fasilitas Rumah Sakit', '2025-08-01 02:18:47'),
(7, 'C3', 'Biaya dan Administrasi', '2025-08-01 02:19:14'),
(8, 'C4', 'Kepuasan Pasien', '2025-08-01 02:19:30'),
(9, 'C5', 'Kualitas Makanan', '2025-08-01 02:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `perbandingan`
--

CREATE TABLE `perbandingan` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `kriteria1_id` int(11) DEFAULT NULL,
  `kriteria2_id` int(11) DEFAULT NULL,
  `alternatif1_id` int(11) DEFAULT NULL,
  `alternatif2_id` int(11) DEFAULT NULL,
  `nilai` decimal(10,6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perbandingan`
--

INSERT INTO `perbandingan` (`id`, `type`, `kriteria1_id`, `kriteria2_id`, `alternatif1_id`, `alternatif2_id`, `nilai`, `created_at`) VALUES
(32, 'kriteria', 5, 6, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(33, 'kriteria', 5, 7, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(34, 'kriteria', 5, 8, NULL, NULL, 4.000000, '2025-08-01 03:40:17'),
(35, 'kriteria', 5, 9, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(36, 'kriteria', 6, 7, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(37, 'kriteria', 6, 8, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(38, 'kriteria', 6, 9, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(39, 'kriteria', 7, 8, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(40, 'kriteria', 7, 9, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(41, 'kriteria', 8, 9, NULL, NULL, 1.000000, '2025-08-01 03:40:17'),
(42, 'alternatif', 5, NULL, 15, 16, 5.000000, '2025-08-01 03:40:42'),
(43, 'alternatif', 5, NULL, 15, 17, 1.000000, '2025-08-01 03:40:42'),
(44, 'alternatif', 5, NULL, 16, 17, 4.000000, '2025-08-01 03:40:42'),
(45, 'alternatif', 6, NULL, 15, 16, 6.000000, '2025-08-01 03:40:57'),
(46, 'alternatif', 6, NULL, 15, 17, 1.000000, '2025-08-01 03:40:57'),
(47, 'alternatif', 6, NULL, 16, 17, 6.000000, '2025-08-01 03:40:57');

-- --------------------------------------------------------

--
-- Table structure for table `sub_kriteria`
--

CREATE TABLE `sub_kriteria` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kriteria_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kategori_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_kriteria`
--

INSERT INTO `sub_kriteria` (`id`, `nama`, `kriteria_id`, `created_at`, `kategori_id`) VALUES
(18, 'test', 5, '2025-08-01 03:10:34', 2),
(19, 'Kualitas Makanan Test', 6, '2025-08-01 03:10:41', 1),
(20, 'Kualitas Makanan', 6, '2025-08-01 03:11:00', 3),
(21, 'test', 6, '2025-08-01 03:11:07', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hasil_ahp`
--
ALTER TABLE `hasil_ahp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kriteria_id` (`kriteria_id`),
  ADD KEY `sub_kriteria_id` (`sub_kriteria_id`),
  ADD KEY `alternatif_id` (`alternatif_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indexes for table `perbandingan`
--
ALTER TABLE `perbandingan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kriteria_id` (`kriteria_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `hasil_ahp`
--
ALTER TABLE `hasil_ahp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `perbandingan`
--
ALTER TABLE `perbandingan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_ahp`
--
ALTER TABLE `hasil_ahp`
  ADD CONSTRAINT `hasil_ahp_ibfk_1` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_ahp_ibfk_2` FOREIGN KEY (`sub_kriteria_id`) REFERENCES `sub_kriteria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_ahp_ibfk_3` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_ahp_ibfk_4` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_kriteria`
--
ALTER TABLE `sub_kriteria`
  ADD CONSTRAINT `sub_kriteria_ibfk_1` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
