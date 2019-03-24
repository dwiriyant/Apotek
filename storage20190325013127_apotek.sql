-- MySQL dump 10.13  Distrib 5.7.23, for osx10.9 (x86_64)
--
-- Host: localhost    Database: apotek
-- ------------------------------------------------------
-- Server version	5.7.23

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL DEFAULT '',
  `alamat` varchar(255) NOT NULL DEFAULT '',
  `telepon` varchar(15) NOT NULL DEFAULT '',
  `jk` varchar(15) NOT NULL DEFAULT '',
  `tgl_lahir` date DEFAULT NULL,
  `pekerjaan` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dokter`
--

DROP TABLE IF EXISTS `dokter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dokter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(191) NOT NULL,
  `jk` varchar(15) NOT NULL DEFAULT '',
  `tgl_lahir` date DEFAULT NULL,
  `email` varchar(191) NOT NULL DEFAULT '',
  `jenis` varchar(191) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokter`
--

LOCK TABLES `dokter` WRITE;
/*!40000 ALTER TABLE `dokter` DISABLE KEYS */;
/*!40000 ALTER TABLE `dokter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(191) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori`
--

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'Obat Bebas',1,'2019-03-24 17:40:25','2019-03-24 17:40:25');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obat`
--

DROP TABLE IF EXISTS `obat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(191) NOT NULL,
  `kategori` int(1) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,
  `harga_jual_satuan` int(11) NOT NULL DEFAULT '0',
  `harga_jual_resep` int(11) NOT NULL DEFAULT '0',
  `harga_jual_pack` int(11) NOT NULL DEFAULT '0',
  `satuan` varchar(191) NOT NULL DEFAULT '',
  `stok` int(11) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obat`
--

