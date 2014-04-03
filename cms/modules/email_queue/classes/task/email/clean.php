<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Задача удаления старых записей
 * @package		KodiCMS/EmailQueue
 * @category	Task
 * @author		ButscHSter
 */
class Task_Email_Clean extends Minion_Task
{
	protected $_options = array();

	protected function _execute(array $params)
	{
		ORM::factory('email_queue')->clean_old();
	}
}