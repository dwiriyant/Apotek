# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.23)
# Database: apotek
# Generation Time: 2020-07-22 14:59:07 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;

INSERT INTO `config` (`id`, `nama`, `value`, `created_at`, `updated_at`)
VALUES
	(1,'stok_minimal',30,'2020-07-19 17:31:05','2020-07-19 20:51:46');

/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table customer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `customer`;

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



# Dump of table dokter
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dokter`;

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



# Dump of table kategori
# ------------------------------------------------------------

DROP TABLE IF EXISTS `kategori`;

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(191) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;

INSERT INTO `kategori` (`id`, `nama`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'Obat Bebasss',1,'2019-03-25 00:40:25','2019-03-25 00:40:25');

/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table obat
# ------------------------------------------------------------

DROP TABLE IF EXISTS `obat`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `obat` WRITE;
/*!40000 ALTER TABLE `obat` DISABLE KEYS */;

INSERT INTO `obat` (`id`, `kode`, `kategori`, `nama`, `tgl_kadaluarsa`, `harga_jual_satuan`, `harga_jual_resep`, `harga_jual_pack`, `satuan`, `stok`, `type`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'8991389220054',1,'Obatssz asdfasdf sdd asdsdf sdfsdfdf','2022-03-27',10001,10002,10003,'tablet',527,1,1,'2019-03-25 00:41:34','2020-06-29 19:54:13'),
	(2,'111111111',1,'Obat kepala','2023-06-29',100000,100000,1000000,'biji/pc',10,1,1,'2020-06-29 19:48:53','2020-07-19 21:02:40'),
	(3,'2222222222',1,'Obat Kaki','2023-06-29',100000,100000,10000,'biji/pc',5,1,1,'2020-06-29 19:54:46','2020-06-29 19:54:46');

/*!40000 ALTER TABLE `obat` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table obat_po
# ------------------------------------------------------------

DROP TABLE IF EXISTS `obat_po`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `obat_po` WRITE;
/*!40000 ALTER TABLE `obat_po` DISABLE KEYS */;

INSERT INTO `obat_po` (`id`, `id_pembelian`, `kode`, `kategori`, `nama`, `tgl_kadaluarsa`, `satuan`, `stok`, `type`, `status`, `created_at`, `updated_at`)
VALUES
	(7,9,'8991389220054',1,'Paracetamol','2022-03-25','tablet',10,0,1,'2019-03-25 01:06:05','2019-03-25 01:06:05'),
	(8,13,'2222222222',1,'Obat Kaki','2023-06-29','biji/pc',100,0,1,'2020-06-29 19:56:31','2020-06-29 19:56:31'),
	(9,14,'2222222222',1,'Obat Kaki','2023-07-19','biji/pc',1,0,1,'2020-07-19 21:14:56','2020-07-19 21:14:56'),
	(10,14,'111111111',1,'Obat kepala','2023-07-19','biji/pc',1,0,1,'2020-07-19 21:14:56','2020-07-19 21:14:56');

/*!40000 ALTER TABLE `obat_po` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table pembelian
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pembelian`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `pembelian` WRITE;
/*!40000 ALTER TABLE `pembelian` DISABLE KEYS */;

INSERT INTO `pembelian` (`id`, `id_supplier`, `no_transaksi`, `nomor_faktur`, `jumlah`, `total_harga`, `jenis`, `tanggal`, `status`, `created_at`, `updated_at`)
VALUES
	(9,NULL,'250319125008',NULL,1,5000,'langsung','2019-03-25 01:05:31',1,'2019-03-25 01:06:04','2019-03-25 01:09:51'),
	(10,NULL,'250319198009',NULL,1,5000,'langsung','2019-03-25 01:06:32',1,'2019-03-25 01:06:42','2019-03-25 01:06:42'),
	(11,NULL,'250319125008',NULL,1,5000,'langsung','2019-03-25 01:05:31',1,'2019-03-25 01:12:25','2019-03-25 01:12:25'),
	(12,NULL,'250319111011',NULL,1,0,'langsung','2019-03-25 01:18:21',1,'2019-03-25 01:18:29','2019-03-25 01:18:29'),
	(13,NULL,'290620190012',NULL,200,0,'po','2020-06-29 19:55:47',2,'2020-06-29 19:56:31','2020-06-29 19:56:31'),
	(14,NULL,'190720198013',NULL,2,0,'po','2020-07-19 21:14:22',2,'2020-07-19 21:14:56','2020-07-19 21:14:56');

/*!40000 ALTER TABLE `pembelian` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table penjualan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `penjualan`;

CREATE TABLE `penjualan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_konsumen` int(11) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `no_transaksi` varchar(191) NOT NULL DEFAULT '',
  `jumlah` float NOT NULL DEFAULT '0',
  `uang` int(11) NOT NULL,
  `total` int(11) NOT NULL DEFAULT '0',
  `biaya_jasa` int(11) DEFAULT '0',
  `diskon` float NOT NULL DEFAULT '0',
  `total_harga` float NOT NULL DEFAULT '0',
  `jenis` varchar(191) NOT NULL DEFAULT 'reguler',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `penjualan` WRITE;
/*!40000 ALTER TABLE `penjualan` DISABLE KEYS */;

