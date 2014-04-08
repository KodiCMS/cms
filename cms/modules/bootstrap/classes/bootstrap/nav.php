<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/components.html#navs
 * @package		Twitter Bootstrap
 * @category	Components
 * @author		ButscHSter
 */
class Bootstrap_Nav extends Bootstrap_Helper_Elements {
	
	const DIVIDER = 'divider-vertical';
	
	/**
	 * You can easily add dividers to your nav links with an empty list item 
	 * and a simple class. Just add this between links:
	 * 
	 *		<li class="divider"></li>
	 * 
	 * @return string
	 */
	public static function divider()
	{
		return '<li'.HTML::attributes(array('class' => Bootstrap_Nav::DIVIDER)).'></li>';
	}

	protected $_template = 'nav';
	
	public function default_attributes()
	{
		return array(
			'class' => 'nav'
		);
	}
	
	public function tabs()
	{
		return $this->attributes('class', 'nav-tabs');
	}
	
	public function pills()
	{
		return $this->attributes('class', 'nav-pills');
	}
	
	public function stacked()
	{
		return $this->attributes('class', 'nav-stacked');
	}
	
	public function lists()
	{
		return $this->attributes('class', 'nav-list');
	}
	
	/**
	 * 
	 * @return \Bootstrap_Dropdown
	 */
	public function add_divider()
	{
		$this->_elements[] = Bootstrap_Nav::divider();
		return $this;
	}
	
	/**
	 * 
	 * @param string $text
	 * @return \Bootstrap_Nav
	 */
	public function add_header( $text )
	{
		$this->_elements[] = '<li class="nav-header">'.$text.'</li>';
		return $this;
	}

	/**
	 * 
	 * @param mixed $element
	 * @param boolean $is_active
	 * @param array $attributes
	 * @param integer $priority
	 * @return \Bootstrap_Nav
	 */
	public function add( $element, $is_active = FALSE, array $attributes = array(), $priority = 0 )
	{
		$element_li = Bootstrap_Nav_Element::factory(array(
			'element' => $element
		))
			->attributes($attributes);
		
		$element->set_parent($element_li);
		
		if($is_active !== FALSE)
		{
			$element_li->attributes('class', 'active');
		}
		
		if( $element instanceof Bootstrap_Element_Button )
		{
			$element->attributes()->delete('class', '^btn');
		}
		
		if( $element instanceof Bootstrap_Dropdown )
		{
			$element_li->attributes('class', 'dropdown');
		}
		
		$this->_add($element_li, $priority);

		return $this;
	}
}