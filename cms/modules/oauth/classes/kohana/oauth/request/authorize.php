<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * OAuth Authorization Request
 *
 * @package    Kohana/OAuth
 * @category   Request
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Request_Authorize extends OAuth_Request {

	protected $name = 'request';

	// http://oauth.net/core/1.0/#rfc.section.6.2.1
	protected $required = array(
		'oauth_token' => TRUE,
	);

	public function execute(array $options = NULL)
	{
		return Request::current()->redirect($this->as_url());
	}

} // End OAuth_Request_Authorize
