<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Controller_Oauth2 extends Controller_Account {
	/**
	 * @var OAuth2
	 */
	protected $_oauth;
		/**
	 * @var OAuth_Token
	 */
	protected $_token;
	/**
	 * @var OAuth2_Provider
	 */
	protected $_provider;
	/**
	 * @var OAuth_Consumer
	 */
	protected $_consumer;

	protected $_access_token_key = 'access_token';

	protected $_request_params = array();

	protected function _login_params()
	{
		$this->_token = $this->_session->get($this->_access_token_key);

		if ( empty($this->_token))
		{
			return array();
		}

		return array(
			// provider name
			'OAuth2.'.$this->name,
			// access token
			$this->_token,
		);
	}

	/**
	 * @throws HTTP_Exception_301
	 */
	protected function _do_login()
	{
		// clear old access tokens
		$this->_session->delete($this->_access_token_key);
		$callback = $this->_changed_uri('identify');

		$this->_consumer->callback($callback);

		HTTP::redirect($this->_provider->authorize_url($this->_consumer, $this->_request_params));
	}

	public $name;

	public $type = 'OAuth2';

	public function before()
	{
		parent::before();
		$this->_oauth = new OAuth2;
		$this->_consumer = OAuth2_Client::factory(Kohana::$config->load('oauth.accounts.' . $this->name));
		$this->_provider = $this->_oauth->provider($this->name);
	}

	public function action_token()
	{
		$this->_do_login();
	}

	public function action_identify()
	{
		$code = $this->request->query('code');
		if ( ! $code)
		{
			Messages::errors(__('Ooops, something was wrong. Cant complete authentication'));
			$this->_go_back('account/identify');
		}

		$this->_consumer->callback($this->_changed_uri(array()));

		$this->_token = $this->_provider->access_token($this->_consumer, $code);

		$this->_session->set($this->_access_token_key, $this->_token);

		$this->_go_back('account/identify');
	}

}