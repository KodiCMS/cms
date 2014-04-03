<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The HMAC-SHA1 signature provides secure signing using the HMAC-SHA1
 * algorithm as defined by [RFC2104](http://tools.ietf.org/html/rfc2104).
 * It uses [OAuth_Request::base_string] as the text and [OAuth_Signature::key]
 * as the signing key.
 *
 * @package    Kohana/OAuth
 * @category   Signature
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Signature_HMAC_SHA1 extends OAuth_Signature {

	protected $name = 'HMAC-SHA1';

	/**
	 * Generate a signed hash of the base string using the consumer and token
	 * as the signing key.
	 *
	 *     $sig = $signature->sign($request, $consumer, $token);
	 *
	 * [!!] This method implements [OAuth 1.0 Spec 9.2.1](http://oauth.net/core/1.0/#rfc.section.9.2.1).
	 *
	 * @param   OAuth_Request   request
	 * @param   OAuth_Consumer  consumer
	 * @param   OAuth_Token     token
	 * @return  string
	 * @uses    OAuth_Signature::key
	 * @uses    OAuth_Request::base_string
	 */
	public function sign(OAuth_Request $request, OAuth_Consumer $consumer, OAuth_Token $token = NULL)
	{
		// Get the signing key
		$key = $this->key($consumer, $token);

		// Get the base string for the signature
		$base_string = $request->base_string();

		// Sign the base string using the key
		return base64_encode(hash_hmac('sha1', $base_string, $key, TRUE));
	}

	/**
	 * Verify a HMAC-SHA1 signature.
	 *
	 *     if ( ! $signature->verify($signature, $request, $consumer, $token))
	 *     {
	 *         throw new Kohana_OAuth_Exception('Failed to verify signature');
	 *     }
	 *
	 * [!!] This method implements [OAuth 1.0 Spec 9.2.2](http://oauth.net/core/1.0/#rfc.section.9.2.2).
	 *
	 * @param   string          signature to verify
	 * @param   OAuth_Request   request
	 * @param   OAuth_Consumer  consumer
	 * @param   OAuth_Token     token
	 * @return  boolean
	 * @uses    OAuth_Signature_HMAC_SHA1::sign
	 */
	public function verify($signature, OAuth_Request $request, OAuth_Consumer $consumer, OAuth_Token $token = NULL)
	{
		return $signature === $this->sign($request, $consumer, $token);
	}

} // End OAuth_Signature_HMAC_SHA1
