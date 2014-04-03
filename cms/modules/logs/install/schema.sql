CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_on` datetime NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `level` tinytext NOT NULL,
  `message` text NOT NULL,
  `additional` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;