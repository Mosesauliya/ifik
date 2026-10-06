-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_ifik_baru
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aset`
--

DROP TABLE IF EXISTS `aset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aset` (
  `id_aset` int(11) NOT NULL AUTO_INCREMENT,
  `id_ruangan` varchar(64) DEFAULT NULL,
  `nama_aset` varchar(200) NOT NULL,
  `kode_aset` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `jumlah_total` int(11) NOT NULL DEFAULT 0,
  `jumlah_reserved` int(11) NOT NULL DEFAULT 0,
  `jumlah_dipinjam` int(11) NOT NULL DEFAULT 0,
  `jumlah_tersedia` int(11) NOT NULL DEFAULT 0,
  `kondisi` varchar(50) DEFAULT 'Baik',
  `foto` varchar(255) DEFAULT NULL,
  `total_peminjaman` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_aset`),
  UNIQUE KEY `kode_aset` (`kode_aset`),
  KEY `idx_aset_ruangan` (`id_ruangan`),
  KEY `idx_aset_total_peminjaman` (`total_peminjaman`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aset`
--

LOCK TABLES `aset` WRITE;
/*!40000 ALTER TABLE `aset` DISABLE KEYS */;
INSERT INTO `aset` VALUES (28,NULL,'PC Intel I7 2600, RAM 4GB, VGA AMD HD 6670, HDD 500 GB','PC-001','PC untuk editing dan desain grafis',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(29,NULL,'PC Intel I5, RAM 4GB, VGA NVIDIA GEFORCE GT 430','PC-002','PC untuk editing dan desain grafis',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(30,NULL,'PC HP Elitedesk 800 G6, Intel I5 10600K, RAM 16GB, VGA RTX 2060 Super, SSD 500GB','PC-003','PC high-end untuk rendering 3D dan editing video',NULL,20,0,0,20,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(31,NULL,'PC HP Omem, Intel I7 Gen II, RAM 16GB, VGA RTX 3060, SSD 512GB, HDD 2TB','PC-004','PC untuk editing video profesional',NULL,5,0,0,5,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(32,NULL,'PC Intel I5 4570, RAM 4-6GB, VGA GEFORCE 210, HDD 500GB','PC-005','PC untuk desain grafis dasar',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(33,NULL,'PC Intel I5 6600K, RAM 8GB, VGA GTX 750 Ti, HDD 500GB','PC-006','PC untuk multimedia',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(34,NULL,'PC Intel I5 6600, RAM 8GB, VGA Intel HD Graphics 530, HDD 500GB','PC-007','PC untuk multimedia',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(35,NULL,'iMac 2012, Intel I5, RAM 8GB, VGA Radeon Pro 570, HDD 1TB','MAC-001','iMac untuk desain grafis',NULL,19,0,0,19,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(36,NULL,'iMac 2011, Intel I5, RAM 4GB, VGA HD 6750M, HDD 500GB','MAC-002','iMac untuk desain grafis',NULL,7,0,0,7,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(37,NULL,'iMac 2008, Intel Core 2 Duo, RAM 4GB, VGA HD 2600 PRO, HDD 320GB','MAC-003','iMac legacy untuk keperluan dasar',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(38,NULL,'iMac 2013, Intel I5, RAM 8GB, VGA Pro 1536, HDD 1TB','MAC-004','iMac untuk desain grafis',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(39,NULL,'Monitor LG EIGOIS FLATRON','MON-001','Monitor LCD 22 inch',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(40,NULL,'Monitor HP P24','MON-002','Monitor HP 24 inch',NULL,64,0,0,64,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(41,NULL,'Monitor LG Flatron','MON-003','Monitor LG Flatron',NULL,6,0,0,6,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(42,NULL,'Monitor Samsung S22F350FH','MON-004','Monitor Samsung 22 inch',NULL,50,0,0,50,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(43,NULL,'Monitor Dell','MON-005','Monitor Dell 24 inch',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(44,NULL,'Projector','PROJ-001','Proyektor untuk presentasi',NULL,8,0,0,8,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(45,NULL,'Switch Hub 24 Port','SW-001','Switch jaringan 24 port',NULL,3,0,0,3,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(46,NULL,'Switch Hub 16 Port','SW-002','Switch jaringan 16 port',NULL,6,0,0,6,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(47,NULL,'Switch Hub 48 Port','SW-003','Switch jaringan 48 port',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(48,NULL,'Switch Hub 8 Port','SW-004','Switch jaringan 8 port',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(49,NULL,'Keyboard USB','KB-001','Keyboard standar USB',NULL,100,0,0,100,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(50,NULL,'Magic Keyboard','KB-002','Apple Magic Keyboard Wireless',NULL,28,0,0,28,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(51,NULL,'Wired Keyboard','KB-003','Keyboard wired untuk PC',NULL,20,0,0,20,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(52,NULL,'Keyboard PC HP','KB-004','Keyboard HP original',NULL,4,0,0,4,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(53,NULL,'Mouse USB','MS-001','Mouse standar USB',NULL,100,0,0,100,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(54,NULL,'Magic Mouse','MS-002','Apple Magic Mouse Wireless',NULL,28,0,0,28,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(55,NULL,'Wired Mouse','MS-003','Mouse wired untuk PC',NULL,20,0,0,20,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(56,NULL,'Mouse PC HP','MS-004','Mouse HP original',NULL,3,0,0,3,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(57,NULL,'Mouse Logitech','MS-005','Mouse Logitech wireless',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(58,NULL,'Wacom Cintiq 13 HD','WAC-001','Wacom drawing tablet with screen',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(59,NULL,'Wacom Intuos Pro','WAC-002','Wacom professional drawing tablet',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(60,NULL,'Wacom Pen Tablet','WAC-003','Wacom standard pen tablet',NULL,25,0,0,25,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(61,NULL,'Speaker Active','SPK-001','Speaker aktif untuk multimedia',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(62,NULL,'Charger Mac','CHG-001','Charger untuk MacBook/iMac',NULL,14,0,0,14,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(63,NULL,'Port VGA Splitter','VGA-001','Splitter VGA 1 to 2',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(64,NULL,'Studio Flash Godox QS400 II','FOTO-001','Studio flash untuk fotografi produk dan portrait',NULL,4,0,0,4,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(65,NULL,'Studio Flash Tronic Alfa 1000','FOTO-002','Studio flash Tronic Alfa 1000W',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(66,NULL,'Studio Flash Tronic Alfa 300','FOTO-003','Studio flash Tronic Alfa 300W',NULL,1,1,0,0,'Baik',NULL,1,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(67,NULL,'Studio Lighting Godox LED1000Bi II','FOTO-004','LED continuous lighting untuk video dan foto',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(68,NULL,'Softbox','FOTO-005','Softbox untuk difusi cahaya studio',NULL,6,0,0,6,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(69,NULL,'Reflector Flash','FOTO-006','Reflector untuk studio flash',NULL,3,0,0,3,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(70,NULL,'Beauty Dish','FOTO-007','Beauty dish untuk portrait photography',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(71,NULL,'Snoot','FOTO-008','Snoot untuk efek cahaya spot',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(72,NULL,'Table Top','FOTO-009','Meja kecil untuk fotografi produk',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(73,NULL,'Kipas','FOTO-010','Kipas untuk efek rambut atau properti',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(74,NULL,'Cermin','FOTO-011','Cermin untuk properti fotografi',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(75,NULL,'Expander Background','FOTO-012','Background expander untuk backdrop',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(76,NULL,'Portable Cut Off','MTL-001','Mesin potong portable untuk logam',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(77,NULL,'Air Compressor Orange','MTL-002','Kompresor angin portable',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(78,NULL,'Bench Drill','MTL-003','Mesin bor meja untuk logam',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(79,NULL,'Bending Pipa','MTL-004','Alat bending pipa manual',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(80,NULL,'Mesin Las Listrik','MTL-005','Mesin las listrik untuk welding',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(81,NULL,'Mesin Kompressor Besar','MTL-006','Kompresor angin industrial besar',NULL,1,0,0,1,'Rusak Berat',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(82,NULL,'Mesin Las TIG Argon','MTL-007','Mesin las TIG untuk pengelasan presisi',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(83,NULL,'Mesin Kompressor Besar IZUMI','MTL-008','Kompresor angin merk IZUMI',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(84,NULL,'Mesin Bubut Kayu','WOD-001','Mesin bubut untuk kayu',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(85,NULL,'Mesin Bor','WOD-002','Mesin bor meja untuk kayu',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(86,NULL,'Bench Grinder','WOD-003','Mesin gerinda meja',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(87,NULL,'Palm Sander','WOD-004','Mesin amplas genggam',NULL,3,0,1,2,'Baik',NULL,1,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(88,NULL,'Trimer','WOD-005','Mesin trimer untuk finishing edge',NULL,1,1,0,0,'Baik',NULL,1,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(89,NULL,'Angel Drill','WOD-006','Bor sudut untuk area sempit',NULL,1,1,0,0,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 06:10:53'),(90,NULL,'Routher','WOD-007','Mesin router kayu',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(91,NULL,'Cordless','WOD-008','Bor cordless tanpa kabel',NULL,1,1,0,0,'Baik',NULL,1,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(92,NULL,'Mitter Saw','WOD-009','Gergaji mitter untuk potong sudut',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(93,NULL,'Table Saw','WOD-010','Gergaji meja untuk potong kayu',NULL,2,0,0,2,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(94,NULL,'CNC Router','WOD-011','Mesin CNC untuk ukir kayu',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(95,NULL,'Laser 60x40','WOD-012','Mesin laser cutting ukuran 60x40 cm',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(96,NULL,'Jig Saw','WOD-013','Gergaji ukir listrik',NULL,1,0,0,1,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(97,NULL,'Plannet','WOD-014','Mesin planner/penebal kayu',NULL,3,0,0,3,'Baik',NULL,0,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(98,NULL,'Sircular Saw','WOD-015','Gergaji circular portable',NULL,2,1,0,1,'Baik',NULL,1,'2026-10-06 05:05:25','2026-10-06 05:05:25'),(99,NULL,'hdmi','IK1-HDM-775','ssdasdfasdf',NULL,1,0,1,0,'Baik',NULL,1,'2026-10-06 05:05:25','2026-10-06 05:05:25');
/*!40000 ALTER TABLE `aset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_barang`
--

DROP TABLE IF EXISTS `peminjaman_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman_barang` (
  `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(50) DEFAULT NULL,
  `id_aset` int(11) NOT NULL,
  `id_peminjam` int(11) DEFAULT NULL,
  `id_user` varchar(64) DEFAULT NULL,
  `nama_peminjam` varchar(150) DEFAULT NULL,
  `nim_nip` varchar(50) DEFAULT NULL,
  `prodi` varchar(120) DEFAULT NULL,
  `jumlah_pinjam` int(11) NOT NULL DEFAULT 1,
  `stock_allocation_status` varchar(20) NOT NULL DEFAULT 'none',
  `stock_allocated_at` datetime DEFAULT NULL,
  `stock_released_at` datetime DEFAULT NULL,
  `jumlah_kembali` int(11) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_actual` date DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `status` varchar(80) NOT NULL DEFAULT 'Menunggu ACC Kaprodi',
  `status_kaprodi` varchar(20) NOT NULL DEFAULT 'Pending',
  `kaprodi_approval_limit_days` int(11) DEFAULT NULL,
  `kaprodi_deadline_at` datetime DEFAULT NULL,
  `kaprodi_expired_at` datetime DEFAULT NULL,
  `catatan_kaprodi` text DEFAULT NULL,
  `tgl_approve_kaprodi` datetime DEFAULT NULL,
  `id_approver_kaprodi` int(11) DEFAULT NULL,
  `status_laboran` enum('Pending','Disetujui','Ditolak') NOT NULL DEFAULT 'Pending',
  `catatan_laboran` text DEFAULT NULL,
  `tgl_approve_laboran` datetime DEFAULT NULL,
  `id_approver_laboran` varchar(64) DEFAULT NULL,
  `status_kaur` enum('Pending','Disetujui','Ditolak') NOT NULL DEFAULT 'Pending',
  `catatan_kaur` text DEFAULT NULL,
  `tgl_approve_kaur` datetime DEFAULT NULL,
  `id_approver_kaur` varchar(64) DEFAULT NULL,
  `kondisi_saat_pinjam` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT 'Baik',
  `kondisi_saat_kembali` varchar(50) DEFAULT NULL,
  `foto_bukti` varchar(1000) DEFAULT NULL,
  `foto_pengembalian` varchar(255) DEFAULT NULL,
  `qr_locked` tinyint(1) NOT NULL DEFAULT 0,
  `qr_finalized_at` datetime DEFAULT NULL,
  `qr_finalized_by` varchar(64) DEFAULT NULL,
  `qr_pengembalian_token` varchar(64) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_peminjaman`),
  KEY `idx_peminjaman_barang_aset` (`id_aset`),
  KEY `idx_peminjaman_barang_user` (`id_user`),
  KEY `idx_peminjaman_barang_status` (`status`),
  KEY `idx_peminjaman_barang_group` (`group_id`),
  KEY `idx_kaprodi_expiry` (`status`,`status_kaprodi`,`kaprodi_deadline_at`),
  KEY `idx_peminjaman_barang_peminjam` (`id_peminjam`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_barang`
--

LOCK TABLES `peminjaman_barang` WRITE;
/*!40000 ALTER TABLE `peminjaman_barang` DISABLE KEYS */;
INSERT INTO `peminjaman_barang` VALUES (1,'PJM_6ac490edb0c9f',89,1,'admin-laa-01','Admin Layanan Akademik','','S1 Desain Komunikasi Visual (DKV)',1,'reserved','2026-10-06 13:10:53',NULL,NULL,'2026-10-14','2026-12-01',NULL,'test','Menunggu ACC Kaprodi','Pending',4,'2026-10-10 13:10:53',NULL,NULL,NULL,NULL,'Pending',NULL,NULL,NULL,'Pending',NULL,NULL,NULL,'Baik',NULL,'AWAL_1791267053_.jpg',NULL,0,NULL,NULL,NULL,NULL,'2026-10-06 06:10:53','2026-10-06 06:10:53');
/*!40000 ALTER TABLE `peminjaman_barang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_barang_detail`
--

DROP TABLE IF EXISTS `peminjaman_barang_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman_barang_detail` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL DEFAULT 1,
  `kondisi_saat_pinjam` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  KEY `idx_detail_peminjaman` (`id_peminjaman`),
  KEY `idx_detail_aset` (`id_aset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_barang_detail`
--

LOCK TABLES `peminjaman_barang_detail` WRITE;
/*!40000 ALTER TABLE `peminjaman_barang_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `peminjaman_barang_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjam`
--

DROP TABLE IF EXISTS `peminjam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjam` (
  `id_peminjam` int(11) NOT NULL AUTO_INCREMENT,
  `nama_peminjam` varchar(200) NOT NULL,
  `nim_nip` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `jenis` varchar(50) NOT NULL DEFAULT 'Mahasiswa',
  `prodi` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_peminjam`),
  UNIQUE KEY `nim_nip` (`nim_nip`),
  KEY `idx_peminjam_prodi` (`prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjam`
--

LOCK TABLES `peminjam` WRITE;
/*!40000 ALTER TABLE `peminjam` DISABLE KEYS */;
INSERT INTO `peminjam` VALUES (1,'Admin Layanan Akademik','',NULL,NULL,'Mahasiswa','S1 Desain Komunikasi Visual (DKV)','2026-10-06 06:10:53');
/*!40000 ALTER TABLE `peminjam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_evidence`
--

DROP TABLE IF EXISTS `peminjaman_evidence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman_evidence` (
  `id_evidence` int(11) NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int(11) DEFAULT NULL,
  `group_id` varchar(120) DEFAULT NULL,
  `jenis` varchar(40) NOT NULL DEFAULT 'serah_terima',
  `nama_file` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `uploaded_by` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_evidence`),
  KEY `idx_evidence_peminjaman` (`id_peminjaman`),
  KEY `idx_evidence_group` (`group_id`),
  KEY `idx_evidence_jenis` (`jenis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_evidence`
--

LOCK TABLES `peminjaman_evidence` WRITE;
/*!40000 ALTER TABLE `peminjaman_evidence` DISABLE KEYS */;
/*!40000 ALTER TABLE `peminjaman_evidence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_settings`
--

DROP TABLE IF EXISTS `peminjaman_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman_settings` (
  `id_setting` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `kaprodi_approval_days` int(11) NOT NULL DEFAULT 4,
  `schema_version` int(11) NOT NULL DEFAULT 0,
  `updated_by` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_settings`
--

LOCK TABLES `peminjaman_settings` WRITE;
/*!40000 ALTER TABLE `peminjaman_settings` DISABLE KEYS */;
INSERT INTO `peminjaman_settings` VALUES (1,4,3,NULL,'2026-10-06 05:17:48','2026-10-06 12:31:32');
/*!40000 ALTER TABLE `peminjaman_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-06 13:32:36
