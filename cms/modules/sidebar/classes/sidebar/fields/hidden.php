<?php defined('SYSPATH') or die('No direct script access.');

class Sidebar_Fields_Hidden extends Sidebar_Fields_Input {
	
	protected $_template = 'hidden';
	
	protected $_options = array(
		'value', 'name'
	);

}