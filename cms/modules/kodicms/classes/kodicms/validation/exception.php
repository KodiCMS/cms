<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS
 * @category	Exception
 * @author		ButscHSter
 */
class KodiCMS_Validation_Exception extends Kohana_Exception 
{
	/**
	* Array of validation objects
	* @var array
	*/
	protected $_objects = array();
	
	/**
	 * Constructs a new exception for the specified model
	 *
	 * @param  Validation $object      The Validation object of the model
	 * @param  string     $message     The error message
	 * @param  array      $values      The array of values for the error message
	 * @param  integer    $code        The error code for the exception
	 * @return void
	 */
	public function __construct(Validation $object, $message = 'Failed to validate array', array $values = NULL, $code = 0)
	{
		$this->_objects['_object'] = $object;
		parent::__construct($message, $values, $code);
		
		Messages::errors($this->errors('validation'));
	}
	
	/**
	 * Returns a merged array of the errors from all the Validation objects in this exception
	 *
	 *     // Will load Model_User errors from messages/orm-validation/user.php
	 *     $e->errors('orm-validation');
	 *
	 * @param   string  $directory Directory to load error messages from
	 * @param   mixed   $translate Translate the message
	 * @return  array
	 * @see generate_errors()
	 */
	public function errors($directory = NULL, $translate = TRUE)
	{
		return $this->generate_errors($this->_objects, $directory, $translate);
	}
	
	/**
	 * Recursive method to fetch all the errors in this exception
	 *
	 * @param  array  $array     Array of Validation objects to get errors from
	 * @param  string $directory Directory to load error messages from
	 * @param  mixed  $translate Translate the message
	 * @return array
	 */
	protected function generate_errors(array $array, $directory, $translate)
	{
		$errors = array();

		foreach ($array as $key => $object)
		{
			if ($object instanceof Validation)
			{
				if ($directory === NULL)
				{
					// Return the raw errors
					$file = NULL;
				}
				else
				{
					$file = trim($directory);
				}

				// Merge in this array of errors
				$errors += $object->errors($file, $translate);
			}
		}

		return $errors;
	}
	
	/**
	 * Returns the protected _objects property from this exception
	 *
	 * @return array
	 */
	public function objects()
	{
		return $this->_objects;
	}

}
