<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Header extends Sidebar_Fields_Abstract {

	protected $_template = 'header';
	
	protected $_options = array(
		'label'
	);
	
	public function __construct($label) 
	{
		$this->_field['label'] = $label;
		$this->_view = View::factory('sidebar/fields/'.$this->_template);
	}
}