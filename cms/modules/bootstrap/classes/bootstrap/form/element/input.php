<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Form
 * @author		ButscHSter
 */
class Bootstrap_Form_Element_Input extends Bootstrap_Form_Helper_Elements {
	
	/**
	 * Use relative sizing classes like .input-large 
	 * 
	 * In future versions, we'll be altering the use of these relative input 
	 * classes to match our button sizes. For example, .input-large will 
	 * increase the padding and font-size of an input.
	 */
	const MINI = 'input-mini';
	const SMALL = 'input-small';
	const MEDIUM = 'input-medium';
	const LARGE = 'input-large';
	const XLARGE = 'input-xlarge';
	const XXLARGE = 'input-xxlarge'; 
	
	/**
	 * Make any <input> or <textarea> element behave like a block level element.
	 * 
	 *		<input class="input-block-level" type="text">
	 */
	const BLOCK_LEVEL = 'input-block-level';
	
	/**
	 * Wrap an .add-on and an input with one of two classes to prepend or append text to an input.
	 * 
	 *		<span class="add-on">.00</span>
	 * 
	 * @param string $text
	 * @return string
	 */
	public static function add_on( $text )
	{
		return '<span'.HTML::attributes(array('class' => 'add-on')).'>' . $text . '</span>';
	}
	
	/**
	 * Present data in a form that's not editable without using actual form markup.
	 * 
	 *		<span class="input-xlarge uneditable-input">Some value here</span>
	 * 
	 * @param string $text
	 * @param array $attributes
	 * @return string
	 */
	public static function uneditable( $text, $size = 'input-xlarge' )
	{
		return '<span'.HTML::attributes(array('class' => 'uneditable-input ' . $size)).'>' . $text . '</span>';
	}
	
	protected $_template = 'form/element/input';
	
	public function required()
	{
		return array('name');
	}
	
	public function default_attributes()
	{
		return array(
			'class' => Bootstrap_Form_Element_Input::MEDIUM
		);
	}
	
	/**
	 *
	 * @var array 
	 */
	protected $_append = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_prepend = array();
	
	/**
	 * 
	 * @param string $title
	 * @param array $attributes
	 * @return Bootstrap_Form_Element_Input
	 */
	public function label( $title, array $attributes = array() )
	{
		$label = Bootstrap_Form_Element_Label::factory(array(
				'for' => $this->attributes('id'), 
				'title' => $title
			), $attributes)
			->set_parent( $this );
		
		return $this->set('label', $label);
	}
	
	/**
	 * 
	 * @param string $text
	 * @return Bootstrap_Form_Element_Input
	 */
	public function placeholder( $text )
	{
		return $this->attributes('placeholder', $text);
	}

	/**
	 * 
	 * @param string $string
	 * @return \Bootstrap_Form_Element_Input
	 */
	public function append( $string )
	{
		if( $string instanceof Bootstrap_Abstract)
		{
			$string->set_parent( $this );
		}

		$this->_append[] = $string;
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $string
	 * @return \Bootstrap_Form_Element_Input
	 */
	public function prepend( $string )
	{
		if( $string instanceof Bootstrap_Abstract)
		{
			$string->set_parent( $this );
		}
		
		$this->_prepend[] = $string;
		
		return $this;
	}
	
	protected function _build_content() 
	{
		parent::_build_content();
		
		$this->_content = View::factory($this->_template_folder . '/form/element/input/extend')
			->set('input', Form::input($this->get('name'), $this->get('value'), 
				$this->attributes()->as_array()))
			->set('append', $this->_append)
			->set('prepend', $this->_prepend);
	}
}