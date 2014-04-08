CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__plugins` (
  `id` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;