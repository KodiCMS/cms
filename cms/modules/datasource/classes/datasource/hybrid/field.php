<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class DataSource_Hybrid_Field {
	
	const TYPE_PRIMITIVE = 'primitive';
	const TYPE_DATASOURCE = 'datasource';
	const TYPE_ARRAY = 'array';
	const TYPE_HYBRID = 'hybrid';
	const TYPE_DOCUMENT = 'document';
	
	const PREFFIX = 'f_';
	
	/**
	 *
	 * @var string
	 */
	public $ds_table = NULL;
	
	/**
	 *
	 * @var string
	 */
	public $table = 'dshfields';
	
	/**
	 *
	 * @var integer
	 */
	public $id = NULL;
	
	/**
	 *
	 * @var integer
	 */
	public $ds_id = NULL;
	
	/**
	 *
	 * @var integer
	 */
	public $from_ds = NULL;
	
	/**
	 *
	 * @var string
	 */
	public $family;
	
	/**
	 *
	 * @var string
	 */
	public $type;
	
	/**
	 *
	 * @var string
	 */
	public $name;

	/**
	 *
	 * @var string
	 */
	public $header;

	/**
	 *
	 * @var array
	 */
	protected $_props = array();
	
	/**
	 * 
	 * @return array
	 */
	public static function types()
	{
		return array(
			self::TYPE_PRIMITIVE => __('Primitive'),
			self::TYPE_DOCUMENT => __('Document'),
			self::TYPE_ARRAY => __('Array of documents'),
			self::TYPE_DATASOURCE => __('Datasource')
		);
	}

	/**
	 * 
	 * @param type $family
	 * @param array $data
	 * @return \DataSource_Hybrid_Field
	 * @throws Kohana_Exception
	 */
	public static function factory($family, array $data)
	{
		$class_name = 'DataSource_Hybrid_Field_' . $family;
		
		if(!class_exists( $class_name ))
		{
			throw new Kohana_Exception('Class for field - :type not found', array(
				':type' => $family));
		}
		
		return new $class_name($data);
	}
	
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty')
			),
			'header' => array(
				array('not_empty')
			),
			'family' => array(
				array('in_array', array(
					':value', array_keys(DataSource_Hybrid_Field::types())
				))
			)
		);
	}
	
	public function validate($data = NULL)
	{
		if($data === NULL)
		{
			$data = $this->as_array();
		}

		$array = Validation::factory($data);
		$rules = $this->rules();
		
		foreach ( $rules as $field => $r )
		{
			$array->rules($field, $r);
		}
		
		if(!$array->check())
		{
			throw new Validation_Exception($array);
		}
		
		return TRUE;
	}

	public function __construct($data) 
	{
		$this->set($data);
		
		$this->type = strtolower($this->type);
		$this->from_ds = (int) $this->from_ds;
	}
	
	public function set($data)
	{
		$valid = $this->validate($data);

		foreach ( $data as $key => $value )
		{
			$this->{$key} = $value;
		}
		
		return $this;
	}

	/**
	 * 
	 * @param type $key
	 * @param type $value
	 */
	public function __set($key, $value)
	{
		$this->_props[$key] = $value;
	}
	
	/**
	 * 
	 * @param type $key
	 * @return string|NULL
	 * @throws Kohana_Exception
	 */
	public function __get($key)
	{
		if(!isset($this->_props[$key]))
		{
			throw new Kohana_Exception('Field properties :prop not found', array(
				':prop' => $key));
		}

		return Arr::get($this->_props, $key);
	}
	
	public function __isset( $key )
	{
		return isset($this->_props[$key]);
	}
	
	public function __unset( $key )
	{
		unset($this->_props[$key]);
	}

	/**
	 * 
	 * @param integer $ds_id
	 * @return \DataSource_Hybrid_Field
	 */
	public function set_ds($ds_id) 
	{
		$this->ds_id = (int) $ds_id;
		$this->ds_table = 'dshybrid_' . $this->ds_id;
		
		return $this;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return \DataSource_Hybrid_Field
	 */
	public function set_id($id) 
	{
		$this->id = (int) $id;
		
		return $this;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function create() 
	{
		$this->validate();

		$query = DB::insert($this->table)
			->columns(array(
				'ds_id', 
				'name', 
				'family', 
				'type', 
				'header',
				'from_ds',
				'props'
			))
			->values(array(
				$this->ds_id, 
				$this->name, 
				$this->family,
				$this->type, 
				$this->header,
				$this->from_ds,
				serialize($this->_props)
			))
			->execute();

		$this->id = $query[0];

		return $this->id;
	}
	
	public function update() 
	{
		$this->validate();

		return DB::update($this->table)
			->set(array(
				'header' => $this->header,
				'name' => $this->name,
				'props' => serialize( $this->_props )
			))
			->where('id', '=', $this->id)
			->execute();
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function remove()
	{
		DB::delete($this->table)
			->where('id', '=', $this->id)
			->execute();
			
		$this->id = NULL;
		
		return TRUE;
	}
	
	public function as_array()
	{
		$data = get_object_vars($this);
		
		$data = Arr::merge($data, $this->_props);
		return $data;
	}

	/**
	 * 
	 * @param Datasource_Document $doc
	 * @return null|array
	 */
	public function get_sql($doc)
	{
		if($this->is_valid($doc->fields[$this->name]))
		{
			return array($this->name, $doc->fields[$this->name]);
		}
		
		return NULL;
	}
	
	public function get_type()
	{
		return NULL;
	}

	public function is_valid($value) 
	{
		return TRUE;
	}
	
	public function fetch_value($doc) 
	{
		FALSE ? $doc : NULL ;
	}
	
	public function convert_to_plain($doc) 
	{
		FALSE ? $doc : NULL ;
	}
	
	public function onCreateDocument($doc) 
	{
		if(!isset($doc->fields[$this->name]))
		{
			$doc->fields[$this->name] = '';
		}
	}
	
	public function onUpdateDocument($old, $new) 
	{
		FALSE ? $old OR $new : NULL ;
	}
	
	public function onRemoveDocument($doc) 
	{
		FALSE ? $doc : NULL ;
	}
}