<?php defined('SYSPATH') or die('No direct access allowed.');

try {
	DB::query(NULL, "SET FOREIGN_KEY_CHECKS=0")->execute();
	DB::query(NULL, "DROP TABLE `".TABLE_PREFIX."messages`, `".TABLE_PREFIX."messages_users`")->execute();
	DB::query(NULL, "SET FOREIGN_KEY_CHECKS=1")->execute();
}  catch (Exception $e) {}
