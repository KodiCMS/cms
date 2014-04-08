<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Database
 * @author		ButscHSter
 */
class KodiCMS_Record
{
	/**
	 * Stores column information for ORM models
	 * @var array
	 */
	protected static $_column_cache = array();
	
	/**
	 *
	 * @var array
	 */
	protected $_table_fields = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $_object_data = array();
	
	/**
	 *
	 * @var string 
	 */
	protected $_primary_key = 'id';


	/**
	 * 
	 * @param array $data
	 * @param array $exclude
	 */
    public function __construct($data = FALSE, $exclude = array())
    {
		$this->reloadColumns();

        if (is_array($data))
		{
            $this->setFromData($data, $exclude);
		}
    }
	
	public function reloadColumns()
	{
		if (empty($this->_table_fields))
		{
			if (isset(Record::$_column_cache[static::tableName()]))
			{
				// Use cached column information
				$this->_table_fields = Record::$_column_cache[static::tableName()];
			}
			else
			{
				// Grab column information from database
				$this->_table_fields = $this->getColumns();

				// Load column cache
				Record::$_column_cache[static::tableName()] = $this->_table_fields;
			}
		}
	}

	/**
	 * 
	 * @param array $data
	 * @param array $exclude
	 */
	public function setFromData($data, $exclude = array())
    {
        foreach($data as $key => $value)
		{
			if(!in_array($key, $exclude))
			{
				$this->$key = $value;
			}
        }
		
		return $this;
    }
	
	public function __get($field)
	{
		if(isset($this->_object_data[$field]))
		{
			return $this->_object_data[$field];
		}
		
		return Arr::get( $this->defaults(), $field );
	}

	public function __set( $field, $value )
	{
		// TODO Fix 
		// Filter the data
		//$value = $this->run_filter($field, $value);

		$this->_object_data[$field] = $value;
	}
	
	public function __isset( $field )
	{
		return isset($this->_object_data[$field]);
	}
	
	/**
	 * Unsets object data.
	 *
	 * @param  string $column Column name
	 * @return void
	 */
	public function __unset($field)
	{
		unset($this->_object_data[$field]);
	}
	
	public function as_array()
	{
		return $this->_object_data;
	}

	/**
     * Generates an insert or update string from the supplied data and executes it
     *
     * @return boolean
     */
    public function save()
    {
        if ( ! $this->beforeSave()) return FALSE;

        if( ! isset($this->{$this->_primary_key} ) )
		{
            if ( ! $this->beforeInsert()) return FALSE;

			$data = $this->prepare_data();

			$return = static::insert(NULL, $data);

            $this->{$this->_primary_key} = $return[0]; 
             
            if ( ! $this->afterInsert()) return FALSE;
        }
		else
		{
            if ( ! $this->beforeUpdate()) return FALSE;
            
			$data = $this->prepare_data(array($this->_primary_key));

			static::update(NULL, $data, array(
				'where' => array(array($this->_primary_key, '=', $this->{$this->_primary_key}))));
			
			$return = TRUE;
            
            if( ! $this->afterUpdate() ) return FALSE;
        }
        
        // Run it !!...
        return $return;
    }
	
	/**
	 * 
	 * @param array $exclude
	 * @return array
	 */
	public function prepare_data($exclude = array())
	{
		$data = array();
		$columns = $this->_table_fields;
		$defaults = $this->defaults();
            
		// Escape and format for SQL insert query
		foreach ($columns as $column => $column_data)
		{
			if (!in_array( $column, $exclude ))
			{
				if(isset($this->$column) )
				{
					$data[$column] = $this->$column;
				}
				else if(Arr::get( $defaults, $column ))
				{
					$data[$column] = Arr::get( $defaults, $column );
				}
			}
		}
		
		return $data;
	}

	/**
     * Generates a delete string and executes it
     *
     * @param string $table the table name
     * @param string $where the query condition
     * @return boolean
     */
    public function delete()
    {
        if ( ! $this->beforeDelete()) return FALSE;
		
		$return = DB::delete(static::tableName())
			->where($this->_primary_key, '=', $this->{$this->_primary_key} )
			->execute();

        if ( ! $this->afterDelete()) 
		{
            $this->save();
            return FALSE;
        }

        return $return;
    }
	
