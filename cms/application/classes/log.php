<?php defined('SYSPATH') OR die('No direct script access.');

class Log extends Kohana_Log {

	/**
	 *
	 * @var array 
	 */
	protected static $_log_levels = array(
		LOG_EMERG		=> 'EMERGENCY',
		LOG_ALERT		=> 'ALERT',
		LOG_CRIT		=> 'CRITICAL',
		LOG_ERR			=> 'ERROR',
		LOG_WARNING		=> 'WARNING',
		LOG_NOTICE		=> 'NOTICE',
		LOG_INFO		=> 'INFO',
		LOG_DEBUG		=> 'DEBUG',
	);
	
	/**
	 * 
	 * @return array
	 */
	public static function levels()
	{
		return Log::$_log_levels;
	}

	/**
	 * 
	 * @param integer $level
	 * @return string
	 */
	public static function level($level)
	{		
		return Arr::get(Log::levels(), $level, 'INFO');
	}
}