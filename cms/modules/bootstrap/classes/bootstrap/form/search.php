<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Search extends Bootstrap_Form {

	public function default_attributes()
	{
		$array = parent::default_attributes();
		
		$array['class'] = Bootstrap_Form::SEARCH;
		return $array;
	}
	
	protected function _build_content() 
	{
		parent::_build_content();

		$input = Bootstrap_Form_Element_Input::factory(array(
				'name' => 'query',
				'value' => $this->get('query')
			))
			->attributes('class', Bootstrap_Form::SEARCH_QUERY)
			->append(
				Bootstrap_Form_Element_Button::factory(array(
					'name' => 'search_button',
					'title' =>  $this->get('button_title', __('Search'))
				))
				->icon( 'search' )
			);
				
		$this
			->add( $input );
	}
}