INSERT INTO `penjualan` (`id`, `id_konsumen`, `id_dokter`, `no_transaksi`, `jumlah`, `uang`, `total`, `biaya_jasa`, `diskon`, `total_harga`, `jenis`, `tanggal`, `status`, `created_at`, `updated_at`)
VALUES
	(1,NULL,NULL,'250319192001',1,10000,5000,0,0,5000,'langsung','2019-03-25 00:43:41',1,'2019-03-25 00:46:36','2019-03-25 00:46:36'),
	(2,NULL,NULL,'250319159001',1,10000,5000,0,0,5000,'langsung','2019-03-25 00:47:58',1,'2019-03-25 00:48:09','2019-03-25 00:48:09'),
	(3,NULL,NULL,'250319183002',1,20000,5000,0,0,5000,'langsung','2019-03-25 00:48:20',1,'2019-03-25 00:48:30','2019-03-25 00:48:30'),
	(4,NULL,NULL,'260120151003',1,0,10001,0,0,10001,'langsung','2020-01-26 12:32:24',1,'2020-01-26 12:32:36','2020-01-26 12:32:36'),
	(5,NULL,NULL,'180720166004',1,0,95000,0,0,95000,'langsung','2020-07-18 22:46:32',1,'2020-07-18 22:47:45','2020-07-18 22:47:45'),
	(6,NULL,NULL,'190720117005',1,0,98000,0,0,98000,'resep','2020-07-19 15:57:40',1,'2020-07-19 16:01:57','2020-07-19 16:01:57'),
	(7,NULL,NULL,'190720118006',1,0,99000,0,0,99000,'resep','2020-07-19 16:03:15',1,'2020-07-19 16:03:32','2020-07-19 16:03:32'),
	(8,NULL,NULL,'190720159007',1,0,99000,0,0,99000,'resep','2020-07-19 16:07:21',1,'2020-07-19 16:07:31','2020-07-19 16:07:31'),
	(9,NULL,NULL,'190720124008',1,0,98000,0,0,98000,'resep','2020-07-19 16:09:12',1,'2020-07-19 16:09:39','2020-07-19 16:09:39'),
	(10,NULL,NULL,'190720124008',1,0,98000,0,0,98000,'resep','2020-07-19 16:09:12',1,'2020-07-19 16:11:27','2020-07-19 16:11:27'),
	(11,NULL,NULL,'190720159010',1,0,98000,0,0,98000,'resep','2020-07-19 16:12:48',1,'2020-07-19 16:13:00','2020-07-19 16:13:00'),
	(12,NULL,NULL,'190720187011',1,0,90000,0,0,90000,'resep','2020-07-19 16:13:35',1,'2020-07-19 16:13:47','2020-07-19 16:13:47'),
	(13,NULL,NULL,'190720116012',1,0,100000,5000,0,105000,'resep','2020-07-19 16:29:44',1,'2020-07-19 16:29:56','2020-07-19 16:29:56');

/*!40000 ALTER TABLE `penjualan` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table retur_pembelian
# ------------------------------------------------------------

DROP TABLE IF EXISTS `retur_pembelian`;

CREATE TABLE `retur_pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_transaksi` int(11) NOT NULL,
  `no_transaksi` varchar(255) NOT NULL DEFAULT '',
  `jumlah` int(11) NOT NULL DEFAULT '1',
  `ppn` int(11) NOT NULL DEFAULT '0',
  `keterangan` varchar(255) NOT NULL DEFAULT '',
  `operator` varchar(60) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `retur_pembelian` WRITE;
/*!40000 ALTER TABLE `retur_pembelian` DISABLE KEYS */;

INSERT INTO `retur_pembelian` (`id`, `id_transaksi`, `no_transaksi`, `jumlah`, `keterangan`, `operator`, `created_at`, `updated_at`)
VALUES
	(1,9,'250319198009',1,'rusak','admin','2019-03-25 01:19:02','2019-03-25 01:19:02');

/*!40000 ALTER TABLE `retur_pembelian` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table retur_penjualan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `retur_penjualan`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `retur_penjualan` WRITE;
/*!40000 ALTER TABLE `retur_penjualan` DISABLE KEYS */;

INSERT INTO `retur_penjualan` (`id`, `id_transaksi`, `no_transaksi`, `jumlah`, `keterangan`, `operator`, `created_at`, `updated_at`)
VALUES
	(1,1,'250319192001',1,'rusak','admin','2019-03-25 01:18:48','2019-03-25 01:18:48');

/*!40000 ALTER TABLE `retur_penjualan` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table setting_biaya
# ------------------------------------------------------------

DROP TABLE IF EXISTS `setting_biaya`;

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



# Dump of table stok_opname
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stok_opname`;

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



# Dump of table supplier
# ------------------------------------------------------------

DROP TABLE IF EXISTS `supplier`;

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



# Dump of table surat_pesanan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `surat_pesanan`;

CREATE TABLE `surat_pesanan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path_surat` varchar(255) NOT NULL DEFAULT '',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table toko
# ------------------------------------------------------------

DROP TABLE IF EXISTS `toko`;

CREATE TABLE `toko` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `toko` WRITE;
/*!40000 ALTER TABLE `toko` DISABLE KEYS */;

