CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__page_roles` (
  `page_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  KEY `page_id` (`page_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `__TABLE_PREFIX__page_roles_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `__TABLE_PREFIX__pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `__TABLE_PREFIX__page_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `__TABLE_PREFIX__roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__user_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'en-us',
  `notice` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `__TABLE_PREFIX__user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__user_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  KEY `expires` (`expires`),
  CONSTRAINT `__TABLE_PREFIX__user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`),
  CONSTRAINT `__TABLE_PREFIX__roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `__TABLE_PREFIX__users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `__TABLE_PREFIX__roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `__TABLE_PREFIX__roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__roles_permissions` (
  `role_id` int(5) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`action`),
  CONSTRAINT `__TABLE_PREFIX__roles_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `__TABLE_PREFIX__roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;