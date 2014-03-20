<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Checkbox extends Sidebar_Fields_Abstract {
	
	protected $_template = 'checkbox';

	protected $_options = array(
		'value', 'name', 'label', 'checked'
	);
	
	public $_field = array(
		'value' => NULL,
		'checked' => FALSE,
		'label' => NULL
	);
}