INSERT INTO `toko` (`id`, `nama`, `alamat`, `no_telp`)
VALUES
	(1,'APOTEK BATU SEHAT','Jl. Brantas 24 Batuz','z0341 - 511303 / 081234073427');

/*!40000 ALTER TABLE `toko` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table transaksi_pembelian
# ------------------------------------------------------------

DROP TABLE IF EXISTS `transaksi_pembelian`;

CREATE TABLE `transaksi_pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `kode_obat` varchar(191) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `diskon` int(11) DEFAULT '0',
  `total_harga` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `transaksi_pembelian` WRITE;
/*!40000 ALTER TABLE `transaksi_pembelian` DISABLE KEYS */;

INSERT INTO `transaksi_pembelian` (`id`, `id_pembelian`, `kode_obat`, `total`, `jumlah`, `total_harga`, `status`, `created_at`, `updated_at`)
VALUES
	(8,9,'8991389220054',5000,1,5000,1,'2019-03-25 01:06:05','2019-03-25 01:09:51'),
	(9,10,'8991389220054',5000,1,5000,1,'2019-03-25 01:06:42','2019-03-25 01:06:42'),
	(10,11,'8991389220054',5000,1,5000,1,'2019-03-25 01:12:25','2019-03-25 01:12:25'),
	(11,12,'8991389220054',0,1,0,1,'2019-03-25 01:18:29','2019-03-25 01:18:29'),
	(12,13,'111111111',0,100,0,1,'2020-06-29 19:56:31','2020-06-29 19:56:31'),
	(13,14,'2222222222',0,1,0,2,'2020-07-19 21:14:56','2020-07-19 21:14:56'),
	(14,14,'111111111',0,1,0,2,'2020-07-19 21:14:56','2020-07-19 21:14:56');

/*!40000 ALTER TABLE `transaksi_pembelian` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table transaksi_penjualan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `transaksi_penjualan`;

CREATE TABLE `transaksi_penjualan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `kode_obat` varchar(191) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `diskon` int(11) DEFAULT '0',
  `total_harga` int(11) NOT NULL,
  `jual_pack` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `transaksi_penjualan` WRITE;
/*!40000 ALTER TABLE `transaksi_penjualan` DISABLE KEYS */;

INSERT INTO `transaksi_penjualan` (`id`, `id_penjualan`, `kode_obat`, `total`, `jumlah`, `diskon`, `total_harga`, `jual_pack`, `status`, `created_at`, `updated_at`)
VALUES
	(1,1,'8991389220054',5000,1,0,5000,0,1,'2019-03-25 00:46:36','2019-03-25 00:46:36'),
	(2,2,'8991389220054',5000,1,0,5000,0,1,'2019-03-25 00:48:09','2019-03-25 00:48:09'),
	(3,3,'8991389220054',5000,1,0,5000,0,1,'2019-03-25 00:48:30','2019-03-25 00:48:30'),
	(4,3,'8991389220054',5000,1,0,5000,0,1,'2019-03-25 00:48:30','2019-03-25 00:48:30'),
	(5,4,'8991389220054',10001,1,0,10001,0,1,'2020-01-26 12:32:36','2020-01-26 12:32:36'),
	(6,5,'111111111',95000,1,0,95000,0,1,'2020-07-18 22:47:45','2020-07-18 22:47:45'),
	(7,8,'111111111',99000,1,1000,99000,0,1,'2020-07-19 16:07:31','2020-07-19 16:07:31'),
	(8,9,'111111111',98000,1,2000,98000,0,1,'2020-07-19 16:09:39','2020-07-19 16:09:39'),
	(9,10,'111111111',98000,1,2000,98000,0,1,'2020-07-19 16:11:27','2020-07-19 16:11:27'),
	(10,11,'111111111',98000,1,2000,98000,0,1,'2020-07-19 16:13:01','2020-07-19 16:13:01'),
	(11,12,'111111111',90000,1,10000,90000,0,1,'2020-07-19 16:13:48','2020-07-19 16:13:48'),
	(12,13,'111111111',100000,1,0,100000,0,1,'2020-07-19 16:29:57','2020-07-19 16:29:57');

/*!40000 ALTER TABLE `transaksi_penjualan` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `level`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'admin','admin','admin@admin.com','',NULL,'$2a$12$E6BCi9K1DVeGfqk03Qa07.jOODI9uM5i/YtOzROOMHQN6i/zVB2Uq',1,1,'2019-02-13 00:00:00','2019-12-19 18:45:10'),
	(2,'kasir','kasir','kasir@kasir.com','123123123',NULL,'$2a$12$suMAs5mgiuO4p/nqf2SWgO8VMBuqnz6Fea4AglPsK.Tpa9r9nndqi','H8zS65uYiTIBBMZbMfl7leL8dZRcdnZFAWEbWtLLSPLLtc7HfZlOgvQ6XHNc',2,1,'2019-02-19 11:44:38','2019-02-23 20:42:03');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
