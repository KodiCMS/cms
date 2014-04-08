CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__api_keys` (
  `id` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;