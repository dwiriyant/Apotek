# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.23)
# Database: apotek
# Generation Time: 2019-03-24 18:19:41 +0000
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
  `diskon` float NOT NULL DEFAULT '0',
  `total_harga` float NOT NULL DEFAULT '0',
  `jenis` varchar(191) NOT NULL DEFAULT 'reguler',
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table retur_pembelian
# ------------------------------------------------------------

DROP TABLE IF EXISTS `retur_pembelian`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



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
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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
  `jual_pack` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
