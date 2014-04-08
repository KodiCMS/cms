<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_System_SSO extends Controller_System_Frontend {
	
	public $template = 'system/frontend';

	/**
	 * @var SSO
	 */
	protected $_sso;

	/**
	 * @var Model_Auth_Data
	 */
	protected $_user;

	/**
	 * @var Session
	 */
	protected $_session;

	public function before()
	{
		parent::before();

		$this->_session = Session::instance();

		$this->_sso = SSO::instance();
		$this->_user = $this->_sso->get_user();
	}
}