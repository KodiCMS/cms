<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class KodiCMS_Database_Query extends Kohana_Database_Query {

	/**
	 *
	 * @var string
	 */
	protected $_cache_key = NULL;
	
	/**
	 *
	 * @var array
	 */
	protected $_cache_tags = array();

	/**
	 * 
	 * @param string $key
	 * @return \Database_Query
	 */
	public function cache_key( $key )
	{
		$this->_cache_key = $key;
		return $this;
	}
	
	public function cache_tags( array $tags )
	{
		$this->_cache_tags = $tags;
		return $this;
	}

	public function execute( $db = NULL, $as_object = NULL, $object_params = NULL )
	{
		if ( !is_object( $db ) )
		{
			// Get the database instance
			$db = Database::instance( $db );
		}

		if ( $as_object === NULL )
		{
			$as_object = $this->_as_object;
		}

		if ( $object_params === NULL )
		{
			$object_params = $this->_object_params;
		}

		// Compile the SQL query
		$sql = $this->compile( $db );

		if ( $this->_lifetime !== NULL AND $this->_type === Database::SELECT AND Kohana::$caching === TRUE )
		{
			// Set the cache key based on the database instance name and SQL
			if ( $this->_cache_key !== NULL )
			{
				$cache_key = 'Database::cache(' . $this->_cache_key . ')';
			}
			else
			{
				$cache_key = 'Database::query("' . $db . '", "' . $sql . '")';
			}

			// Read the cache first to delete a possible hit with lifetime <= 0
			if ( ($result = Cache::instance()->get($cache_key)) !== NULL
					AND !$this->_force_execute )
			{
				// Return a cached result
				return new Database_Result_Cached( $result, $sql, $as_object, $object_params );
			}
		}

		// Execute the query
		$result = $db->query( $this->_type, $sql, $as_object, $object_params );

		if ( isset( $cache_key ) AND $this->_lifetime > 0 AND Kohana::$caching === TRUE )
		{
			// Cache the result array
			Cache::instance()->set_with_tags($cache_key, $result->as_array(), $this->_lifetime, $this->_cache_tags);
		}

		return $result;
	}

}

// End Database_Query
