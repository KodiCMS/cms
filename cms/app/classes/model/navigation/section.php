<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package    Kodi/Navigation
 */
class Model_Navigation_Section extends Model_Navigation_Abstract implements Countable, Iterator, SeekableIterator, ArrayAccess {
	
	/**
	 *
	 * @var array
	 */
	protected $_pages = array();
	
	/**
	 *
	 * @var integer
	 */
	protected $_total_pages = 0;
	
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
		return Arr::get($this->params, 'name');
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
	 * @param Model_Navigation_Page $page
	 * @param integer $priority
	 * @return \Model_Navigation_Section
	 */
	public function add_page(  Model_Navigation_Page $page, $priority = 0 )
	{
		$priority = (int) $priority;
		
		if ( isset( $this->_pages[$priority] ) )
		{
			while ( isset( $this->_pages[$priority] ) )
			{
				$priority++;
			}
		}
		
		$page->set_section($this);
		
		$this->_pages[$priority] = $page;
		
		$this->sort_pages();
		
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Model_Navigation_Section
	 */
	public function sort_pages()
	{
		ksort($this->_pages);
		
		return $this;
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
		return $this->_total_pages;
	}

	/**
	 * Implements [ArrayAccess::offsetExists], determines if row exists.
	 *
	 *     if (isset($result[10]))
	 *     {
	 *         // Row 10 exists
	 *     }
	 *
	 * @return  boolean
	 */
	public function offsetExists($offset)
	{
		return ($offset >= 0 AND $offset < $this->_total_pages);
	}

	/**
	 * Implements [ArrayAccess::offsetGet], gets a given row.
	 *
	 *     $row = $result[10];
	 *
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		if ( ! $this->seek($offset))
			return NULL;

		return $this->current();
	}

	/**
	 * Implements [ArrayAccess::offsetSet], throws an error.
	 *
	 * [!!] You cannot modify a database result.
	 *
	 * @return  void
	 * @throws  Kohana_Exception
	 */
	final public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('Breadcrumbs are read-only');
	}

	/**
	 * Implements [ArrayAccess::offsetUnset], throws an error.
	 *
	 * [!!] You cannot modify a database result.
	 *
	 * @return  void
	 * @throws  Kohana_Exception
	 */
	final public function offsetUnset($offset)
	{
		throw new Kohana_Exception('Breadcrumbs are read-only');
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
		return $this->_current_key;
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
		return $this->_items[$this->_current_key];
	}
	
	/**
	 * Implements [SeekableIterator::seek], changes the key to a position
	 * @param int $position The position to seek to.
	 * @return bool
	 */
	public function seek($position)
	{
		if($this->offsetExists($position))
		{
			$this->_current_key = $position;
			return true;
		}
		else
		{
			return false;
		}
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
		++$this->_current_key;
		return $this;
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
		return $this;
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
		$this->_current_key = 0;
		return $this;
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
		return $this->offsetExists($this->_current_key);
	}
}