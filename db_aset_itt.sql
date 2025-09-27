# Host: localhost  (Version 5.5.5-10.1.38-MariaDB)
# Date: 2025-08-16 09:11:45
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "assets"
#

DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(100) NOT NULL,
  `nama_aset` varchar(50) NOT NULL DEFAULT '',
  `jenis` varchar(75) NOT NULL DEFAULT '',
  `lokasi` varchar(255) NOT NULL DEFAULT '',
  `kondisi` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

#
# Data for table "assets"
#

INSERT INTO `assets` VALUES (1,'','Personal Computer','Apollyon','HSE Departemen','Baru'),(2,'','Printer Epson L3150','Epson L3150','',''),(3,'','Printer Epson L3110','Epson L3110','Rig 29','Baru'),(4,'','Printer Epson L3210','Epson L3210','',''),(6,'','CCTV IP Cam','Smart IPcam Avaro Outdoor','',''),(7,'','Instrument Drilling System','Totco Drilling','',''),(8,'','Mesin Laminating','PROMAXI FGK 330','',''),(9,'','Laptop/Notebook','Asus A11404Z','',''),(10,'','CCTV Dahua Outdoor','4 MP','',''),(11,'','CCTV Dahua Indoor','5 MP','','');

#
# Structure for table "departemen"
#

