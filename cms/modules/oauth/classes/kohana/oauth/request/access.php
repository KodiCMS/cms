<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * OAuth Access Request
 *
 * @package    Kohana/OAuth
 * @category   Request
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Request_Access extends OAuth_Request {

	protected $name = 'Access';

	protected $required = array(
		'oauth_consumer_key'     => TRUE,
		'oauth_token'            => TRUE,
		'oauth_signature_method' => TRUE,
		'oauth_signature'        => TRUE,
		'oauth_timestamp'        => TRUE,
		'oauth_nonce'            => TRUE,
		'oauth_verifier'         => TRUE,
		'oauth_version'          => TRUE,
	);

	public function execute(array $options = NULL)
	{
		return OAuth_Response::factory(parent::execute($options));
	}

} // End OAuth_Request_Access
