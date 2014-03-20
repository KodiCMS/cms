<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/API
 * @category	Model
 * @author		ButscHSter
 */
class Model_API extends Model_Database {
	
	/**
	 * Table columns
	 * @var array
	 */
	protected $_table_columns;
	
	/**
	 * Secured table columns
	 * @var array
	 */
	protected $_secured_columns = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_params = array();

	/**
	 * Table name
	 * @var string
	 */
	protected $_table_name;
	
	public function __construct( $db = NULL )
	{
		parent::__construct( $db );
		
		$this->_table_columns = $this->list_columns();
	}
	
	/**
	 * @return string
	 */
	public function table_name()
	{
		return $this->_table_name;
	}
	
	/**
	 * @return array
	 */
	public function table_columns()
	{
		return $this->_table_columns;
	}
	
	/**
	 * @return array
	 */
	public function secured_columns()
	{
		return $this->_secured_columns;
	}

	/**
	 * 
	 * @global type $table_name
	 * @param array $fields
	 * @param array $related_columns
	 * @param array $remove_fields
	 * @return array
	 * @throws HTTP_API_Exception
	 */
	public function filtered_fields($fields, $remove_fields = array())
	{
		if( ! is_array($fields) )
		{
			$fields = array($fields);
		}

		$secured_fields = array_intersect($this->_secured_columns, $fields);

		// Exclude fields
		$fields = array_diff($fields, $remove_fields);
		
		// TODO сделать проверку токена, выдаваемого под API
		if( ! empty($secured_fields) AND ! AuthUser::isLoggedIn('login') )
		{
			throw HTTP_API_Exception::factory(API::ERROR_PERMISSIONS, 'You don`t have permissions to access to this fields (:fields).', array(
				':fields' => implode(', ', $secured_fields)
			));
		}

		$fields = array_intersect(array_keys($this->_table_columns), $fields);
		
		foreach ($fields as $i => $field)
		{
			$fields[$i] = $this->table_name() . '.' . $field;
		}

		return $fields;
	}
	
	/**
	 * 
	 * @param array $params
	 * @return \Model_API
	 */
	public function set_params( array $params )
	{
		$this->_params = $params;
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) 
	{
		return $this->get($name);
	}
	
	/**
	 * 
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name) 
	{
		return isset($this->_params[$name]);
	}

	/**
	 * 
	 * @param string $name
	 * @param mixed $filter
	 * @return type
	 */
	public function get($name, $default = NULL)
	{
		return Arr::get($this->_params, $name, $default);
	}

		/**
	 * 
	 * @param mixed $param
	 * @param mixed $filter
	 * @return array
	 */
	public function prepare_param($param, $filter = NULL)
	{
		if(!is_array($param) AND strpos($param, ',') !== FALSE)
		{
			$param = explode(',', $param);
		}

		if(is_array($param) AND $filter !== NULL)
		{
			$param = array_filter($param, $filter);
		}

		return $param;
	}

	/**
	 * Proxy method to Database list_columns.
	 *
	 * @return array
	 */
	public function list_columns()
	{
		if(Kohana::$caching === TRUE)
		{
			$cache = Cache::instance();
			if ( ($result = $cache->get( 'table_columns_' . $this->_table_name )) !== NULL )
			{
				return $result;
			}

			$cache->set( 'table_columns_' . $this->_table_name, $this->_db->list_columns( $this->_table_name ) );
		}

		// Proxy to database
		return $this->_db->list_columns( $this->_table_name );
	}
}