DROP TABLE IF EXISTS `departemen`;
CREATE TABLE `departemen` (
  `kode_departemen` varchar(50) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL,
  PRIMARY KEY (`kode_departemen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Data for table "departemen"
#

INSERT INTO `departemen` VALUES ('admin','IT Support'),('HR','Human Resource Development'),('HS','HSE'),('LOGOFF','Logistic Office'),('LOGWHS','Logistic Warehouse'),('MTCN','Maintanance'),('project_ma','Project Manager'),('RE','Reability Engineer'),('RIG21','Staff Rig 21'),('RIG27','Staff Rig 27'),('RIG28','Staff Rig 28'),('RIG29','Staff Rig 29'),('RIG32','Staff Rig 32'),('RIG60','Staff Rig 60'),('RIG61','Staff Rig 61'),('TR01','Transport');

#
# Structure for table "detail_perbaikan"
#

DROP TABLE IF EXISTS `detail_perbaikan`;
CREATE TABLE `detail_perbaikan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal_pengajuan` date NOT NULL,
  `chargo_manifest` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `kode_aset` varchar(50) DEFAULT NULL,
  `nama_aset` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Menunggu',
  `kategori_aset` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

#
# Data for table "detail_perbaikan"
#

INSERT INTO `detail_perbaikan` VALUES (5,'2025-08-06','CM-1900',1,'Lemot','A02','Del Core i7',69,'Diproses','Personal Computer','2025-08-08 22:15:45'),(6,'2025-08-06','CM-1901',1,'Tampilkan Gambar','A13','Dahua',72,'Selesai','CCTV','2025-08-08 22:16:25'),(7,'2025-07-09','CM-1902',2,'ad','A14','Smart IPcam Avaro Outdoor',73,'Selesai','CCTV','2025-08-08 22:56:34'),(8,'2025-08-08','CM-1903',1,'sadasd','A11','HP Deskjet 2332',69,'Menunggu','Printer',NULL),(9,'2025-07-30','CM-1904',2,'sadad','A13','Dahua',69,'Selesai','CCTV','2025-08-08 22:57:20'),(10,'2025-09-17','CM-1905',2,'sd','A14','Smart IPcam Avaro Outdoor',69,'Menunggu','CCTV',NULL),(11,'2025-01-01','CM-1906',1,'n','A15','Totco Drilling',73,'Menunggu','Sensor Drilling',NULL),(12,'2025-02-02','CM-1907',1,'f','A13','Dahua',72,'Menunggu','CCTV',NULL),(13,'2025-03-03','CM-1908',1,'f','A11','HP Deskjet 2332',69,'Menunggu','Printer',NULL),(14,'2025-08-08','CM-1909',1,'c','A06','Epson L3150',73,'Menunggu','Printer',NULL),(15,'2025-08-09','CM-1910',1,'rusak','A06','Epson L3150',69,'Menunggu','Printer',NULL),(16,'2025-08-09','CM-1911',1,'ds','A09','Epson L5290',69,'Menunggu','Printer',NULL),(17,'2025-08-09','CM-1912',1,'n','A04','Asus Vivobook 14 i3',69,'Menunggu','Laptop',NULL);

#
# Structure for table "goods_form"
#

DROP TABLE IF EXISTS `goods_form`;
CREATE TABLE `goods_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_goods_form` varchar(50) NOT NULL,
  `tanggal_goods_form` date NOT NULL,
  `chargo_manifest` varchar(100) NOT NULL,
  `name_of_goods` varchar(100) NOT NULL,
  `kategori_aset` varchar(100) DEFAULT NULL,
  `description` text,
  `qty` int(11) NOT NULL,
  `remarks` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

#
# Data for table "goods_form"
#

INSERT INTO `goods_form` VALUES (15,'GF001','2025-07-16','CM 22-9872','Totco Drilling','Sensor Drilling','BDSI 27',2,'Ready To Use'),(16,'GF001','2025-07-16','CM 22-9872','Asus A11404Z','Laptop','BDSI 27',1,'Ready To Use'),(17,'GF002','2025-07-21','CM 21-1098','Epson L3210','Printer','Maintanance',1,'Ready To Use'),(18,'GF002','2025-07-21','CM 21-1098','Smart IPcam Avaro Outdoor','CCTV','Maintanance',1,'Ready To Use');

#
# Structure for table "karyawann"
#

DROP TABLE IF EXISTS `karyawann`;
CREATE TABLE `karyawann` (
  `nomor_badge` varchar(50) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `kode_departemen` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`nomor_badge`),
  KEY `kode_departemen` (`kode_departemen`),
  CONSTRAINT `karyawann_ibfk_1` FOREIGN KEY (`kode_departemen`) REFERENCES `departemen` (`kode_departemen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Data for table "karyawann"
#

INSERT INTO `karyawann` VALUES ('BDS-1942123','Dyah','hr'),('BDS-1942213','Teguh','admin'),('BDS-1942321','Firania Amanda','mtcn'),('BDS-1942354','Harianto','hs'),('BDS-1942356','Yanwar Shaf','re'),('BDS-1942366','Rudy Sofyan','rig21'),('BDS-1942430','Alfarizi Situmorang','rig32'),('BDS-1942432','Jhon Hendri','rig28'),('BDS-1942454','Sony','rig60'),('BDS-1942654','Bagas Al Fajri','rig27'),('BDS-1942765','Ramses','rig61'),('BDS-1942767','J.Romico','logoff'),('BDS-1942879','Karlina','tr01'),('BDS-1942989','Yujian','project_ma');

#
# Structure for table "kategori_aset"
#

DROP TABLE IF EXISTS `kategori_aset`;
CREATE TABLE `kategori_aset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

#
# Data for table "kategori_aset"
#

INSERT INTO `kategori_aset` VALUES (3,'Printer'),(4,'Laptop'),(5,'Personal Computer'),(6,'Sensor Drilling'),(7,'CCTV'),(8,'Perangkat Keras');

#
# Structure for table "assetss"
#

DROP TABLE IF EXISTS `assetss`;
CREATE TABLE `assetss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_aset` varchar(100) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `kode_aset` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `assetss_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_aset` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

#
# Data for table "assetss"
#

INSERT INTO `assetss` VALUES (2,'Del Core i5',5,'A01'),(3,'Del Core i7',5,'A02'),(4,'MSI PRO Desktop PC Intel i5',5,'A03'),(5,'Asus Vivobook 14 i3',4,'A04'),(6,'Asus A11404Z',4,'A05'),(7,'Epson L3150',3,'A06'),(8,'Epson L3110',3,'A07'),(9,'Epson L3210',3,'A08'),(10,'Epson L5290',3,'A09'),(11,'HP Deskjet 2132',3,'A10'),(12,'HP Deskjet 2332',3,'A11'),(13,'HiView ',7,'A12'),(14,'Dahua',7,'A13'),(15,'Smart IPcam Avaro Outdoor',7,'A14'),(16,'Totco Drilling',6,'A15'),(17,'Promaxi Fgk 330',8,'A16');

#
# Structure for table "laporan_perbaikan"
#

DROP TABLE IF EXISTS `laporan_perbaikan`;
CREATE TABLE `laporan_perbaikan` (
  `id_laporan` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `bulan` int(11) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `kode_aset` varchar(50) DEFAULT NULL,
  `nama_aset` varchar(100) DEFAULT NULL,
  `kategori_aset` varchar(100) DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `chargo_manifest` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `keterangan` text,
  PRIMARY KEY (`id_laporan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Data for table "laporan_perbaikan"
#


#
# Structure for table "manifest_perbaikan"
#

DROP TABLE IF EXISTS `manifest_perbaikan`;
CREATE TABLE `manifest_perbaikan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal_pengajuan` date NOT NULL,
  `username` varchar(100) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL,
  `chargo_manifest` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

#
# Data for table "manifest_perbaikan"
#

INSERT INTO `manifest_perbaikan` VALUES (1,'2025-07-09','firaniaamd','Maintanance','CM 23-1746'),(2,'2025-07-10','firaniaamd','Maintanance','CM 23-100'),(3,'2025-07-12','firaniaamd','Maintanance','CM116'),(4,'2025-07-15','firaniaamd','Maintanance','CM-110'),(5,'2025-07-15','firaniaamd','Maintanance','CM 23-100'),(6,'2025-07-16','firaniaamd','Maintanance','CM 21-1098'),(7,'2025-07-16','bagas','Staff Rig 27','CM 22-9872'),(8,'2025-07-22','fariz','Staff Rig 32','CM-19023'),(9,'2025-08-05','firaniaamd','Maintanance','CM-19023'),(10,'2025-08-05','firaniaamd','Maintanance','CM-19024'),(11,'2025-08-05','fariz','Staff Rig 32','CM-19023'),(12,'2025-08-05','fariz','Staff Rig 32','CM-12345');

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(50) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `nomor_badge` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

#
# Data for table "users"
#

INSERT INTO `users` VALUES (46,'lie','$2y$10$ul6of70kD1THl.LnaUPBg.4DxqSLxiBnjosBYjrSELL6nFZ9wvYLC','project_manager','l','BDS-1942434'),(55,'teguh','$2y$10$t6aD5RTBQ2nZoBjEeOqDuuyq4oDuB8qmPyVhNpKcrxlhl.7Gv3qBW','admin','teguh','BDS-1942354'),(69,'firaniaamd','$2y$10$q3wdDT1MzRPc31Y3mhg4NOCLwCZnDh4Qq7CaAOxe7yv0PsWImT8z2','mtcn','firania','BDS-1942321'),(72,'bagas','$2y$10$FHgUEk.1k9FXhl3ZW.LaKureN/1JjNVl3QTrlRrN6tOZCyXI0pZM2','rig27','bagas','BDS-1942654'),(73,'fariz','$2y$10$rTe7qExW/Y0OkNB9wsgDVeZdFwn2vvFZIQLiwAQ3WRt1Ax/n8xZxC','rig32','fariz','BDS-1942430');

#
# Structure for table "perbaikan"
#

DROP TABLE IF EXISTS `perbaikan`;
CREATE TABLE `perbaikan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_aset` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal_pengajuan` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Menunggu','Diproses','Selesai') DEFAULT 'Menunggu',
  PRIMARY KEY (`id`),
  KEY `id_aset` (`id_aset`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `perbaikan_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `assets` (`id`),
  CONSTRAINT `perbaikan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "perbaikan"
#


#
# Structure for table "userss"
#

DROP TABLE IF EXISTS `userss`;
CREATE TABLE `userss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `plain_password` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `nomor_badge` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

#
# Data for table "userss"
#

INSERT INTO `userss` VALUES (2,'BDS-1929333','$2y$10$sSWKq8zjPezoDnXBI0jYseTHqFP5Dpq1Cl4ymnjD18xfRSGlRBsnC',NULL,'RIG32','BDS-1929333'),(6,'BDS-1942898','$2y$10$ULg.iPaTEe9SMrjshtEOTOrOodxcR2w3Ps4TlqcX7IptufPoLjpx2',NULL,'TR01','BDS-1942898'),(7,'BDS-19428203','$2y$10$eT2Wcv6Dy8xSSs48QpW5ru9IFBqNbaIPQBNO7yCp7gRHOiDPxZvbe',NULL,'RIG21','BDS-19428203'),(8,'BDS-1942899','$2y$10$WK9EJKST9zv7jtwsK7GZjuNZ/jFWYphUIfDODeGuIEb/f5hk38d1.',NULL,'TR01','BDS-1942899'),(10,'BDS-1942782','$2y$10$bN/0wiUwbRmjbELLZsnOlu2sGkKkZADIR6oS.yGylXoNXaffeJ9hm',NULL,'MTCN','BDS-1942782'),(11,'BDS-1942321','$2y$10$YIIZmN3upi8mReagx8O/sOzfkxb1aN3GcW7cq0FDfc97bJI3oWRqW',NULL,'RIG29','BDS-1942321'),(12,'BDS-1942567','$2y$10$oBUdAsCFCfB4qoJVc1rkTOY/cMUak3.ijYbXVUp9BHid4AG6GuNTq',NULL,'RIG27','BDS-1942567'),(13,'BDS-1942315','$2y$10$IqGXQ0su.kgux3eFlzfxNur7RWvNIVAN5ie7yNqbrx396hfCE2Tsi',NULL,'RIG28','BDS-1942315'),(14,'BDS-1942765','$2y$10$f0j.Om47Ej481PkZHpS.xeL2fjGJ8nMkCK2EzUI697l0HUNvuFHgC',NULL,'RE','BDS-1942765'),(15,'BDS-1942320','$2y$10$NgC6l5vs3a5.FvD/Vu2BOOZ5Srce5DiZjG0eoh9z7.Wn8MAG.ZzDO',NULL,'RE','BDS-1942320'),(16,'BDS-1942767','$2y$10$J9Uggkka2myHrY5A2yHTxeoPJssXD.DDnEdxCh0zdN402oaSPoEVu',NULL,'LOGWHS','BDS-1942767'),(17,'BDS-19423120','$2y$10$FMl8HycSsyZ/RnxH9AsWSucNVOw8m4jQL4Y.0YXwqXXxSwby3RVp.',NULL,'HR','BDS-19423120'),(18,'BDS-1942231','$2y$10$FX.yuNwBSHlZthMxqv6w/uC7Au/KHIY3NHwJUGpg20EGCi5oulUFW',NULL,'RIG61','BDS-1942231'),(19,'BDS-1942434','$2y$10$dvm9ND2PtGoRXr0lKgV7nuVrGiAc4lWhu6HHUl.jH.oFT9NnLuBXC',NULL,'PROJECT_MANAGER','BDS-1942434'),(23,'BDS-1942333','$2y$10$S6khpvRn0KWAo3z00M7yfuRqSuc/yY.AdyXFPk2Zqqj9rtG41KNT6',NULL,'PROJECT_MA','BDS-1942333'),(24,'BDS-194221','$2y$10$zF8T9tp1upNPzo1njJfIROaf7zxsbOOI3befOgH326BxoEpvu0pjC',NULL,'PROJECT_MANAGER','BDS-194221'),(26,'BDS-194229','$2y$10$7MRd0psI2Gkq2vAWbJoWu.92uHBS68J/bVgXpBlsUM7RGhgq2q0mm',NULL,'PROJECT_MA','BDS-194229'),(28,'BDS-1942354','$2y$10$HNePrbRWNHVu4cVGqtdIbejCWvqzk8K2J0KSIfO8NZL7bXdgjHOye',NULL,'admin','BDS-1942354'),(29,'BDS-1847398','$2y$10$NYS4a6d1vEIoh0BkciiVluEaVm63/sgUvprjvpWN/6YT6acuILDhC',NULL,'RIG21','BDS-1847398'),(30,'BDS-1942554','$2y$10$ESPfd5NyG/sBdJax.tIX/e2S8z4OAE6TqU3JDwz6KCfTuEsBMlFC.',NULL,'HS','BDS-1942554');
