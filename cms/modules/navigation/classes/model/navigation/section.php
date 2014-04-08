<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Navigation
 * @category	Model
 * @author		ButscHSter
 */
class Model_Navigation_Section extends Model_Navigation_Abstract implements Countable, Iterator {
	
	/**
	 *
	 * @var array
	 */
	protected $_pages = array();

	/**
	 *
	 * @var array 
	 */
	protected $_sections = array();

	/**
	 *
	 * @var integer
	 */
	protected $_current_key = 0;
	
	/**
	 * 
	 * @param array $options
	 * @return Model_Navigation_Section
	 */
	public static function factory($data = array())
	{
		return new Model_Navigation_Section($data);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function id()
	{
		return Arr::get($this->_params, 'name');
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_pages()
	{
		return $this->_pages;
	}
	
	/**
	 * 
	 * @param array $pages
	 * @return \Model_Navigation_Section
	 */
	public function add_pages( array $pages )
	{
		foreach ($pages as $page)
		{
			if(isset($page['children']))
			{
				$section = Model_Navigation::get_section($page['name'], $this);
				if(isset($page['icon']))
					$section->icon = $page['icon'];

				if(count($page['children']) > 0)
				{
					$section->add_pages($page['children']);
				}
			}
			else
			{
				$page = new Model_Navigation_Page($page);
				if(ACL::check( $page->permissions )) 
					$this->add_page( $page );
			}
		}
		
		return $this;
	}

	/**
	 * 
	 * @param Model_Navigation_Page $page
	 * @param integer $priority
	 * @return \Model_Navigation_Section
	 */
	public function add_page(Model_Navigation_Abstract & $page, $priority = 0)
	{
		$priority = (int) $priority;
		
		if(isset($page->priority))
		{
			$priority = (int) $page->priority;
		}
		
		if($page instanceof Model_Navigation_Section)
		{		
			$this->_sections[] = $page;
			$page->set_section($this);
		}
		else
		{
			if ( isset( $this->_pages[$priority] ) )
			{
				while ( isset( $this->_pages[$priority] ) )
				{
					$priority++;
				}
			}			
		
			$this->_pages[$priority] = $page;
		}
		
		$page->set_section($this);

		return $this
			->update()
			->sort();
	}
	
	/**
	 * 
	 * @param string $uri
	 * @return boolean
	 */
	public function find_active_page_by_uri($uri)
	{
		$found = FALSE;
		
		foreach ( $this->get_pages() as $page )
		{
			$url = $page->url();

			$len = strpos($url, ADMIN_DIR_NAME);
			if($len !== FALSE)
			{
				$len += strlen(ADMIN_DIR_NAME);
			}
			$url = substr($url, $len);

			if ( !empty($url) AND strpos($uri, ltrim($url, '/')) !== FALSE )
			{
				$page->set_active();

				Model_Navigation::$current = & $page;

				$found = TRUE;
				break;
			}
		}
		
		if($found === FALSE)
		{
			foreach ($this->_sections as $section)
			{
				$found = $section->find_active_page_by_uri($uri);
				if($found !== FALSE)
				{
					return $found;
				}
			}
		}
		
		return $found;
	}

	/**
	 * 
	 * @param type $name
	 * @return Model_Navigation_Section
	 */
	public function find_section($name)
	{
		foreach ($this->_sections as $section)
		{
			if($section->id() == $name)
			{
				return $section;
			}
		}
		
		foreach ($this->_sections as $section)
		{
			$found = $section->find_section($name);
			if($found !== NULL)
			{
				return $found;
			}
		}
		
		return NULL;
	}

	/**
	 * 
	 * @param string $uri
	 * @return null|Model_Navigation_Page
	 */
	public function & find_page_by_uri( $uri )
	{
		$_page = NULL;
		
		foreach ($this->get_pages() as $page)
		{
			if($page->url() == $uri)
			{
				return $page;
			}
		}
		
		foreach ($this->_sections as $section)
		{
			$found = $section->find_page_by_uri($uri);
			if($found !== NULL)
			{
				return $found;
			}
		}
		
		return $_page;
	}
	
	/**
	 * 
	 * @return \Model_Navigation_Section
	 */
	public function update()
	{
		$this->counter = 0;
		$this->permissions = array();

		foreach ($this->get_pages() as $page)
		{
			$this->counter += (int) $page->counter;
		}
		
		return $this;
	}

	/**
	 * 
	 * @return \Model_Navigation_Section
	 */
	public function sort()
	{
		uasort($this->_sections, function($a, $b)
		{
			if ($a->id() == $b->id()) 
			{
				return 0;
			}

			return ($a->id() < $b->id()) ? -1 : 1;
		});
		
		ksort($this->_pages);

		return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function sections()
	{
		return $this->_sections;
	}
	
	/**
	 * Implements [Countable::count], returns the total number of rows.
	 *
	 *     echo count($result);
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->_pages);
	}

	/**
	 * Implements [Iterator::key], returns the current row number.
	 *
	 *     echo key($result);
	 *
	 * @return  integer
	 */
	public function key()
	{
		return key($this->_pages);
	}
	
	/**
	 * Implements [Iterator::key], returns the current breadcrumb item.
	 *
	 *     echo key($result);
	 *
	 * @return  integer
	 */
	public function current()
	{
		return current($this->_pages);
	}

	/**
	 * Implements [Iterator::next], moves to the next row.
	 *
	 *     next($result);
	 *
	 * @return  $this
	 */
	public function next()
	{
		next($this->_pages);
	}

	/**
	 * Implements [Iterator::prev], moves to the previous row.
	 *
	 *     prev($result);
	 *
	 * @return  $this
	 */
	public function prev()
	{
		--$this->_current_key;
	}

	/**
	 * Implements [Iterator::rewind], sets the current row to zero.
	 *
	 *     rewind($result);
	 *
	 * @return  $this
	 */
	public function rewind()
	{
		reset($this->_pages);
	}

	/**
	 * Implements [Iterator::valid], checks if the current row exists.
	 *
	 * [!!] This method is only used internally.
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		$key = key($this->_pages);
		return ($key !== NULL AND $key !== FALSE);
	}
}