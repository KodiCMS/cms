<?php defined('SYSPATH') or die('No direct access allowed.');
try {
	DB::query(NULL, "SET FOREIGN_KEY_CHECKS=0")->execute();

	DB::query(NULL, "
	  CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."messages` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`created_on` datetime NOT NULL,
		`from_user_id` int(10) unsigned DEFAULT NULL,
		`title` varchar(255) NOT NULL DEFAULT '',
		`text` text NOT NULL,
		PRIMARY KEY (`id`),
		KEY `from_user_id` (`from_user_id`)
	  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8")->execute();

	DB::query(NULL, "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."messages_users` (
		`message_id` int(10) unsigned NOT NULL,
		`parent_id` int(10) unsigned NOT NULL DEFAULT '0',
		`user_id` int(10) unsigned NOT NULL DEFAULT '0',
		`status` int(3) NOT NULL DEFAULT '1',
		`updated_on` datetime NOT NULL,
		PRIMARY KEY (`message_id`,`user_id`),
		KEY `user_id` (`user_id`),
		KEY `status` (`status`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

	DB::query(NULL, "ALTER TABLE `".TABLE_PREFIX."messages`
	  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `".TABLE_PREFIX."users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE")->execute();

	DB::query(NULL, "ALTER TABLE `".TABLE_PREFIX."messages_users`
	  ADD CONSTRAINT `messages_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `".TABLE_PREFIX."users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  ADD CONSTRAINT `messages_users_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `".TABLE_PREFIX."messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE")->execute();

	DB::query(NULL, "SET FOREIGN_KEY_CHECKS=1")->execute();
}  catch (Exception $e) {}
