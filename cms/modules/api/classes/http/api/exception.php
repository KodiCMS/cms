<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/API
 * @category	Exception
 * @author		ButscHSter
 */
class HTTP_API_Exception extends Kohana_HTTP_Exception {
	
	/**
     * Generate a Response for all Exceptions without a more specific override
     * 
     * The user should see a nice error page, however, if we are in development
     * mode we should show the normal Kohana error page.
     * 
     * @return Response
     */
    public function get_response()
    {
		// Lets log the Exception, Just in case it's important!
		Kohana_Exception::log($this);

		$params = array
		(
			'code'  => 500,
			'message' => rawurlencode($this->getMessage()),
			'response' => NULL
		);

		if ($this instanceof HTTP_Exception)
		{
			$params['code'] = $this->getCode();
		}

		try
		{
			return json_encode($params);
		}
		catch ( Exception $e )
		{
			return parent::get_response();
		}
    }
}