CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `path` varchar(64) NOT NULL,
  `path_part` varchar(64) NOT NULL,
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `lvl` int(10) unsigned DEFAULT NULL,
  `scope` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;