<?php defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth Access Token
 *
 * @package    Kohana/OAuth
 * @category   Token
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Token_Access extends OAuth_Token {

	protected $name = 'Access';

	/**
	 * @var  string  token secret
	 */
	protected $secret;

	protected $required = array(
		'token',
		'secret',
	);

} // End OAuth_Token_Access
