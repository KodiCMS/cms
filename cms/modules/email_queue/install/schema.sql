CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__email_queues` (
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

CREATE TABLE IF NOT EXISTS `__TABLE_PREFIX__email_queue_bodies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_id` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `queue_id` (`queue_id`),
  CONSTRAINT `__TABLE_PREFIX__email_queue_bodies_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `__TABLE_PREFIX__email_queues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;