<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * OAuth Signature
 *
 * @package    Kohana/OAuth
 * @category   Signature
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
abstract class Kohana_OAuth_Signature {

	/**
	 * Create a new signature object by name.
	 *
	 *     $signature = OAuth_Signature::factory('HMAC-SHA1');
	 *
	 * @param   string  signature name: HMAC-SHA1, PLAINTEXT, etc
	 * @param   array   signature options
	 * @return  OAuth_Signature
	 */
	public static function factory($name, array $options = NULL)
	{
		// Create the class name as a base of this class
		$class = 'OAuth_Signature_'.str_replace('-', '_', $name);

		return new $class($options);
	}

	/**
	 * @var  string  signature name: HMAC-SHA1, PLAINTEXT, etc
	 */
	protected $name;

	/**
	 * Return the value of any protected class variables.
	 *
	 *     $name = $signature->name;
	 *
	 * @param   string  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Get a signing key from a consumer and token.
	 *
	 *     $key = $signature->key($consumer, $token);
	 *
	 * [!!] This method implements the signing key of [OAuth 1.0 Spec 9](http://oauth.net/core/1.0/#rfc.section.9).
	 *
	 * @param   OAuth_Consumer  consumer
	 * @param   OAuth_Token     token
	 * @return  string
	 * @uses    OAuth::urlencode
	 */
	public function key(OAuth_Consumer $consumer, OAuth_Token $token = NULL)
	{
		$key = OAuth::urlencode($consumer->secret).'&';

		if ($token)
		{
			$key .= OAuth::urlencode($token->secret);
		}

		return $key;
	}

	abstract public function sign(OAuth_Request $request, OAuth_Consumer $consumer, OAuth_Token $token = NULL);

	abstract public function verify($signature, OAuth_Request $request, OAuth_Consumer $consumer, OAuth_Token $token = NULL);

} // End OAuth_Signature
