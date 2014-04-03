<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * OAuth Consumer
 *
 * @package    Kohana/OAuth
 * @category    Base
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Consumer {

	/**
	 * Create a new consumer object.
	 *
	 *     $consumer = OAuth_Consumer::factory($options);
	 *
	 * @param   array  consumer options, key and secret are required
	 * @return  OAuth_Consumer
	 */
	public static function factory(array $options = NULL)
	{
		return new OAuth_Consumer($options);
	}

	/**
	 * @var  string  consumer key
	 */
	protected $key;

	/**
	 * @var  string  consumer secret
	 */
	protected $secret;

	/**
	 * @var  string  callback URL for OAuth authorization completion
	 */
	protected $callback;

	/**
	* @var  string  optional realm for the OAuth request
	*/
	protected $realm;

	/**
	 * Sets the consumer key and secret.
	 *
	 * @param   array  consumer options, key and secret are required
	 * @return  void
	 */
	public function __construct(array $options = NULL)
	{
		if ( ! isset($options['key']))
		{
			throw new Kohana_OAuth_Exception('Required option not passed: :option',
				array(':option' => 'key'));
		}

		if ( ! isset($options['secret']))
		{
			throw new Kohana_OAuth_Exception('Required option not passed: :option',
				array(':option' => 'secret'));
		}

		$this->key = $options['key'];

		$this->secret = $options['secret'];

		if (isset($options['callback']))
		{
			$this->callback = $options['callback'];
		}

		if (isset($options['realm']))
		{
			$this->realm = $options['realm'];
		}
	}

	/**
	 * Return the value of any protected class variable.
	 *
	 *     // Get the consumer key
	 *     $key = $consumer->key;
	 *
	 * @param   string  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Change the consumer callback.
	 *
	 * @param   string  new consumer callback
	 * @return  $this
	 */
	public function callback($callback)
	{
		$this->callback = $callback;

		return $this;
	}

} // End OAuth_Consumer
