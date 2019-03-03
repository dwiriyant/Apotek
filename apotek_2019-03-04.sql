# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.23)
# Database: apotek
# Generation Time: 2019-03-03 22:44:54 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


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

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;

INSERT INTO `customer` (`id`, `nama`, `alamat`, `telepon`, `jk`, `tgl_lahir`, `pekerjaan`, `email`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'Paijo','Malang','08213237733','Laki laki','1994-05-16','Swasta','paijo@gmail.com',1,'2019-02-24 05:53:08','2019-02-24 05:53:08');

/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;


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

LOCK TABLES `dokter` WRITE;
/*!40000 ALTER TABLE `dokter` DISABLE KEYS */;

INSERT INTO `dokter` (`id`, `nama`, `alamat`, `telepon`, `jk`, `tgl_lahir`, `email`, `jenis`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'Wildan','Malang','082317373935','Laki laki','1990-01-30','wildan@gmail.com','Umum',1,'2019-02-24 05:55:24','2019-02-24 05:55:24');

/*!40000 ALTER TABLE `dokter` ENABLE KEYS */;
UNLOCK TABLES;


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
	(1,'Obat Bebas',1,'2019-02-23 04:48:29','2019-02-23 07:20:11');

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
  `satuan` varchar(191) NOT NULL DEFAULT '',
  `stok` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `obat` WRITE;
/*!40000 ALTER TABLE `obat` DISABLE KEYS */;

INSERT INTO `obat` (`id`, `kode`, `kategori`, `nama`, `tgl_kadaluarsa`, `harga_jual_satuan`, `harga_jual_resep`, `satuan`, `stok`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'8992753700103',1,'Paracetamolz','2022-02-23',20000,21000,'botol',15,1,'2019-02-23 07:32:30','2019-03-02 22:24:11'),
	(2,'4987176002679',1,'Vick Formula','2022-03-02',31000,32000,'botol',10,1,'2019-03-02 21:33:33','2019-03-02 21:33:33'),
	(3,'4987176002672',1,'Vick Formula','2022-03-03',0,0,'botol',20,1,'2019-03-03 23:40:11','2019-03-04 05:20:57'),
	(4,'4987176002671',1,'Bodrex','2022-03-03',0,0,'tablet',35,1,'2019-03-03 23:40:11','2019-03-04 05:21:02');

/*!40000 ALTER TABLE `obat` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table pembelian
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pembelian`;

CREATE TABLE `pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) DEFAULT NULL,
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

INSERT INTO `pembelian` (`id`, `id_supplier`, `nomor_faktur`, `jumlah`, `total_harga`, `jenis`, `tanggal`, `status`, `created_at`, `updated_at`)
VALUES
	(6,NULL,NULL,55,545000,'langsung','2019-03-03 23:38:51',1,'2019-03-03 23:40:11','2019-03-03 23:40:11'),
	(7,NULL,NULL,1,20000,'langsung','2019-03-04 05:37:25',1,'2019-03-04 05:39:17','2019-03-04 05:39:17'),
	(8,NULL,NULL,1,10000,'langsung','2019-03-04 05:40:33',1,'2019-03-04 05:40:44','2019-03-04 05:40:44'),
	(9,NULL,NULL,1,10000,'langsung','2019-03-04 05:41:10',1,'2019-03-04 05:41:18','2019-03-04 05:41:18'),
	(10,NULL,NULL,1,10000,'langsung','2019-03-04 05:42:08',1,'2019-03-04 05:42:17','2019-03-04 05:42:17'),
	(11,NULL,NULL,1,5000,'langsung','2019-03-04 05:42:35',1,'2019-03-04 05:42:53','2019-03-04 05:42:53');

/*!40000 ALTER TABLE `pembelian` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table penjualan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `penjualan`;

CREATE TABLE `penjualan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_konsumen` int(11) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `jumlah` float NOT NULL DEFAULT '0',
  `uang` int(11) NOT NULL,
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

INSERT INTO `penjualan` (`id`, `id_konsumen`, `id_dokter`, `jumlah`, `uang`, `diskon`, `total_harga`, `jenis`, `tanggal`, `status`, `created_at`, `updated_at`)
VALUES
	(6,NULL,NULL,10,50000,0,20000,'reguler','2019-03-02 21:47:31',1,'2019-03-02 21:49:42','2019-03-04 00:11:24'),
	(7,NULL,NULL,10,50000,0,20000,'reguler','2019-03-02 21:47:31',1,'2019-03-02 21:50:20','2019-03-04 00:11:30'),
	(8,NULL,1,1,50000,0,32000,'resep','2019-03-04 05:30:08',1,'2019-03-04 05:30:36','2019-03-04 05:30:36');

/*!40000 ALTER TABLE `penjualan` ENABLE KEYS */;
UNLOCK TABLES;


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

LOCK TABLES `supplier` WRITE;
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;

INSERT INTO `supplier` (`id`, `nama`, `alamat`, `kota`, `telepon`, `no_rekening`, `email`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'PT Obat Abadiz','Jl. sudirman no 32 Malang','Malang','081232138479','11822727','obat@gmail.com',1,'2019-02-23 14:20:42','2019-02-23 14:22:44');

/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;
UNLOCK TABLES;


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



# Dump of table transaksi_pembelian
# ------------------------------------------------------------

DROP TABLE IF EXISTS `transaksi_pembelian`;

CREATE TABLE `transaksi_pembelian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `kode_obat` varchar(191) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `transaksi_pembelian` WRITE;
/*!40000 ALTER TABLE `transaksi_pembelian` DISABLE KEYS */;

INSERT INTO `transaksi_pembelian` (`id`, `id_pembelian`, `kode_obat`, `total`, `jumlah`, `total_harga`, `created_at`, `updated_at`)
VALUES
	(2,6,'',300000,20,0,'2019-03-03 23:40:11','2019-03-03 23:40:11'),
	(3,6,'4987176002671',245000,35,0,'2019-03-03 23:40:11','2019-03-03 23:40:11');

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
  `total_harga` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `transaksi_penjualan` WRITE;
/*!40000 ALTER TABLE `transaksi_penjualan` DISABLE KEYS */;

INSERT INTO `transaksi_penjualan` (`id`, `id_penjualan`, `kode_obat`, `total`, `jumlah`, `total_harga`, `created_at`, `updated_at`)
VALUES
	(1,6,'4987176002689',20000,0,0,'2019-03-02 21:49:43','2019-03-02 21:49:43'),
	(2,7,'4987176002689',20000,0,0,'2019-03-02 21:50:21','2019-03-02 21:50:21'),
	(3,8,'4987176002679',32000,0,0,'2019-03-04 05:30:37','2019-03-04 05:30:37');

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
	(1,'admin','admin','admin@admin.com','',NULL,'$2y$12$uW0P/VD0BPkc7ENdw2AwzuF2o0JtV8uCTqtZvF.AXpI47MBO4z1i6','LNCtsGocqlvz20SUEK6o5T21q5zE92UMM4KKqhElruAkq7HJGWlqVNuncnoj',1,1,'2019-02-13 00:00:00','2019-02-24 13:27:37'),
	(2,'kasir','kasir','kasir@kasir.com','123123123',NULL,'$2y$10$TAh4C1kWkif/x4zif3cX2.mlkoyj33FJP163hP6qmHmyG3l1QHKtK','H8zS65uYiTIBBMZbMfl7leL8dZRcdnZFAWEbWtLLSPLLtc7HfZlOgvQ6XHNc',2,1,'2019-02-19 11:44:38','2019-02-23 20:42:03');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
