<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception {

	/**
	 * Get a Response object representing the exception
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception  $e
	 * @return  Response
	 */
	public static function response(Exception $e)
	{
		if ( Config::get('site', 'debug') == Config::YES OR Kohana::$environment !== Kohana::PRODUCTION)
		{
			// Show the normal Kohana error page.
			return parent::response($e);
		}
		else
		{
			$params = array
			(
				'code'  => 500,
				'message' => rawurlencode($e->getMessage())
			);

			if ($e instanceof HTTP_Exception)
			{
				$params['code'] = $e->getCode();
			}
				
			try
			{
				$request = Request::factory( Route::get('error')->uri($params), array(), FALSE)
					->execute()
					->send_headers(TRUE)
					->body();
				
				// Prepare the response object.
				$response = Response::factory();

				// Set the response status
				$response->status(($e instanceof HTTP_Exception) ? $e->getCode() : 500);

				// Set the response body
				$response->body($request);
				
				return $response;
			}
			catch ( Exception $e )
			{
				return parent::response($e);
			}
		}
	}
}
