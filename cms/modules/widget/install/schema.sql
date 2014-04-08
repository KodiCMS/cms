CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__page_widgets` (
  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
  `widget_id` int(10) unsigned NOT NULL DEFAULT '0',
  `block` varchar(32) NOT NULL DEFAULT '',
  `position` int(4) NOT NULL DEFAULT '500',
  PRIMARY KEY (`page_id`,`widget_id`),
  KEY `page_block` (`page_id`,`block`),
  KEY `widget_id` (`widget_id`),
  CONSTRAINT `__TABLE_PREFIX__page_widgets_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `__TABLE_PREFIX__pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `__TABLE_PREFIX__page_widgets_ibfk_2` FOREIGN KEY (`widget_id`) REFERENCES `__TABLE_PREFIX__widgets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__widgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `template` varchar(100) DEFAULT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `code` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__layout_blocks` (
  `layout_name` varchar(100) NOT NULL,
  `block` varchar(100) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`layout_name`,`block`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;