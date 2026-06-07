-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: parkir_otomatis
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `kendaraan`
--

DROP TABLE IF EXISTS `kendaraan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_plat` varchar(20) NOT NULL,
  `jenis_kendaraan` enum('Motor','Mobil','Truk') NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  PRIMARY KEY (`id_kendaraan`),
  UNIQUE KEY `nomor_plat` (`nomor_plat`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kendaraan`
--

LOCK TABLES `kendaraan` WRITE;
/*!40000 ALTER TABLE `kendaraan` DISABLE KEYS */;
INSERT INTO `kendaraan` VALUES (1,'AG 1234 RR','Motor','dwi');
/*!40000 ALTER TABLE `kendaraan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parkir`
--

DROP TABLE IF EXISTS `parkir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parkir` (
  `id_parkir` int(11) NOT NULL AUTO_INCREMENT,
  `id_slot` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `plat` varchar(20) DEFAULT NULL,
  `jenis` varchar(20) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `waktu_masuk` datetime NOT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_parkir`),
  KEY `fk_parkir_slot` (`id_slot`),
  KEY `fk_parkir_user` (`id_user`),
  KEY `fk_parkir_kendaraan` (`id_kendaraan`),
  CONSTRAINT `fk_parkir_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`),
  CONSTRAINT `fk_parkir_slot` FOREIGN KEY (`id_slot`) REFERENCES `slot_parkir` (`id_slot`),
  CONSTRAINT `fk_parkir_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parkir`
--

LOCK TABLES `parkir` WRITE;
/*!40000 ALTER TABLE `parkir` DISABLE KEYS */;
INSERT INTO `parkir` VALUES (1,1,1,'AG 1234 RR','Motor','dwi','SP-546P-KEF5','2026-06-05 09:12:25','2026-06-05 10:00:01','Selesai',1);
/*!40000 ALTER TABLE `parkir` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slot_parkir`
--

DROP TABLE IF EXISTS `slot_parkir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `slot_parkir` (
  `id_slot` int(11) NOT NULL AUTO_INCREMENT,
  `kode_slot` varchar(10) NOT NULL,
  `status` enum('Kosong','Terisi') DEFAULT 'Kosong',
  PRIMARY KEY (`id_slot`),
  UNIQUE KEY `kode_slot` (`kode_slot`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slot_parkir`
--

LOCK TABLES `slot_parkir` WRITE;
/*!40000 ALTER TABLE `slot_parkir` DISABLE KEYS */;
INSERT INTO `slot_parkir` VALUES (1,'A01','Kosong'),(2,'A02','Kosong'),(3,'A03','Kosong'),(4,'A04','Kosong'),(5,'A05','Kosong'),(6,'B01','Kosong'),(7,'B02','Kosong'),(8,'B03','Kosong'),(9,'B04','Kosong'),(10,'B05','Kosong');
/*!40000 ALTER TABLE `slot_parkir` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `id_parkir` int(11) NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `metode_bayar` varchar(20) DEFAULT 'Cash',
  `tanggal_bayar` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_transaksi`),
  KEY `fk_transaksi_parkir` (`id_parkir`),
  CONSTRAINT `fk_transaksi_parkir` FOREIGN KEY (`id_parkir`) REFERENCES `parkir` (`id_parkir`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi`
--

LOCK TABLES `transaksi` WRITE;
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
INSERT INTO `transaksi` VALUES (1,1,10000.00,'Cash','2026-06-05 10:00:01');
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Administrator','admin','admin123','admin'),(2,'dwi wahyudi','dwi','$2y$10$XvF87ysVULaHTDOCsXOQiuPHNDbC7w9t7rEeM5.SXF8UwFGmU.y2O','petugas');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'parkir_otomatis'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-05 10:37:47
