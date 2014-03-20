<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Separator extends Sidebar_Fields_Abstract {

	protected $_template = 'separator';
	
	public function __construct($header = NULL, $render = TRUE) 
	{
		$this->_view = View::factory('sidebar/fields/'.$this->_template, array(
			'header' => $header
		));
	}
}