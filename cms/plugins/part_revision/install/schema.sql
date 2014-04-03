CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__part_revision` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `part_id` int(10) unsigned NOT NULL,
  `created_on` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `part_id` (`part_id`,`created_on`),
  CONSTRAINT `__TABLE_PREFIX__part_revision_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `__TABLE_PREFIX__page_parts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8