<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Input extends Sidebar_Fields_Abstract {
	
	protected $_template = 'input';

	protected $_options = array(
		'value', 'name', 'label'
	);
	
	public $_field = array(
		'value' => NULL
	);
}