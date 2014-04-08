<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Navigation
 * @category	Model
 * @author		ButscHSter
 */
class Model_Navigation {

	/**
	 *
	 * @var Model_Navigation_Section
	 */
	protected static $_root_section = NULL;
	
	/**
	 *
	 * @var Model_Navigation_Page 
	 */
	public static $current = NULL;
	
	/**
	 * 
	 * @param array $sitemap
	 */
	public static function init(array $sitemap)
	{
		foreach ($sitemap as $section)
		{
			if(!isset($section['name'])) continue;

			$section_object = self::get_section($section['name']);
			
			if(!empty($section['children']))
			{
				$section_object->add_pages($section['children']);
			}
		}
	}

	/**
	 * 
	 * @param string $name
	 * @return Model_Navigation_Section
	 */
	public static function get_section($name, Model_Navigation_Section $parent = NULL)
	{
		if($parent === NULL)
		{
			if(self::$_root_section === NULL)
			{
				self::$_root_section = new Model_Navigation_Section(array(
					'name' => 'root'
				));
			}
			
			$parent = & self::$_root_section;
		}

		$section = $parent->find_section($name);

		if( $section === NULL )
		{
			$section = new Model_Navigation_Section(array(
				'name' => $name
			));

			$parent->add_page($section);
		}
		
		return $section;
	}

	/**
	 * 
	 * @param string $section
	 * @param string $name
	 * @param string $uri
	 * @param array $permissions
	 * @param integer $priority
	 */
	public static function add_section( $section = 'Other', $name, $uri, $priority = 0, $counter = 0 )
	{
		self::get_section( $section )
			->add_page(new Model_Navigation_Page(array(
				'name' => $name,
				'url' => $uri,
				'counter' => (int) $counter,
			)), $priority);
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function get($uri = NULL)
	{
//		self::sort();
		
		if($uri === NULL)
		{
			$uri = Request::current()->uri();
		}
		
		if($uri == ADMIN_DIR_NAME)
		{
			$uri .= '/' . Config::get('site', 'default_tab');
		}

		$uri = strtolower($uri);

		$break = FALSE;
		
		self::$_root_section->find_active_page_by_uri($uri);

		return self::$_root_section;
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param array $data
	 */
	public static function update($uri, array $data)
	{
		$page = self::find_page_by_uri($uri);
		
		if($page instanceof Model_Navigation_Page)
		{
			foreach ($data as $key => $value)
			{
				$page->{$key} = $value;
			}
		}
	}

	/**
	 * 
	 * @param string $uri
	 * @return NULL|Model_Navigation_Page
	 */
	public static function & find_page_by_uri($uri)
	{
		$_page = NULL;
		foreach ( self::$_root_section->sections() as $section )
		{
			if( $page = $section->find_page_by_uri( $uri ) )
			{
				return $page;
			}
		}
		
		return $_page;
	}

	public static function sort()
	{
		uasort(self::$_root_section->sections(), function($a, $b)
		{
			if ($a->id() == $b->id()) 
			{
				return 0;
			}

			return ($a->id() < $b->id()) ? -1 : 1;
		});
	}
	
	/**
	 * 
	 * @param Bootstrap_Helper_Elements $nav
	 * @param array $sections
	 * @param boolean $is_active
	 * @return \Bootstrap_Helper_Elements
	 */
	public static function build_dropdown(Bootstrap_Helper_Elements $nav, array $sections, & $is_active = FALSE)
	{
		foreach ( $sections as $section )
		{
			$is_active = FALSE;
			if(count($section) == 0) continue;

			$dropdown = Bootstrap_Navbar_Dropdown::factory(array(
				'title' => $section->name(),
			))->icon($section->icon);

			foreach ( $section as $page )
			{
				if($page->divider === TRUE)
				{
					$dropdown->add_divider();
				}

				$dropdown->add(Bootstrap_Element_Button::factory(array(
						'href' => $page->url(), 'title' => $page->name()
				))->attributes('data-counter', $page->counter)->icon($page->icon), $page->is_active());

				if($page->is_active())
				{
					$is_active = TRUE;
				}
				
				if(count($section->sections()) > 0)
				{
					$is_sub_active = FALSE;
					$dropdown = self::build_dropdown($dropdown, $section->sections(), $is_sub_active);
					
					if($is_sub_active === TRUE)
					{
						$is_active = TRUE;
					}
				}
			}

			$nav->add($dropdown, $is_active);
		}
		
		return $nav;
	}
}