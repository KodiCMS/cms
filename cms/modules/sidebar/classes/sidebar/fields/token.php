<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Token extends Sidebar_Fields_Hidden {

	public function __construct($field = array(), $render = TRUE) 
	{
		if(!isset($field['value']))
		{
			$field['value'] = Security::token();
		}
		
		if(!isset($field['name']))
		{
			$field['name'] = 'security_token';
		}

		parent::__construct($field, $render);
	}
}