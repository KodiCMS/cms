<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * OAuth Provider
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
abstract class Kohana_OAuth_Provider {

	/**
	 * Create a new provider.
	 *
	 *     // Load the Twitter provider
	 *     $provider = OAuth_Provider::factory('twitter');
	 *
	 * @param   string   provider name
	 * @param   array    provider options
	 * @return  OAuth_Provider
	 */
	public static function factory($name, array $options = NULL)
	{
		$class = 'OAuth_Provider_'.$name;

		return new $class($options);
	}

	/**
	 * @var  string  provider name
	 */
	public $name;

	/**
	 * @var  array  additional request parameters to be used for remote requests
	 */
	protected $params = array();

	/**
	 * Overloads default class properties from the options.
	 *
	 * Any of the provider options can be set here:
	 *
	 * Type      | Option        | Description                                    | Default Value
	 * ----------|---------------|------------------------------------------------|-----------------
	 * mixed     | signature     | Signature method name or object                | provider default
	 *
	 * @param   array   provider options
	 * @return  void
	 */
	public function __construct(array $options = NULL)
	{
		if (isset($options['signature']))
		{
			// Set the signature method name or object
			$this->signature = $options['signature'];
		}

		if ( ! is_object($this->signature))
		{
			// Convert the signature name into an object
			$this->signature = OAuth_Signature::factory($this->signature);
		}

		if ( ! $this->name)
		{
			// Attempt to guess the name from the class name
			$this->name = strtolower(substr(get_class($this), strlen('OAuth_Provider_')));
		}
	}

	/**
	 * Return the value of any protected class variable.
	 *
	 *     // Get the provider signature
	 *     $signature = $provider->signature;
	 *
	 * @param   string  variable name
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->$key;
	}

	/**
	 * Returns the request token URL for the provider.
	 *
	 *     $url = $provider->url_request_token();
	 *
	 * @return  string
	 */
	abstract public function url_request_token();

	/**
	 * Returns the authorization URL for the provider.
	 *
	 *     $url = $provider->url_authorize();
	 *
	 * @return  string
	 */
	abstract public function url_authorize();

	/**
	 * Returns the access token endpoint for the provider.
	 *
	 *     $url = $provider->url_access_token();
	 *
	 * @return  string
	 */
	abstract public function url_access_token();

	/**
	 * Ask for a request token from the OAuth provider.
	 *
	 *     $token = $provider->request_token($consumer);
	 *
	 * @param   OAuth_Consumer  consumer
	 * @param   array           additional request parameters
	 * @return  OAuth_Token_Request
	 * @uses    OAuth_Request_Token
	 */
	public function request_token(OAuth_Consumer $consumer, array $params = NULL)
	{
		// Create a new GET request for a request token with the required parameters
		$request = OAuth_Request::factory('Token', 'GET', $this->url_request_token(), array(
			'realm'              => $consumer->realm,
			'oauth_consumer_key' => $consumer->key,
			'oauth_callback'     => $consumer->callback,
		));

		if ($params)
		{
			// Load user parameters
			$request->params($params);
		}

		// Sign the request using only the consumer, no token is available yet
		$request->sign($this->signature, $consumer);

		// Create a response from the request
		$response = $request->execute();

		// Store this token somewhere useful
		return OAuth_Token::factory('Request', array(
			'token'  => $response->param('oauth_token'),
			'secret' => $response->param('oauth_token_secret'),
		));
	}

	/**
	 * Get the authorization URL for the request token.
	 *
	 *     $this->request->redirect($provider->authorize_url($token));
	 *
	 * @param   OAuth_Token_Request  token
	 * @param   array                additional request parameters
	 * @return  string
	 */
	public function authorize_url(OAuth_Token_Request $token, array $params = NULL)
	{
		// Create a new GET request for a request token with the required parameters
		$request = OAuth_Request::factory('Authorize', 'GET', $this->url_authorize(), array(
			'oauth_token' => $token->token,
		));

		if ($params)
		{
			// Load user parameters
			$request->params($params);
		}

		return $request->as_url();
	}

	/**
	 * Exchange the request token for an access token.
	 *
	 *     $token = $provider->access_token($consumer, $token);
	 *
	 * @param   OAuth_Consumer       consumer
	 * @param   OAuth_Token_Request  token
	 * @param   array                additional request parameters
	 * @return  OAuth_Token_Access
	 */
	public function access_token(OAuth_Consumer $consumer, OAuth_Token_Request $token, array $params = NULL)
	{
		// Create a new GET request for a request token with the required parameters
		$request = OAuth_Request::factory('Access', 'GET', $this->url_access_token(), array(
			'realm'              => $consumer->realm,
			'oauth_consumer_key' => $consumer->key,
			'oauth_token'        => $token->token,
			'oauth_verifier'     => $token->verifier,
		));

		if ($params)
		{
			// Load user parameters
			$request->params($params);
		}

		// Sign the request using only the consumer, no token is available yet
		$request->sign($this->signature, $consumer, $token);

		// Create a response from the request
		$response = $request->execute();

		$params = $response->params();
		$params['token'] = $response->param('oauth_token');
		$params['secret'] = $response->param('oauth_token_secret');

		// Store this token somewhere useful
		return OAuth_Token::factory('Access', $params);
	}

} // End OAuth_Signature
