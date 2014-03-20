<?php defined('SYSPATH') or die('No direct access allowed.');

switch(DB_TYPE)
{
	case 'mysql':
		$config = array(
			'type'       => 'mysql',
			'connection' => array(
				'hostname'   => DB_SERVER,
				'database'   => DB_NAME,
				'username'   => DB_USER,
				'password'   => DB_PASS,
				'persistent' => FALSE,
			),
			'table_prefix' => TABLE_PREFIX,
			'charset'      => 'utf8',
			'caching'      => FALSE,
			'profiling'	   => TRUE
		);
		
		break;
	case 'pdo':
		$config = array(
			'type'       => 'PDO',
			'connection' => array(
				'dsn'        => 'mysql:host='.DB_SERVER.';dbname='.DB_NAME,
				'username'   => DB_USER,
				'password'   => DB_PASS,
				'persistent' => FALSE,
			),
			'table_prefix' => TABLE_PREFIX,
			'charset'      => 'utf8',
			'caching'      => FALSE,
		);
		
		break;
	default:
		throw new Kohana_Exception('Database driver :driver not supported',
			array(':driver' => DB_TYPE));
}

return array('default' => $config);