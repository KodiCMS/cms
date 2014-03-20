<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Radio extends Sidebar_Fields_Abstract {
	
	protected $_template = 'radio';
	
	protected $_options = array(
		'name', 'label', 'value', 'selected'
	);
	
	public $_field = array(
		'selected' => FALSE
	);
}