	/**
	 * Filters a value for a specific column
	 *
	 * @param  string $field  The column name
	 * @param  string $value  The value to filter
	 * @return string
	 */
	protected function run_filter($field, $value)
	{
		if(Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('Record filter field', $field);
		}
		
		$filters = $this->filters();

		// Get the filters for this column
		$wildcards = empty($filters[TRUE]) ? array() : $filters[TRUE];

		// Merge in the wildcards
		$filters = empty($filters[$field]) ? $wildcards : array_merge($wildcards, $filters[$field]);

		// Bind the field name and model so they can be used in the filter method
		$_bound = array
		(
			':field' => $field,
			':model' => $this,
		);

		foreach ($filters as $array)
		{
			// Value needs to be bound inside the loop so we are always using the
			// version that was modified by the filters that already ran
			$_bound[':value'] = $value;

			// Filters are defined as array($filter, $params)
			$filter = $array[0];
			
			$params = Arr::get($array, 1, array(':value'));

			foreach ($params as $key => $param)
			{
				if (is_string($param) AND array_key_exists($param, $_bound))
				{
					// Replace with bound value
					$params[$key] = $_bound[$param];
				}
			}

			if (is_array($filter) OR ! is_string($filter))
			{
				// This is either a callback as an array or a lambda
				$value = call_user_func_array($filter, $params);
			}
			elseif (strpos($filter, '::') === FALSE)
			{
				// Use a function call
				$function = new ReflectionFunction($filter);

				// Call $function($this[$field], $param, ...) with Reflection
				$value = $function->invokeArgs($params);
			}
			else
			{
				// Split the class and method of the rule
				list($class, $method) = explode('::', $filter, 2);

				// Use a static method call
				$method = new ReflectionMethod($class, $method);

				// Call $Class::$method($this[$field], $param, ...) with Reflection
				$value = $method->invokeArgs(NULL, $params);
			}
		}
		
		if(isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		return $value;
	}

	/**
	 * Filter definitions for validation
	 *
	 * @return array
	 */
	public function filters()
	{
		return array();
	}
	
	/**
	 * 
	 * @return array
	 */
	public function defaults()
	{
		return array();
	}
    
    /**
     * Return an array of all columns in the table
     * It is a good idea to rewrite this method in all your model classes;
     * used in save() for creating the insert and/or update sql query
     */
    public function getColumns()
    {
		if(Kohana::$caching === TRUE)
		{
			$cache = Cache::instance();
			if ( ($result = $cache->get( 'table_columns_' . static::tableName() )) !== NULL )
			{
				return $result;
			}

			$cache->set( 'table_columns_' . static::tableName(), Database::instance()->list_columns( static::tableName() ) );
		}

		// Proxy to database
		return Database::instance()->list_columns( static::tableName() );
    }

	/**
	 * 
	 * @param string $class_name
	 * @return string
	 */
	final public static function tableName($class_name = NULL)
    {
		if($class_name === NULL)
		{
			$class_name = get_called_class();
		}

        try
        {
            if (class_exists($class_name) AND defined($class_name.'::TABLE_NAME'))
			{
                return constant($class_name.'::TABLE_NAME');
			}
			
			return Inflector::underscore($class_name);
        }
        catch (Exception $e)
        {
			return Inflector::underscore($class_name);
        }
    }
	
	/**
	 * 
	 * @return string
	 */
	public static function calledClass()
	{
		return get_called_class();
	}

	/**
	 * 
	 * @param string $class_name
	 * @param array $data
	 * @return integer
	 */
    public static function insert($class_name, $data)
    {
        return DB::insert(static::tableName($class_name))
			->columns( array_keys( $data ) )
			->values( array_values( $data ) )
			->execute();
    }
    
    public static function update($class_name, $data, $clause = array(), $values = array())
    {
		$sql = DB::update(static::tableName($class_name))
			->set($data);
		
		$sql = static::_conditions($sql, $clause);

        return $sql
			->parameters($values)
			->execute();
    }
    
    public static function deleteWhere($class_name, array $clause, $values = array())
    {
		$sql = DB::delete()
			->table(static::tableName($class_name));

		$sql = static::_conditions($sql, $clause);

		return $sql
			->parameters($values)
			->execute();
    }
    
	public static function findByIdFrom($class_name, $id)
	{
		return static::findOneFrom($class_name, array(
			'where' => array(array('id', '=', $id))
		));
	}

	public static function findOneFrom($class_name, $clause = array(), $values = array())
	{
		$sql = DB::select('*')
			->from(static::tableName($class_name));
		
		$sql = static::_conditions($sql, $clause);

		return $sql
			->parameters( $values )
			->as_object($class_name)
			->execute()
			->current();
	}

	public static function findAllFrom($class_name, $clause = array(), $values = array())
	{
		$sql = DB::select('*')
			->from(static::tableName($class_name));

		$sql = static::_conditions($sql, $clause);

		return $sql
			->parameters( $values )
			->as_object( $class_name )
			->execute()
			->as_array();
	}

	public static function countFrom($class_name, $clause = array(), $values = array())
	{
		$sql = DB::select(array(DB::expr( 'COUNT(*)' ), 'nb_rows'))
			->from(static::tableName($class_name));
		
		$sql = static::_conditions($sql, $clause);

		return (int) $sql
			->parameters( $values )
			->execute()
			->get('nb_rows', 0);
	}
	
	/**
	 * 
	 * @param Database_Query $db
	 * @param array $clause
	 * @return type
	 */
	public static function _conditions($db, array $clause)
	{
		foreach ($clause as $type => $params)
		{
			switch ($type)
			{
				case 'select': 
					foreach ($params as $param)
					{
						$db->select($param);
					}
					break;
				case 'where': 
					foreach ($params as $param)
					{
						$db->where($param[0], $param[1], $param[2]);
					}
					break;
				case 'or_where': 
					foreach ($params as $param)
					{
						$db->or_where($param[0], $param[1], $param[2]);
					}
					break;
				case 'order_by': 
					foreach ($params as $param)
					{
						$db->order_by($param[0], $param[1]);
					}
					break;
				case 'limit':
					$db->limit((int) $params);
					break;
				case 'offset': 
					$db->offset((int) $params);
					break;
			}
		}
		
		return $db;
	}

	public function beforeSave() { return TRUE; }
    public function beforeInsert() { return TRUE; }
    public function beforeUpdate() { return TRUE; }
    public function beforeDelete() { return TRUE; }
    public function afterSave() { return TRUE; }
    public function afterInsert() { return TRUE; }
    public function afterUpdate() { return TRUE; }
    public function afterDelete() { return TRUE; }
}