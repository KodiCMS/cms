<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Category extends Model_MPTT 
{
	protected $_table_name = 'categories';
	protected $path_calculation_enabled = TRUE;
	
	public function labels()
	{
		return array(
			'name'				=> __('Category name'),
			'path_part'			=> __('Slug'),
		);
	}
	
	public function rules() 
	{
		$rules = array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 255))
			),
			'path_part' => array(
				array('not_empty'),
				array('max_length', array(':value', 100))
			)
		);
		return $rules;
	}
	
	public function filters()
	{
		return array(
			$this->path_part_column => array(
				array('URL::title'),
				array('strtolower')
			),
			$this->path_column => array(
				array('URL::title'),
				array('strtolower')
			),
			$this->left_column => array(
				array('intval')
			),
			$this->right_column => array(
				array('intval')
			),
			$this->level_column => array(
				array('intval')
			),
			$this->scope_column => array(
				array('intval')
			)
		);		
	}
}
