<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Mail Queue Body Model. Contains the actual content of the email. Splitting them off improves performance.
 * @package		KodiCMS/EmailQueue
 * @category	Model
 * @author		ButscHSter
 */
class Model_Email_Queue_Body extends ORM
{
	public function rules()
	{
		return array(
			'body' => array(
				array('not_empty'),
			),
		);
	}
}