CREATE TABLE `qr_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_hash` varchar(255) DEFAULT NULL,
  `ref_name` varchar(255) DEFAULT NULL,
  `max_usages` int(11) DEFAULT '0',
  `current_usages` int(11) DEFAULT '0',
  `payload` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
