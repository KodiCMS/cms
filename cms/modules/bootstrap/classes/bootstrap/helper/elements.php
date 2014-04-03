<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * http://twitter.github.com/bootstrap/base-css.html#forms
 * @package		Twitter Bootstrap
 * @category	Helper
 * @author		ButscHSter
 */
class Bootstrap_Helper_Elements extends Bootstrap_Helper_Element implements Countable, Iterator {
	
	/**
	 *
	 * @var array 
	 */
	protected $_elements = array();
	
	/**
	 *
	 * @var integer 
	 */
	protected $_position = 0;
	
	/**
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 * @throws Bootstrap_Exception
	 */
	public function __call($method, $arguments) 
	{
		if ( method_exists($this, $method) ) 
		{
			return call_user_func_array(array($this, $method), $arguments);
        }
		else if( $method == 'add' )
		{
			return call_user_func_array(array($this, '_add'), $arguments);
		}
		
		throw new Bootstrap_Exception('Method :method not exists', array(
			':method' => $method ));
	}

		/**
	 * 
	 * @param Bootstrap_Helper_Element $element
	 * @param integer $priority
	 */
	protected function _add( $element, $priority = 0 )
	{
		if( is_string( $element ) )
		{
			$element = Bootstrap::HTML( $element );
		}

		if( ! ($element instanceof Bootstrap_Helper_Element ))
			throw new Bootstrap_Exception(
					'Element must be an instance of Bootstrap_Helper_Element');

		$priority = (int) $priority;

		while ( isset( $this->_elements[$priority] ) )
		{
			$priority++;
		}
		
		$element->set_parent( $this );
		
		$this->_elements[$priority] = & $element;
		
		return $this;
	}
	
	public function br()
	{
		return $this->add( Bootstrap::HTML('<br />'));
	}
	
	public function hr()
	{
		return $this->add( Bootstrap::HTML('<hr />'));
	}
	
	/**
	 * 
	 * @return array
	 */
	public function elements()
	{
		return $this->_elements;
	}
	
	protected function _build_content() 
	{
		$this->_content = View::factory( $this->_template_folder . '/elements')
			->bind('elements', $this->_elements);
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function count()
	{
		return count($this->_elements);
	}
	
	public function rewind() 
	{
		$this->_position = 0;
    }
	
	public function current() 
	{
		return $this->_elements[$this->_position];
    }
	
	public function key() 
	{
		return $this->_position;
    }
	
	public function next() 
	{
		++$this->_position;
    }
	
	public function valid() 
	{
		return isset($this->_elements[$this->_position]);
    }
}