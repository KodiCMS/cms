<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Cli installer for KodiCMS
 *
 * It can accept the following options:
 *  - db_server: Mysql server (default - localhost)
 *  - db_port: Mysql port (default - 3306) 
 *  - db_user: Mysql user (default - root)
 *  - db_password: Mysql password (default - empty)
 *  - db_name: Mysql database
 *  - table_prefix: Mysql database prefix (default - empty)
 *  - site_name: CMS site title
 *  - username: Admin username
 *  - password: Admin password (default - auto generate)
 *  - email: Admin email (default - admin@yoursite.com)
 *  - admin_dir_name: Directory to access backend intarface (default - backend)
 *  - url_suffix: URL suffix append to url (default - .html)
 *  - timezone: Current timezone (http://www.php.net/manual/en/timezones.php)
 *  - empty_database: Clear selected database before import new data

 * @package		KodiCMS/Installer
 * @category	Task
 * @author		ButscHSter
 */
class Task_Install extends Minion_Task
{
	protected $_options = array(
		'db_server' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_password' => '',
		'db_name' => NULL,
		'db_table_prefix' => '',
		'site_name' => CMS_NAME,
		'username' => 'admin',
		'password' => NULL,
		'email' => 'admin@yoursite.com',
		'admin_dir_name' => 'backend',
		'url_suffix' => '.html',
		'timezone' => NULL,
		'password_generate' => TRUE,
		'empty_database' => FALSE,
		'cache_type' => 'sqlite'
	);
	
	public function build_validation(Validation $validation)
	{
		$cache_types = Kohana::$config->load('installer')->get( 'cache_types', array() );
		return parent::build_validation($validation)
			->rule('db_server', 'not_empty')
			->rule('db_port', 'not_empty')
			->rule('db_port', 'numeric')
			->rule('db_user', 'not_empty')
			->rule('username', 'not_empty')
			->rule('email', 'not_empty')
			->rule('email', 'email')
			->rule('cache_type', 'not_empty')
			->rule('cache_type', 'in_array', array(':value', array_keys( $cache_types )))
			->rule('admin_dir_name', 'not_empty');
	}

	protected function _execute(array $params)
	{
		$params['db_driver'] = 'mysql';

		if( $params['db_name'] === NULL )
		{
			$params['db_name'] = Minion_CLI::read(__('Please enter database name'));
		}
		
		if( $params['timezone'] === NULL )
		{
			$answer = Minion_CLI::read(__('Select current timezone automaticly (:current)', array(':current' => date_default_timezone_get())), array('y', 'n'));
			
			if($answer == 'y')
			{
				$params['timezone'] = date_default_timezone_get();
			}
			else
			{
				$params['timezone'] = Minion_CLI::read(__('Please enter current timezone (:site)', array(':site' => 'http://www.php.net/manual/en/timezones.php')), DateTimeZone::listIdentifiers());
			}
		}
		
		if( $params['cache_type'] === NULL )
		{
			$cache_types = Kohana::$config->load('installer')->get( 'cache_types', array() );
			$params['cache_type'] = Minion_CLI::read(__('Please enter cache type (:types)', array(
				':types' => implode(', ', $cache_types)
			)));
		}
		
		if( $params['password'] !== NULL )
		{
			unset($params['password_generate']);
			$params['password_field'] = $params['password_confirm'] = $params['password'];
		}
		
		$response = Request::factory('install/go')
			->method(Request::POST)
			->post(array('install' => $params))
			->execute()
			->body();
		
		Minion_CLI::write($response);
	}
}