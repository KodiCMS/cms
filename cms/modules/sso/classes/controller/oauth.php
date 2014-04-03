<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Controller_Oauth extends Controller_Account {

	/**
	 * @var  OAuth
	 */
	protected $_oauth;
	/**
	 * @var OAuth_Token
	 */
	protected $_token;
	/**
	 * @var OAuth_Provider
	 */
	protected $_provider;
	/**
	 * @var OAuth_Consumer
	 */
	protected $_consumer;

	protected $_config;

	protected $_request_params = array();
	protected $_access_params = array();

	protected $_request_token_key = 'request_token';
	protected $_access_token_key  = 'access_token';

	protected function _login_params()
	{
		$this->_token = $this->_session->get($this->_access_token_key);

		if ( empty($this->_token))
		{
			return array();
		}

		return array(
			// provider name
			'OAuth.'.$this->name,
			// access token
			$this->_token,
		);
	}

	public $name;

	public $type = 'OAuth';

	public function before()
	{
		parent::before();
		$this->_oauth = new OAuth;
		$this->_config = Kohana::$config->load('oauth.accounts.'.$this->name);
		$this->_consumer =  OAuth_Consumer::factory($this->_config);
		$this->_provider = OAuth_Provider::factory($this->name, $this->_config);
	}

	public function action_index()
	{
		$this->go($this->_changed_uri('login'));
	}

	/**
	 * @throws HTTP_Exception_301
	 */
	protected function _do_login()
	{
		// clear old tokens
		$this->_session->delete($this->_access_token_key);
		$this->_session->delete($this->_request_token_key);
		// where to go back with provider token
		$this->_consumer->callback($this->_changed_uri('identify'));
		// get request token
		$token = $this->_provider->request_token($this->_consumer, $this->_request_params);
		// save request token
		$this->_session->set($this->_request_token_key, $token);
		// trying to get Oauth verifier
		$this->go($this->_provider->authorize_url($token));
	}

	public function action_token()
	{
		$this->_do_login();
	}

	public function action_identify()
	{
		$this->_token = $this->_session->get($this->_request_token_key);

		if ( ! is_object($this->_token))
		{
			Messages::errors(__('Authentication failed due wrong token'));
			// Send the user back to the beginning
			$this->_go_back('account/identify');
		}
		elseif (is_object($this->_token) AND $this->_token->token !== $this->request->query('oauth_token'))
		{
			$this->_session->delete($this->_request_token_key);

			Messages::errors(__('Authentication failed due wrong token'));
			// Send the user back to the beginning
			$this->_go_back('account/identify');
		}

		// Get the verifier
		$verifier = Arr::get($_GET, 'oauth_verifier');

		// Store the verifier in the token
		$this->_token->verifier($verifier);
		// Exchange the request token for an access token
		$this->_token = $this->_provider->access_token($this->_consumer, $this->_token, $this->_access_params);
		// Store the access token
		$this->_session->set($this->_access_token_key, $this->_token);

		$this->_go_back('account/identify');
	}
}