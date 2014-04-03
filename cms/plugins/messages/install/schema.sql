CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_on` datetime NOT NULL,
  `from_user_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from_user_id` (`from_user_id`),
  CONSTRAINT `__TABLE_PREFIX__messages_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__messages_users` (
  `message_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` int(3) NOT NULL DEFAULT '1',
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`message_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `__TABLE_PREFIX__messages_users_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `__TABLE_PREFIX__messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `__TABLE_PREFIX__messages_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;