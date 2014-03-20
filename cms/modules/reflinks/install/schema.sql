CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__user_reflinks` (
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `code` varchar(255) NOT NULL,
  `data` text,
  `created` datetime NOT NULL,
  UNIQUE KEY `unique_reflink` (`user_id`,`code`),
  CONSTRAINT `__TABLE_PREFIX__user_reflinks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;