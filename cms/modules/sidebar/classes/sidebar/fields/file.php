<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_File extends Sidebar_Fields_Abstract {

	protected $_template = 'file';
	
	protected $_options = array(
		'name', 'label'
	);
	
	public $_field = array(
		'label' => NULL
	);
}