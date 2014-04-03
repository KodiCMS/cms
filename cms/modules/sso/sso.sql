CREATE TABLE IF NOT EXISTS `user_social` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned DEFAULT NULL,
	`service_id` varchar(200) NOT NULL DEFAULT '',
	`service_name` varchar(200) NOT NULL DEFAULT '',
	`service_type` varchar(100) NOT NULL DEFAULT '',
	`email` varchar(200) DEFAULT NULL,
	`name` varchar(200) NOT NULL DEFAULT '',
	`avatar` varchar(200) DEFAULT NULL,
	`response` text NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `service_key` (`service_id`,`service_type`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;