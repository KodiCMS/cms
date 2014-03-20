CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__page_behavior_settings` (
  `page_id` int(10) unsigned NOT NULL,
  `behavior_id` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`page_id`),
  CONSTRAINT `__TABLE_PREFIX__page_behavior_settings_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `__TABLE_PREFIX__pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;