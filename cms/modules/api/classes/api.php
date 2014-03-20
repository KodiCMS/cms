<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/API
 * @author		ButscHSter
 */
class API {
	
	const NO_ERROR = 200;
	const ERROR_MISSING_PAPAM = 110;
	const ERROR_VALIDATION = 120;
	const ERROR_UNKNOWN = 130;
	const ERROR_TOKEN = 140;
	const ERROR_PERMISSIONS = 220;
	const ERROR_PAGE_NOT_FOUND = 404;
	
	/**
	 * 
	 * @param string $uri
	 * @param array $params
	 * @return API_Response
	 */
	public static function get($uri, array $params = array())
	{
		$request = static::request($uri)
			->method(Request::GET)
			->query($params)
			->query('api_key', Config::get('api', 'key'))
			->execute();
		
		return static::response($request);
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param array $params
	 * @return API_Response
	 */
	public static function put($uri, array $params = array())
	{
		$request = static::request($uri)
			->method(Request::PUT)
			->post($params)
			->post('api_key', Config::get('api', 'key'))
			->execute();
		
		return static::response($request);
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param array $params
	 * @return API_Response
	 */
	public static function post($uri, array $params = array())
	{
		$request = static::request($uri)
			->method(Request::POST)
			->post($params)
			->post('api_key', Config::get('api', 'key'))
			->execute();
		
		return static::response($request);
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param array $params
	 * @return API_Response
	 */
	public static function delete($uri, array $params = array())
	{
		$request = static::request($uri)
			->method(Request::DELETE)
			->post($params)
			->post('api_key', Config::get('api', 'key'))
			->execute();
		
		return static::response($request);
	}
	
	/**
	 * 
	 * @param string $uri
	 * @return Request
	 */
	public static function request($uri)
	{
		if(strpos( $uri, '-' ) === FALSE)
		{
			$uri = '-' . $uri;
		}
		else if (
			strpos( $uri, '-' ) > 0
		AND
			strpos( $uri, '/' ) === FALSE
		)
		{
			$uri = '/' . $uri;
		}

		return Request::factory('api' . $uri);
	}
	
	/**
	 * 
	 * @param Response $response
	 * @return \API_Response
	 */
	protected static function response(Response $response)
	{
		return new API_Response($response);
	}
}