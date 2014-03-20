CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__dshfields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ds_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL DEFAULT '',
  `family` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  `header` varchar(255) NOT NULL DEFAULT '',
  `from_ds` int(11) unsigned DEFAULT '0',
  `props` text NOT NULL,
  `position` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ds_id` (`ds_id`),
  KEY `family` (`family`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__hybrid_tags` (
  `field_id` int(11) unsigned NOT NULL,
  `doc_id` int(11) NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  KEY `tag_id` (`tag_id`),
  KEY `field_id` (`field_id`,`doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__dshybrid` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ds_id` int(11) unsigned NOT NULL DEFAULT '0',
  `published` int(1) unsigned DEFAULT '0',
  `header` varchar(255) NOT NULL DEFAULT '',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ds_id` (`ds_id`,`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;