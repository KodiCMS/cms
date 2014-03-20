<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Задача удаления старых записей
 * @package		KodiCMS/EmailQueue
 * @category	Task
 * @author		ButscHSter
 */
class Task_Search_Reindex extends Minion_Task
{
	protected $_options = array();

	protected function _execute(array $params)
	{
		Observer::notify('update_search_index');
	}
}