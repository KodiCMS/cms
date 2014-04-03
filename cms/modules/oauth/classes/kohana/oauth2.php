<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * OAuth v2 class
 *
 * @package    Kohana/OAuth
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
abstract class Kohana_OAuth2 extends OAuth {

	/**
	 * Get request object
	 *
	 * @param   string   Request type (access, token etc)
	 * @param   string   Request method (POST, GET)
	 * @param   string   URL
	 * @param   array    Request params
	 * @return  OAuth2_Request
	 */
	public function request($type, $method, $url, array $options = NULL)
	{
		return OAuth2_Request::factory($type, $method, $url, $options);
	}

	/**
	 * @param  $name  Provider name
	 * @param  array  Provider options
	 * @return OAuth2_Provider
	 */
	public function provider($name, array $options = NULL)
	{
		return OAuth2_Provider::factory($name, $options);
	}

	/**
	 * @param  $name   Token type
	 * @param  array   Token options
	 * @return OAuth2_Token
	 */
	public function token($name, array $options = NULL)
	{
		return OAuth2_Token::factory($name, $options);
	}

}
