<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The PLAINTEXT signature does not provide any security protection and should
 * only be used over a secure channel such as HTTPS.
 *
 * @package    Kohana/OAuth
 * @category   Signature
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */
class Kohana_OAuth_Signature_PLAINTEXT extends OAuth_Signature {

	protected $name = 'PLAINTEXT';

	/**
	 * Generate a plaintext signature for the request _without_ the base string.
	 *
	 *     $sig = $signature->sign($request, $consumer, $token);
	 *
	 * [!!] This method implements [OAuth 1.0 Spec 9.4.1](http://oauth.net/core/1.0/#rfc.section.9.4.1).
	 *
	 * @param   OAuth_Request   request
	 * @param   OAuth_Consumer  consumer
	 * @param   OAuth_Token     token
	 * @return  $this
	 */
	public function sign(OAuth_Request $request, OAuth_Consumer $consumer, OAuth_Token $token = NULL)
	{
		// Use the signing key as the signature
		return $this->key($consumer, $token);
	}

	/**
	 * Verify a plaintext signature.
	 *
	 *     if ( ! $signature->verify($signature, $request, $consumer, $token))
	 *     {
	 *         throw new Kohana_OAuth_Exception('Failed to verify signature');
	 *     }
	 *
	 * [!!] This method implements [OAuth 1.0 Spec 9.4.2](http://oauth.net/core/1.0/#rfc.section.9.4.2).
	 *
	 * @param   string          signature to verify
	 * @param   OAuth_Request   request
	 * @param   OAuth_Consumer  consumer
	 * @param   OAuth_Token     token
	 * @return  boolean
	 * @uses    OAuth_Signature_PLAINTEXT::sign
	 */
	public function verify($signature, OAuth_Request $request, OAuth_Consumer $consumer, OAuth_Token $token = NULL)
	{
		return $signature === $this->key($consumer, $token);
	}

} // End OAuth_Signature_PLAINTEXT
