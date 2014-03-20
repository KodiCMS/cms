<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Запуск задач через Crontab
 * @package		KodiCMS/JobRun
 * @category	Task
 * @author		ButscHSter
 */
class Task_Log_Clean extends Minion_Task
{
	protected $_options = array();

	protected function _execute(array $params)
	{
		ORM::factory('log')->clean_old();
	}
}