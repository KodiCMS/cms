<?php defined('SYSPATH') OR die('No direct script access.');

class I18n extends Kohana_I18n {
	
	public static function load($lang)
	{
		$table = parent::load($lang);
		
		$filename = CMSPATH . FileSystem::normalize_path('media/js/i18n/'.$lang.'.js');
		
		if( ! file_exists($filename) 
				OR ( file_exists($filename) AND (time() - filemtime($filename)) > Date::DAY))
		{
			if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
			{
				// Start a new benchmark
				$benchmark = Profiler::start('i18n', 'Generate file for lang - ' . $lang);
			}
			try
			{
				// Create the log file
				file_put_contents($filename, '// Auto generated i18n lang file for lang '. $lang."\n");
				file_put_contents($filename, 'cms.addTranslation(' . json_encode($table) . ');', FILE_APPEND);
				
				// Allow anyone to write to log files
				chmod($filename, 0777);
			}
			catch(Exception $e)
			{
				// do something
			}

			if (isset($benchmark))
			{
				// Stop the benchmark
				Profiler::stop($benchmark);
			}
		}
		
		return $table;
	}
	
	/**
	 * 
	 * @return string
	 */
	public static function detect_lang()
	{
		$browser_langs = array_keys(Request::accept_lang());
		$lang = array_shift($browser_langs);
				
		return self::normalize_lang_key($lang);
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function available_langs()
	{
		$langs = Kohana::$config->load('locales')->as_array();
		
		$_langs = array();
		foreach ($langs as $lang => $name)
		{
			$_langs[self::normalize_lang_key($lang)] = $name;
		}
		
		return $_langs;
	}
	
	public static function normalize_lang_key($lang)
	{
		return strtolower(str_replace(array(' ', '_'), '-', $lang));
	}
}