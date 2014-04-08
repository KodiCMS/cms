<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/API
 * @category	Exception
 * @author		ButscHSter
 */
class API_Validation_Exception extends Kohana_Exception 
{
	/**
	* Array of validation objects
	* @var array
	*/
	protected $_errors = array();
	
	/**
	 * Constructs a new exception for the specified model
	 *
	 * @param  Validation $object      The Validation object of the model
	 * @param  string     $message     The error message
	 * @param  array      $values      The array of values for the error message
	 * @param  integer    $code        The error code for the exception
	 * @return void
	 */
	public function __construct($errors, $message = 'Failed to validate array', array $values = NULL)
	{
		$this->_errors = $errors;
		parent::__construct($message, $values, API::ERROR_VALIDATION);
	}
	
	public function get_response()
    {
		// Lets log the Exception, Just in case it's important!
		Kohana_Exception::log($this);

		$params = array
		(
			'code'  => $this->getCode(),
			'message' => rawurlencode($this->getMessage()),
			'response' => NULL,
			'errors' => $this->_errors
		);

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
