CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__search_index` (
  `id` int(10) unsigned NOT NULL,
  `module` varchar(50) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `title` text NOT NULL,
  `annotation` varchar(255) default NULL,
  `header` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`,`module`),
  FULLTEXT KEY `header` (`header`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;