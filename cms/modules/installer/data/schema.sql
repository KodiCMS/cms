SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `TABLE_PREFIX_settings` (
  `name` varchar(40) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `id` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `TABLE_PREFIX_plugins` (
  `id` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_layout_blocks` (
  `layout_name` varchar(100) NOT NULL,
  `block` varchar(100) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`layout_name`,`block`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `TABLE_PREFIX_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `breadcrumb` varchar(160) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` text,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `layout_file` varchar(250) NOT NULL,
  `behavior_id` varchar(25) NOT NULL,
  `status_id` int(11) unsigned NOT NULL DEFAULT '100',
  `created_on` datetime DEFAULT NULL,
  `published_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_by_id` int(11) unsigned DEFAULT NULL,
  `updated_by_id` int(11) unsigned DEFAULT NULL,
  `position` mediumint(6) unsigned DEFAULT NULL,
  `needs_login` tinyint(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `created_by_id` (`created_by_id`),
  KEY `updated_by_id` (`updated_by_id`),
  KEY `slug` (`slug`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE `TABLE_PREFIX_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `count` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `TABLE_PREFIX_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `TABLE_PREFIX_page_parts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `filter_id` varchar(25) DEFAULT NULL,
  `content` longtext,
  `content_html` longtext,
  `page_id` int(11) unsigned DEFAULT NULL,
  `is_protected` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE `TABLE_PREFIX_page_roles` (
  `page_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  KEY `page_id` (`page_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `TABLE_PREFIX_page_tags` (
  `page_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `page_id` (`page_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_page_widgets` (
  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
  `widget_id` int(10) unsigned NOT NULL DEFAULT '0',
  `block` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`page_id`,`widget_id`),
  KEY `page_block` (`page_id`,`block`),
  KEY `widget_id` (`widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_page_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `key` varchar(20) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_page_behavior_settings` (
  `page_id` int(10) unsigned NOT NULL,
  `behavior_id` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `TABLE_PREFIX_users` (
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

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_user_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `notice` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_user_social` (
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
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_user_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_user_reflinks` (
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `code` varchar(255) NOT NULL,
  `data` text,
  `created` datetime NOT NULL,
  UNIQUE KEY `unique_reflink` (`user_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `TABLE_PREFIX_roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_email_queues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `sender_name` varchar(128) DEFAULT NULL,
  `sender_email` varchar(320) NOT NULL,
  `recipient_name` varchar(128) DEFAULT NULL,
  `recipient_email` varchar(320) NOT NULL,
  `subject` varchar(78) DEFAULT NULL,
  `priority` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT 'Higher priority is a larger number. Priority 5 is higher than priority 1.',
  `attempts` smallint(5) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_email_queue_bodies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `queue_id` (`queue_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `TABLE_PREFIX_widgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `template` varchar(100) DEFAULT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `code` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `TABLE_PREFIX_pages`
	ADD FOREIGN KEY ( `created_by_id` ) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	ADD FOREIGN KEY ( `updated_by_id` ) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_page_parts`
  ADD CONSTRAINT `page_parts_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `TABLE_PREFIX_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_page_tags`
  ADD CONSTRAINT `page_tags_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `TABLE_PREFIX_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `page_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `TABLE_PREFIX_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_page_roles`
  ADD CONSTRAINT `page_roles_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `TABLE_PREFIX_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `page_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `TABLE_PREFIX_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_page_widgets`
  ADD CONSTRAINT `page_widgets_ibfk_2` FOREIGN KEY (`widget_id`) REFERENCES `TABLE_PREFIX_widgets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `page_widgets_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `TABLE_PREFIX_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_page_fields`
  ADD CONSTRAINT `page_fields_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `TABLE_PREFIX_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_page_behavior_settings`
  ADD CONSTRAINT `page_behavior_settings_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `TABLE_PREFIX_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `TABLE_PREFIX_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_user_social`
  ADD CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_user_reflinks`
  ADD CONSTRAINT `user_reflinks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `TABLE_PREFIX_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TABLE_PREFIX_email_queue_bodies`
  ADD CONSTRAINT `email_queue_bodies_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `TABLE_PREFIX_email_queues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS=1;