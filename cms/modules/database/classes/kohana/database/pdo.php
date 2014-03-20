<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * PDO database connection.
 *
 * @package    Kohana/Database
 * @category   Drivers
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_PDO extends Database {

	// PDO uses no quoting for identifiers
	protected $_identifier = '`';

	public function __construct($name, array $config)
	{
		parent::__construct($name, $config);

		if (isset($this->_config['identifier']))
		{
			// Allow the identifier to be overloaded per-connection
			$this->_identifier = (string) $this->_config['identifier'];
		}
	}

	public function connect()
	{
		if ($this->_connection)
			return;

		// Extract the connection parameters, adding required variabels
		extract($this->_config['connection'] + array(
			'dsn'        => '',
			'username'   => NULL,
			'password'   => NULL,
			'persistent' => FALSE,
		));

		// Clear the connection parameters for security
		unset($this->_config['connection']);

		// Force PDO to use exceptions for all errors
		$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

		if ( ! empty($persistent))
		{
			// Make the connection persistent
			$options[PDO::ATTR_PERSISTENT] = TRUE;
		}

		try
		{
			// Create a new PDO connection
			$this->_connection = new PDO($dsn, $username, $password, $options);
		}
		catch (PDOException $e)
		{
			throw new Database_Exception(':error',
				array(':error' => $e->getMessage()),
				$e->getCode());
		}		
			
		if ( ! empty($this->_config['charset']))
		{
			// Set the character set
			$this->set_charset($this->_config['charset']);
		}
	}

	/**
	 * Create or redefine a SQL aggregate function.
	 *
	 * [!!] Works only with SQLite
	 *
	 * @link http://php.net/manual/function.pdo-sqlitecreateaggregate
	 *
	 * @param   string      $name       Name of the SQL function to be created or redefined
	 * @param   callback    $step       Called for each row of a result set
	 * @param   callback    $final      Called after all rows of a result set have been processed
	 * @param   integer     $arguments  Number of arguments that the SQL function takes
	 *
	 * @return  boolean
	 */
	public function create_aggregate($name, $step, $final, $arguments = -1)
	{
		$this->_connection or $this->connect();

		return $this->_connection->sqliteCreateAggregate(
			$name, $step, $final, $arguments
		);
	}

	/**
	 * Create or redefine a SQL function.
	 *
	 * [!!] Works only with SQLite
	 *
	 * @link http://php.net/manual/function.pdo-sqlitecreatefunction
	 *
	 * @param   string      $name       Name of the SQL function to be created or redefined
	 * @param   callback    $callback   Callback which implements the SQL function
	 * @param   integer     $arguments  Number of arguments that the SQL function takes
	 *
	 * @return  boolean
	 */
	public function create_function($name, $callback, $arguments = -1)
	{
		$this->_connection or $this->connect();

		return $this->_connection->sqliteCreateFunction(
			$name, $callback, $arguments
		);
	}

	public function disconnect()
	{
		// Destroy the PDO object
		$this->_connection = NULL;

		return parent::disconnect();
	}

	public function set_charset($charset)
	{
		// Make sure the database is connected
		$this->_connection OR $this->connect();

		// This SQL-92 syntax is not supported by all drivers
		$this->_connection->exec('SET NAMES '.$this->quote($charset));
	}

	public function query($type, $sql, $as_object = FALSE, array $params = NULL)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if (Kohana::$profiling)
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		try
		{
			$result = $this->_connection->query($sql);
		}
		catch (Exception $e)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			// Convert the exception in a database exception
			throw new Database_Exception(':error [ :query ]',
				array(
					':error' => $e->getMessage(),
					':query' => $sql
				),
				$e->getCode());
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT)
		{
			// Convert the result into an array, as PDOStatement::rowCount is not reliable
			if ($as_object === FALSE)
			{
				$result->setFetchMode(PDO::FETCH_ASSOC);
			}
			elseif (is_string($as_object))
			{
				$result->setFetchMode(PDO::FETCH_CLASS, $as_object, $params);
			}
			else
			{
				$result->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
			}

			$result = $result->fetchAll();

			// Return an iterator of results
			return new Database_Result_Cached($result, $sql, $as_object, $params);
		}
		elseif ($type === Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				$this->_connection->lastInsertId(),
				$result->rowCount(),
			);
		}
		else
		{
			// Return the number of rows affected
			return $result->rowCount();
		}
	}

	public function begin($mode = NULL)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return $this->_connection->beginTransaction();
	}

	public function commit()
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return $this->_connection->commit();
	}

	public function rollback()
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return $this->_connection->rollBack();
	}

	public function list_tables($like = NULL)
	{
		if (is_string($like))
		{
			// Search for table names
			$result = $this->query(Database::SELECT, 'SHOW TABLES LIKE '.$this->quote($like), FALSE);
		}
		else
		{
			// Find all table names
			$result = $this->query(Database::SELECT, 'SHOW TABLES', FALSE);
		}

		$tables = array();
		foreach ($result as $row)
		{
			$tables[] = reset($row);
		}

		return $tables;
	}

	public function list_columns($table, $like = NULL, $add_prefix = TRUE)
	{
		// Quote the table name
		$table = ($add_prefix === TRUE) ? $this->quote_table($table) : $table;
		
		if (is_string($like))
		{
			// Search for column names
			$result = $this->query(Database::SELECT, 'DESCRIBE '.$table.' LIKE '.$this->quote($like), FALSE);
		}
		else
		{
			// Find all column names
			$result = $this->query(Database::SELECT, 'DESCRIBE '.$table, FALSE);
		}
		
		$count = 0;
		$columns = array();
		foreach ($result as $row)
		{
			list($type, $length) = $this->_parse_type($row['Type']);

			$column = $this->datatype($type);

			$column['column_name']      = $row['Field'];
			$column['column_default']   = $row['Default'];
			$column['data_type']        = $type;
			$column['is_nullable']      = ($row['Null'] == 'YES');
			$column['ordinal_position'] = ++$count;

			switch ($column['type'])
			{
				case 'float':
					if (isset($length))
					{
						list($column['numeric_precision'], $column['numeric_scale']) = explode(',', $length);
					}
				break;
				case 'int':
					if (isset($length))
					{
						// MySQL attribute
						$column['display'] = $length;
					}
				break;
				case 'string':
					switch ($column['data_type'])
					{
						case 'binary':
						case 'varbinary':
							$column['character_maximum_length'] = $length;
						break;
						case 'char':
						case 'varchar':
							$column['character_maximum_length'] = $length;
						case 'text':
						case 'tinytext':
						case 'mediumtext':
						case 'longtext':
							$column['collation_name'] = NULL;
						break;
						case 'enum':
						case 'set':
							$column['collation_name'] = $row['Collation'];
							$column['options'] = explode('\',\'', substr($length, 1, -1));
						break;
					}
				break;
			}

			// MySQL attributes
			$column['extra']        = $row['Extra'];
			$column['key']          = $row['Key'];

			$columns[$row['Field']] = $column;
		}

		return $columns;
	}

	public function escape($value)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return $this->_connection->quote($value);
	}
	
	public function datatype($type)
	{
		static $types = array
		(
			'blob'                      => array('type' => 'string', 'binary' => TRUE, 'character_maximum_length' => '65535'),
			'bool'                      => array('type' => 'bool'),
			'bigint unsigned'           => array('type' => 'int', 'min' => '0', 'max' => '18446744073709551615'),
			'datetime'                  => array('type' => 'string'),
			'decimal unsigned'          => array('type' => 'float', 'exact' => TRUE, 'min' => '0'),
			'double'                    => array('type' => 'float'),
			'double precision unsigned' => array('type' => 'float', 'min' => '0'),
			'double unsigned'           => array('type' => 'float', 'min' => '0'),
			'enum'                      => array('type' => 'string'),
			'fixed'                     => array('type' => 'float', 'exact' => TRUE),
			'fixed unsigned'            => array('type' => 'float', 'exact' => TRUE, 'min' => '0'),
			'float unsigned'            => array('type' => 'float', 'min' => '0'),
			'geometry'                  => array('type' => 'string', 'binary' => TRUE),
			'int unsigned'              => array('type' => 'int', 'min' => '0', 'max' => '4294967295'),
			'integer unsigned'          => array('type' => 'int', 'min' => '0', 'max' => '4294967295'),
			'longblob'                  => array('type' => 'string', 'binary' => TRUE, 'character_maximum_length' => '4294967295'),
			'longtext'                  => array('type' => 'string', 'character_maximum_length' => '4294967295'),
			'mediumblob'                => array('type' => 'string', 'binary' => TRUE, 'character_maximum_length' => '16777215'),
			'mediumint'                 => array('type' => 'int', 'min' => '-8388608', 'max' => '8388607'),
			'mediumint unsigned'        => array('type' => 'int', 'min' => '0', 'max' => '16777215'),
			'mediumtext'                => array('type' => 'string', 'character_maximum_length' => '16777215'),
			'national varchar'          => array('type' => 'string'),
			'numeric unsigned'          => array('type' => 'float', 'exact' => TRUE, 'min' => '0'),
			'nvarchar'                  => array('type' => 'string'),
			'point'                     => array('type' => 'string', 'binary' => TRUE),
			'real unsigned'             => array('type' => 'float', 'min' => '0'),
			'set'                       => array('type' => 'string'),
			'smallint unsigned'         => array('type' => 'int', 'min' => '0', 'max' => '65535'),
			'text'                      => array('type' => 'string', 'character_maximum_length' => '65535'),
			'tinyblob'                  => array('type' => 'string', 'binary' => TRUE, 'character_maximum_length' => '255'),
			'tinyint'                   => array('type' => 'int', 'min' => '-128', 'max' => '127'),
			'tinyint unsigned'          => array('type' => 'int', 'min' => '0', 'max' => '255'),
			'tinytext'                  => array('type' => 'string', 'character_maximum_length' => '255'),
			'year'                      => array('type' => 'string'),
		);

		$type = str_replace(' zerofill', '', $type);

		if (isset($types[$type]))
			return $types[$type];

		return parent::datatype($type);
	}

} // End Database_PDO
