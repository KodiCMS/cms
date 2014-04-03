<?php defined('SYSPATH') or die('No direct access allowed.');

class KodiCMS_Model_Widget_Part {
	
	public $block = NULL;
	protected $_html = NULL;
	
	public function __construct($block, $html)
	{
		$this->block = $block;
		$this->_html = $html;
	}
	
	public function __toString()
	{
		return (string) $this->_html;
	}
}