LOCK TABLES `obat` WRITE;
/*!40000 ALTER TABLE `obat` DISABLE KEYS */;
INSERT INTO `obat` VALUES (1,'8991389220054',1,'Paracetamol','2022-03-25',5000,6000,4500,'tablet',214,1,1,'2019-03-24 17:41:34','2019-03-24 18:19:02');
/*!40000 ALTER TABLE `obat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obat_po`
--

DROP TABLE IF EXISTS `obat_po`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obat_po` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `kode` varchar(191) NOT NULL,
  `kategori` int(1) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,
  `satuan` varchar(191) NOT NULL DEFAULT '',
  `stok` int(11) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obat_po`
--

LOCK TABLES `obat_po` WRITE;
/*!40000 ALTER TABLE `obat_po` DISABLE KEYS */;
INSERT INTO `obat_po` VALUES (7,9,'8991389220054',1,'Paracetamol','2022-03-25','tablet',10,0,1,'2019-03-24 18:06:05','2019-03-24 18:06:05');
/*!40000 ALTER TABLE `obat_po` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembelian`
--

DROP TABLE IF EXISTS `pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) DEFAULT NULL,
  `no_transaksi` varchar(191) NOT NULL DEFAULT '',
  `nomor_faktur` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL DEFAULT '0',
  `total_harga` float NOT NULL DEFAULT '0',
  `jenis` varchar(191) NOT NULL DEFAULT 'langsung',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembelian`
--

LOCK TABLES `pembelian` WRITE;
/*!40000 ALTER TABLE `pembelian` DISABLE KEYS */;
INSERT INTO `pembelian` VALUES (9,NULL,'250319125008',NULL,1,5000,'langsung','2019-03-24 18:05:31',1,'2019-03-24 18:06:04','2019-03-24 18:09:51'),(10,NULL,'250319198009',NULL,1,5000,'langsung','2019-03-24 18:06:32',1,'2019-03-24 18:06:42','2019-03-24 18:06:42'),(11,NULL,'250319125008',NULL,1,5000,'langsung','2019-03-24 18:05:31',1,'2019-03-24 18:12:25','2019-03-24 18:12:25'),(12,NULL,'250319111011',NULL,1,0,'langsung','2019-03-24 18:18:21',1,'2019-03-24 18:18:29','2019-03-24 18:18:29');
/*!40000 ALTER TABLE `pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penjualan`
--

DROP TABLE IF EXISTS `penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `penjualan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_konsumen` int(11) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `no_transaksi` varchar(191) NOT NULL DEFAULT '',
  `jumlah` float NOT NULL DEFAULT '0',
  `uang` int(11) NOT NULL,
  `total` int(11) NOT NULL DEFAULT '0',
  `diskon` float NOT NULL DEFAULT '0',
  `total_harga` float NOT NULL DEFAULT '0',
  `jenis` varchar(191) NOT NULL DEFAULT 'reguler',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penjualan`
--

LOCK TABLES `penjualan` WRITE;
/*!40000 ALTER TABLE `penjualan` DISABLE KEYS */;
INSERT INTO `penjualan` VALUES (1,NULL,NULL,'250319192001',1,10000,5000,0,5000,'langsung','2019-03-24 17:43:41',1,'2019-03-24 17:46:36','2019-03-24 17:46:36'),(2,NULL,NULL,'250319159001',1,10000,5000,0,5000,'langsung','2019-03-24 17:47:58',1,'2019-03-24 17:48:09','2019-03-24 17:48:09'),(3,NULL,NULL,'250319183002',1,20000,5000,0,5000,'langsung','2019-03-24 17:48:20',1,'2019-03-24 17:48:30','2019-03-24 17:48:30');
/*!40000 ALTER TABLE `penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retur_pembelian`
--

DROP TABLE IF EXISTS `retur_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retur_pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) NOT NULL,
  `no_transaksi` varchar(255) NOT NULL DEFAULT '',
  `jumlah` int(11) NOT NULL DEFAULT '1',
  `keterangan` varchar(255) NOT NULL DEFAULT '',
  `operator` varchar(60) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retur_pembelian`
--

LOCK TABLES `retur_pembelian` WRITE;
/*!40000 ALTER TABLE `retur_pembelian` DISABLE KEYS */;
INSERT INTO `retur_pembelian` VALUES (1,9,'250319198009',1,'rusak','admin','2019-03-24 18:19:02','2019-03-24 18:19:02');
/*!40000 ALTER TABLE `retur_pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retur_penjualan`
--

DROP TABLE IF EXISTS `retur_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retur_penjualan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) NOT NULL,
  `no_transaksi` varchar(255) NOT NULL DEFAULT '',
  `jumlah` int(11) NOT NULL DEFAULT '1',
  `keterangan` varchar(255) NOT NULL DEFAULT '',
  `operator` varchar(60) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retur_penjualan`
--

LOCK TABLES `retur_penjualan` WRITE;
/*!40000 ALTER TABLE `retur_penjualan` DISABLE KEYS */;
INSERT INTO `retur_penjualan` VALUES (1,1,'250319192001',1,'rusak','admin','2019-03-24 18:18:48','2019-03-24 18:18:48');
/*!40000 ALTER TABLE `retur_penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting_biaya`
--

DROP TABLE IF EXISTS `setting_biaya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setting_biaya` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(11) NOT NULL DEFAULT '',
  `deskripsi` varchar(191) NOT NULL DEFAULT '',
  `periode` date DEFAULT NULL,
  `biaya` int(11) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting_biaya`
--

LOCK TABLES `setting_biaya` WRITE;
/*!40000 ALTER TABLE `setting_biaya` DISABLE KEYS */;
/*!40000 ALTER TABLE `setting_biaya` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stok_opname`
--

DROP TABLE IF EXISTS `stok_opname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stok_opname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_obat` int(11) NOT NULL,
  `stok_software` int(11) NOT NULL,
  `stok_nyata` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL DEFAULT '',
  `operator` varchar(60) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stok_opname`
--

LOCK TABLES `stok_opname` WRITE;
/*!40000 ALTER TABLE `stok_opname` DISABLE KEYS */;
/*!40000 ALTER TABLE `stok_opname` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `kota` varchar(191) NOT NULL,
  `telepon` varchar(191) NOT NULL,
  `no_rekening` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier`
--

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surat_pesanan`
--

DROP TABLE IF EXISTS `surat_pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surat_pesanan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path_surat` varchar(255) NOT NULL DEFAULT '',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surat_pesanan`
--

LOCK TABLES `surat_pesanan` WRITE;
/*!40000 ALTER TABLE `surat_pesanan` DISABLE KEYS */;
/*!40000 ALTER TABLE `surat_pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_pembelian`
--

DROP TABLE IF EXISTS `transaksi_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksi_pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `kode_obat` varchar(191) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_pembelian`
--

LOCK TABLES `transaksi_pembelian` WRITE;
/*!40000 ALTER TABLE `transaksi_pembelian` DISABLE KEYS */;
INSERT INTO `transaksi_pembelian` VALUES (8,9,'8991389220054',5000,1,5000,1,'2019-03-24 18:06:05','2019-03-24 18:09:51'),(9,10,'8991389220054',5000,1,5000,1,'2019-03-24 18:06:42','2019-03-24 18:06:42'),(10,11,'8991389220054',5000,1,5000,1,'2019-03-24 18:12:25','2019-03-24 18:12:25'),(11,12,'8991389220054',0,1,0,1,'2019-03-24 18:18:29','2019-03-24 18:18:29');
/*!40000 ALTER TABLE `transaksi_pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_penjualan`
--

DROP TABLE IF EXISTS `transaksi_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksi_penjualan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `kode_obat` varchar(191) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `jual_pack` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_penjualan`
--

LOCK TABLES `transaksi_penjualan` WRITE;
/*!40000 ALTER TABLE `transaksi_penjualan` DISABLE KEYS */;
INSERT INTO `transaksi_penjualan` VALUES (1,1,'8991389220054',5000,1,5000,0,1,'2019-03-24 17:46:36','2019-03-24 17:46:36'),(2,2,'8991389220054',5000,1,5000,0,1,'2019-03-24 17:48:09','2019-03-24 17:48:09'),(3,3,'8991389220054',5000,1,5000,0,1,'2019-03-24 17:48:30','2019-03-24 17:48:30');
/*!40000 ALTER TABLE `transaksi_penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT '2',
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin','admin@admin.com','',NULL,'$2y$12$uW0P/VD0BPkc7ENdw2AwzuF2o0JtV8uCTqtZvF.AXpI47MBO4z1i6','LNCtsGocqlvz20SUEK6o5T21q5zE92UMM4KKqhElruAkq7HJGWlqVNuncnoj',1,1,'2019-02-12 17:00:00','2019-02-24 06:27:37'),(2,'kasir','kasir','kasir@kasir.com','123123123',NULL,'$2y$10$TAh4C1kWkif/x4zif3cX2.mlkoyj33FJP163hP6qmHmyG3l1QHKtK','H8zS65uYiTIBBMZbMfl7leL8dZRcdnZFAWEbWtLLSPLLtc7HfZlOgvQ6XHNc',2,1,'2019-02-19 04:44:38','2019-02-23 13:42:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'apotek'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-25  1:31:27
