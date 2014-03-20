<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Page_Sitemap {
	
	/**
	 *
	 * @var Model_Page_Sitemap 
	 */
	protected static $_sitemap = NULL;
	
	/**
	 * 
	 * @return Model_Page_Sitemap
	 */
	public static function get( $include_hidden = FALSE)
	{
		if(Model_Page_Sitemap::$_sitemap === NULL)
		{
			$pages = ORM::factory('page')
				->order_by('parent_id', 'asc')
				->order_by('position', 'asc');
			
			if(( bool) $include_hidden === FALSE)
			{
				$pages->where('status_id', 'in', array(Model_Page::STATUS_PASSWORD_PROTECTED, Model_Page::STATUS_PUBLISHED));
			}
			
			$res_pages = $pages->find_all();
			
			$current_page = Context::instance()->get_page();

			if($current_page instanceof Model_Page_Front)
			{
				$current_page = $current_page->id;
			}

			$_pages = array();
			foreach ($res_pages as $page)
			{
				$_pages[$page->id] = $page->as_array();
				$_pages[$page->id]['uri'] = ''; //'/' . $page->get_uri();
				$_pages[$page->id]['url'] = '';
				$_pages[$page->id]['slug'] = $page->slug;
				$_pages[$page->id]['level'] = 0;
				$_pages[$page->id]['is_active'] = TRUE;//URL::match($_pages[$page->id]['uri']);
			}

			$pages = array();
			foreach ($_pages as & $page)
			{
				$pages[$page['parent_id']][] = & $page;
			}

			foreach ($_pages as & $page)
			{
				if(isset($pages[$page['id']]))
				{
					foreach ($pages[$page['id']] as & $_page)
					{
						$_page['level'] = $page['level'] + 1;
						$_page['parent'] = $page;
						
						$_page['uri'] = $page['uri'] . '/' . $_page['slug'];
						$_page['url'] = URL::frontend($_page['uri']);
						$_page['is_active'] = URL::match($_page['uri']);

						if(empty($_page['layout_file']))
						{
							$_page['layout_file'] = $page['layout_file'];
						}
						
						if($_page['is_active'])
							$page['is_active'] = TRUE;
					}

					$page['childs'] = $pages[$page['id']];
				}
			}

			Model_Page_Sitemap::$_sitemap = new Model_Page_Sitemap(reset($pages));
		}

		return clone(Model_Page_Sitemap::$_sitemap);
	}
	
	/**
	 *
	 * @var array 
	 */
	protected $_pages = array();

	/**
	 * 
	 * @param array $pages
	 */
	protected function __construct( array $pages = array())
	{
		$this->_pages = $pages;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return \Model_Page_Sitemap
	 */
	public function find( $id )
	{
		$this->_pages = $this->_find( $this->_pages, $id );
		
		return $this;
	}
	

	/**
	 * 
	 * @param array $array
	 * @param integer $id
	 * @return array
	 */
	protected function _find( $array, $id )
	{
		$found = array();
		foreach($array as $page)
		{
			if($page['id'] == $id)
			{
				return array($page);
			}
			
			if( ! empty($page['childs']))
			{
				$found = $this->_find($page['childs'], $id);
				
				if(!empty($found)) 
				{
					return $found;
				}
			}
		}
		
		return $found;
	}
	
	/**
	 * 
	 * @return \Model_Page_Sitemap
	 */
	public function children()
	{
		if( ! empty($this->_pages[0]['childs']))
		{
			$this->_pages = $this->_pages[0]['childs'];
		}
		else
		{
			$this->_pages = array();
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return \Model_Page_Sitemap
	 */
	public function exclude( array $ids )
	{
		if( !empty($ids) )
			$this->_exclude( $this->_pages, $ids );

		return $this;
	}
	
	/**
	 * 
	 * @param array $array
	 * @param array $ids
	 * @return array
	 */
	public function _exclude( & $array, array $ids )
	{
		foreach($array as $i => & $page)
		{
			if( in_array($page['id'], $ids) )
			{
				unset($array[$i]);
			}
			
			if( !empty($page['childs']))
			{
				$this->_exclude($page['childs'], $ids);
			}
		}
	}

	/**
	 * 
	 * @return array
	 */
	public function as_array( $childs = TRUE )
	{
		if( $childs === FALSE )
		{
			foreach($this->_pages as & $page)
			{
				if(isset($page['childs']))
					unset( $page['childs'] );
			}
		}
			
		return $this->_pages;
	}

	/**
	 * 
	 * @return array
	 */
	public function flatten( $childs = TRUE )
	{
		return $this->_flatten( $this->_pages, $childs );
	}
	
	/**
	 * 
	 * @param array $array
	 * @param boolean $childs
	 * @param array $return
	 * @return array
	 */
	public function _flatten( array $array, $childs = TRUE, & $return = array() )
	{
		foreach( $array as $page )
		{
			$return[$page['id']] = $page;
			
			if( $childs !== FALSE AND !empty($page['childs']))
			{
				$this->_flatten( $page['childs'], $childs, $return );
			}
			
			$return[$page['id']]['childs'] = array();
		}
		
		return $return;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function breadcrumbs()
	{
		return array_reverse($this->_breadcrumbs( $this->_pages[0] ));
	}
	
	/**
	 * 
	 * @param array $page
	 * @param array $crumbs
	 * @return type
	 */
	protected function _breadcrumbs( array $page, &$crumbs = array() )
	{
		$crumbs[] = $page;
			
		if( !empty($page['parent']) )
			$this->_breadcrumbs( $page['parent'], $crumbs );
		
		return $crumbs;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param string $selected
	 * @param array $attributes
	 * @return string
	 */
	public function select_choises()
	{
		$pages = $this->flatten();
		
		$options = array();
		foreach ($pages as $page)
		{
			$options[$page['id']] = str_repeat('- ', $page['level'] * 2) . $page['title'];
		}
		
		return $options;
	}
}