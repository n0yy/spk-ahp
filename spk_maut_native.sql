CREATE DATABASE spk_ahp_rs;
USE spk_ahp_rs;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

CREATE TABLE IF NOT EXISTS `pengguna` (
    `id_pengguna` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `email` VARCHAR(50) DEFAULT NULL,
    `role` ENUM('admin', 'staf') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `nama`, `email`, `role`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 'admin@gmail.com', 'admin'),
(2, 'staf', 'ee11cbb19052e40b07aac0ca060c23ee', 'Staf', 'staf@gmail.com', 'staf');

CREATE TABLE IF NOT EXISTS `kriteria` (
    `id_kriteria` INT(11) NOT NULL AUTO_INCREMENT,
    `kode_kriteria` VARCHAR(10) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT DEFAULT NULL,
    `id_induk` INT(11) DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_kriteria`),
    FOREIGN KEY (`id_induk`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kriteria` (`id_kriteria`, `kode_kriteria`, `nama`, `deskripsi`) VALUES
(1, 'C1', 'Fasilitas Kamar', 'Comfort and cleanliness of patient rooms'),
(2, 'C2', 'Pelayanan Medis', 'Responsiveness and professionalism of medical personnel'),
(3, 'C3', 'Administrasi', 'Efficiency of the administrative process');

CREATE TABLE IF NOT EXISTS `alternatif` (
    `id_alternatif` INT(11) NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_alternatif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `alternatif` (`id_alternatif`, `nama`, `deskripsi`) VALUES
(1, 'Tingkatkan Kebersihan Kamar', 'Improve cleanliness standards in patient rooms'),
(2, 'Percepat Respons Medis', 'Improve the speed of response from medical staff'),
(3, 'Sederhanakan Administrasi', 'Simplify the patient administrative process'),
(4, 'Tingkatkan Fasilitas Kamar', 'Add comfort facilities in patient rooms'),
(5, 'Pelatihan Staf Medis', 'Train medical staff for better service'),
(6, 'Digitalisasi Administrasi', 'Use digital systems for administration');

CREATE TABLE IF NOT EXISTS `perbandingan_berpasangan` (
    `id_perbandingan` INT(11) NOT NULL AUTO_INCREMENT,
    `id_kriteria_1` INT(11) NOT NULL,
    `id_kriteria_2` INT(11) NOT NULL,
    `nilai` FLOAT NOT NULL,
    `id_pengguna` INT(11) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_perbandingan`),
    FOREIGN KEY (`id_kriteria_1`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE CASCADE,
    FOREIGN KEY (`id_kriteria_2`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE CASCADE,
    FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `perbandingan_berpasangan` (`id_perbandingan`, `id_kriteria_1`, `id_kriteria_2`, `nilai`, `id_pengguna`) VALUES
(1, 1, 1, 1, 1),
(2, 1, 2, 3, 1),
(3, 1, 3, 5, 1),
(4, 2, 1, 0.333, 1),
(5, 2, 2, 1, 1),
(6, 2, 3, 3, 1),
(7, 3, 1, 0.2, 1),
(8, 3, 2, 0.333, 1),
(9, 3, 3, 1, 1);

CREATE TABLE IF NOT EXISTS `perbandingan_alternatif` (
    `id_perbandingan` INT(11) NOT NULL AUTO_INCREMENT,
    `id_kriteria` INT(11) NOT NULL,
    `id_alternatif_1` INT(11) NOT NULL,
    `id_alternatif_2` INT(11) NOT NULL,
    `nilai` FLOAT NOT NULL,
    `id_pengguna` INT(11) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_perbandingan`),
    FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE CASCADE,
    FOREIGN KEY (`id_alternatif_1`) REFERENCES `alternatif` (`id_alternatif`) ON DELETE CASCADE,
    FOREIGN KEY (`id_alternatif_2`) REFERENCES `alternatif` (`id_alternatif`) ON DELETE CASCADE,
    FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `perbandingan_alternatif` (`id_perbandingan`, `id_kriteria`, `id_alternatif_1`, `id_alternatif_2`, `nilai`, `id_pengguna`) VALUES
(1, 1, 1, 1, 1, 1),
(2, 1, 1, 2, 3, 1),
(3, 1, 1, 3, 5, 1),
(4, 1, 2, 1, 0.333, 1),
(5, 1, 2, 2, 1, 1),
(6, 1, 2, 3, 2, 1),
(7, 1, 3, 1, 0.2, 1),
(8, 1, 3, 2, 0.5, 1),
(9, 1, 3, 3, 1, 1);

CREATE TABLE IF NOT EXISTS `hasil_ahp` (
    `id_hasil` INT(11) NOT NULL AUTO_INCREMENT,
    `jumlah_kriteria` INT(11) NOT NULL,
    `bobot_kriteria` TEXT NOT NULL,
    `lambda_max` FLOAT NOT NULL,
    `ci` FLOAT NOT NULL,
    `cr` FLOAT NOT NULL,
    `id_pengguna` INT(11) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_hasil`),
    FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hasil_ahp` (`id_hasil`, `jumlah_kriteria`, `bobot_kriteria`, `lambda_max`, `ci`, `cr`, `id_pengguna`) VALUES
(1, 3, '[0.633, 0.260, 0.106]', 3.053, 0.0265, 0.0457, 1);

CREATE TABLE IF NOT EXISTS `hasil_alternatif` (
    `id_hasil` INT(11) NOT NULL AUTO_INCREMENT,
    `id_kriteria` INT(11) NOT NULL,
    `id_alternatif` INT(11) NOT NULL,
    `bobot` FLOAT NOT NULL,
    `id_pengguna` INT(11) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_hasil`),
    FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE CASCADE,
    FOREIGN KEY (`id_alternatif`) REFERENCES `alternatif` (`id_alternatif`) ON DELETE CASCADE,
    FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hasil_alternatif` (`id_hasil`, `id_kriteria`, `id_alternatif`, `bobot`, `id_pengguna`) VALUES
(1, 1, 1, 0.633, 1),
(2, 1, 2, 0.260, 1),
(3, 1, 3, 0.106, 1);