CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__user_social` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `service_id` varchar(200) NOT NULL DEFAULT '',
  `service_name` varchar(200) NOT NULL DEFAULT '',
  `service_type` varchar(100) NOT NULL DEFAULT '',
  `realname` varchar(200) NOT NULL DEFAULT '',
  `email` varchar(200) DEFAULT NULL,
  `avatar` varchar(200) DEFAULT NULL,
  `response` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_key` (`service_id`,`service_type`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `__TABLE_PREFIX__user_social_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;