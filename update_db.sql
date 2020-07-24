CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `penjualan` ADD `biaya_jasa` int(11) NOT NULL DEFAULT 0 AFTER `total`;

ALTER TABLE `pembelian` ADD `ppn` int(11) NOT NULL DEFAULT 0 AFTER `jumlah`;

ALTER TABLE `transaksi_penjualan` ADD `diskon` int(11) NOT NULL DEFAULT 0 AFTER `jumlah`;

ALTER TABLE `transaksi_pembelian` ADD `diskon` int(11) NOT NULL DEFAULT 0 AFTER `jumlah`;