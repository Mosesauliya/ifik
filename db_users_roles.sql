-- ============================================================
-- SQL Schema for Roles & Users (IFIK System)
-- ============================================================

USE `db_ifik`;

-- ------------------------------------------------------------
-- 1. Table: roles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `display_name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert 5 Roles
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(1, 'admin',     'Admin System',       'Administrator pengelola sistem dan booking'),
(2, 'laboran',   'Laboran',            'Petugas laboratorium yang memverifikasi peralatan & ruangan'),
(3, 'kaur',      'Kepala Urusan',      'Ka. Ur yang menyetujui peminjaman & kegiatan'),
(4, 'dosen',     'Dosen',              'Dosen pengampu / Dosen Wali'),
(5, 'mahasiswa', 'Mahasiswa',          'Mahasiswa pengguna fasilitas FIK')
ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`);

-- ------------------------------------------------------------
-- 2. Table: users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nidn_nim` VARCHAR(50) NULL,
    `no_hp` VARCHAR(20) NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) 
        REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password Default untuk semua akun: password123
-- Hash Bcrypt: $2y$10$mALt0.edsf7iUAx3mHIyTuCDhsdxAvS8MNfvANy/AG1ZFRNZRSLFy

INSERT INTO `users` (`role_id`, `name`, `email`, `password`, `nidn_nim`, `no_hp`, `status`) VALUES
(1, 'Alif Admin',     'alifadmin@telkomuniversity.ac.id',     '$2y$10$mALt0.edsf7iUAx3mHIyTuCDhsdxAvS8MNfvANy/AG1ZFRNZRSLFy', '10000001', '081234567801', 'active'),
(2, 'Alif Laboran',   'aliflaboran@telkomuniversity.ac.id',   '$2y$10$mALt0.edsf7iUAx3mHIyTuCDhsdxAvS8MNfvANy/AG1ZFRNZRSLFy', '10000002', '081234567802', 'active'),
(3, 'Alif Ka. Ur',    'alifkaur@telkomuniversity.ac.id',      '$2y$10$mALt0.edsf7iUAx3mHIyTuCDhsdxAvS8MNfvANy/AG1ZFRNZRSLFy', '10000003', '081234567803', 'active'),
(4, 'Alif Dosen',     'alifdosen@telkomuniversity.ac.id',     '$2y$10$mALt0.edsf7iUAx3mHIyTuCDhsdxAvS8MNfvANy/AG1ZFRNZRSLFy', '19850101', '081234567804', 'active'),
(5, 'Alif Mahasiswa', 'alifmahasiswa@telkomuniversity.ac.id', '$2y$10$mALt0.edsf7iUAx3mHIyTuCDhsdxAvS8MNfvANy/AG1ZFRNZRSLFy', '1301210001', '081234567805', 'active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `password` = VALUES(`password`);
