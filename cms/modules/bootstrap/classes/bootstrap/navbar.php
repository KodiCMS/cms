<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/components.html#navbar
 * 
 * @package		Twitter Bootstrap
 * @category	Components
 * @author		ButscHSter
 */
class Bootstrap_Navbar extends Bootstrap_Helper_Elements {

	const DIVIDER = 'divider-vertical';
	
	/**
	 *		<li class="active">
	 *			<a href="#">Home</a>
	 *		</li>
	 */
	const ACTIVE_LINK = 'active';
	
	/**
	 * To properly style and position a form within the navbar, add the 
	 * appropriate classes as shown below. 
	 * 
	 * For a default form, include .navbar-form and either .pull-left 
	 * or .pull-right to properly align it.
	 * 
	 *		<form class="navbar-form pull-left">
	 *			<input type="text" class="span2">
	 *			<button type="submit" class="btn">Submit</button>
	 *		</form>
	 */
	const FORM = 'navbar-form';
	
	/**
	 * For a more customized search form, add .navbar-search to the form 
	 * and .search-query to the input for specialized styles in the navbar.
	 * 
	 *		<form class="navbar-search pull-left">
	 *			<input type="text" class="search-query" placeholder="Search">
	 *		</form>
	 */
	const SEARCH = 'navbar-search';
	
	/**
	 * Wrap strings of text in an element with .navbar-text, usually on a <p> 
	 * tag for proper leading and color.
	 */
	const TEXT = 'navbar-text';
	
	/**
	 * Add .navbar-fixed-top and remember to account for the hidden area 
	 * underneath it by adding at least 40px padding to the <body>. 
	 * Be sure to add this after the core Bootstrap CSS and before the 
	 * optional responsive CSS.
	 * 
	 *		<div class="navbar navbar-fixed-top"> ... </div>
	 */
	const FIXED_TOP = 'navbar-fixed-top';
	
	/**
	 * Add .navbar-fixed-bottom instead.
	 * 
	 *		<div class="navbar navbar-fixed-bottom"> ... </div>
	 */
	const FIXED_BOTTOM = 'navbar-fixed-bottom';
	
	/**
	 * Create a full-width navbar that scrolls away with the page by 
	 * adding .navbar-static-top. Unlike the .navbar-fixed-top class, 
	 * you do not need to change any padding on the body.
	 * 
	 *		<div class="navbar navbar-static-top"> ... </div>
	 */
	const STATIC_TOP = 'navbar-static-top';
	
	/**
	 * Modify the look of the navbar by adding .navbar-inverse.
	 * 
	 *		<div class="navbar navbar-inverse"> ... </div>
	 */
	const INVERSE = 'navbar-inverse';
	
	
	/**
	 * A simple link to show your brand or project name only requires an 
	 * anchor tag.
	 * 
	 *		<a class="brand" href="#">Project name</a>
	 * 
	 * @param string $title
	 * @param string $url
	 * @param array $attributes
	 * @return string
	 */
	public static function brand( $title, $url = '#', $attributes = array() )
	{	
		if(isset($attributes['class']))
			$attributes['class'] .= ' brand';
		else
			$attributes['class'] = ' brand';

		return HTML::anchor($url, $title, $attributes);
	}
	
	/**
	 * You can easily add dividers to your nav links with an empty list item 
	 * and a simple class. Just add this between links:
	 * 
	 *		<li class="divider-vertical"></li>
	 * 
	 * @return string
	 */
	public static function divider()
	{
		return '<li'.HTML::attributes(array('class' => Bootstrap_Navbar::DIVIDER)).'></li>';
	}
	
	protected $_template = 'navbar';
	
	public function default_attributes()
	{
		return array(
			'class' => 'navbar'
		);
	}

	public function fix_top()
	{
		return $this->attributes('class', 'navbar-fixed-top');
	}
	
	public function fix_bottom()
	{
		return $this->attributes('class', 'navbar-fixed-bottom');
	}
	
	public function static_top()
	{
		return $this->attributes('class', 'navbar-static-top');
	}
	
	public function inverse()
	{
		return $this->attributes('class', 'navbar-inverse');
	}

	public function add( $element, $priority = 0 )
	{
		if( $element instanceof Bootstrap_Form_Search )
		{
			$element->attributes('class', 'navbar-search');
		}
		else if( $element instanceof Bootstrap_Form )
		{
			$element->attributes('class', 'navbar-form');
		}

		$this->_add($element, $priority);

		return $this;
	}
}