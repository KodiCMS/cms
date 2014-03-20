CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__page_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `key` varchar(20) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`key`),
  CONSTRAINT `__TABLE_PREFIX__page_fields_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `__TABLE_PREFIX__pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;