<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/Users
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_User_Profile extends ORM {

	protected $_created_column = array(
		'column' => 'created_on',
		'format' => 'Y-m-d H:i:s'
	);

    protected $_has_one = array(
		'user' => array('model' => 'user'),
    );

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 32))
			),
		);
	}
	
	public function labels()
	{
		return array(
			'name'			=> __('Name'),
			'locale'		=> __('Interface language'),
			'notice'        => __('Subscribe to email notifications')
		);
	}
	
	public function form_columns()
	{
		return array(
			'locale' => array(
				'type' => 'select',
				'choises' => 'I18n::available_langs'
			),
			'notice' => array(
				'type' => 'checkbox',
				'checked' => FALSE,
				'value' => 1
			)
		);
	}
}