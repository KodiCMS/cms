<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana extends Kohana_Core {
	
	public static function modules(array $modules = NULL)
	{
		$modules = parent::modules($modules);
		
		foreach (array(CMSPATH, DOCROOT) as $path)
		{
			if (!in_array($path, Kohana::$_paths))
			{
				array_unshift(Kohana::$_paths, $path);
			}
		}

		return $modules;
	}
}
