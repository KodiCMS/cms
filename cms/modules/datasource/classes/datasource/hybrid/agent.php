<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class Datasource_Hybrid_Agent {

	const COND_EQ = 0;
	const COND_BTW = 1;
	const COND_GT = 2;
	const COND_LT = 3;
	const COND_GTEQ = 4;
	const COND_LTEQ = 5;
	const COND_CONTAINS = 6;
	const COND_LIKE = 7;

	const VALUE_CTX = 10;
	const VALUE_PLAIN = 20;
	
	/**
	 *
	 * @var integer
	 */
	public $ds_id;
	
	/**
	 *
	 * @var string
	 */
	public $ds_key;
	
	/**
	 *
	 * @var string
	 */
	public $ds_path;
	
	/**
	 *
	 * @var string
	 */
	public $ds_name;
	
	/**
	 *
	 * @var array
	 */
	public $ds_fields = NULL;
	
	/**
	 *
	 * @var array
	 */
	public $ds_field_names = NULL;
	
	/**
	 *
	 * @var array
	 */
	public $sys_fields = NULL;
	
	public function __construct($dsId, $dsKey, $dsPath, $dsName) 
	{
		$this->ds_id = $dsId;
		$this->ds_key = $dsKey;
		$this->ds_path = $dsPath;
		$this->ds_name = $dsName;
	}

	/**
	 * 
	 * @return array
	 */
	public function get_fields()
	{
		if($this->ds_fields !== NULL)
		{
			return $this->ds_fields;
		}
		
		$this->ds_fields = $this->ds_field_names = array();
		
		$query = DB::select('dsf.id', 'dsf.ds_id', 'dsf.name', 'dsf.family', 'dsf.type', 'dsf.header', 'dsf.from_ds')
			->from(array('hybriddatasources', 'hds'), array('dshfields', 'dsf') )
			->where('hds.ds_id', '=', $this->ds_id)
			->where( DB::expr( 'FIND_IN_SET(dsf.ds_id, hds.path)'), '>', 0 )
			->execute();
		
		foreach ($query as $row)
		{
			$name = str_replace(  DataSource_Hybrid_Field::PREFFIX, '', $row['name']);
			$id = $row['id'];

			$this->ds_fields[$id] = array(
				'ds_id' => $row['ds_id'],
				'type' => constant('DataSource_Hybrid_Field::TYPE_' . strtoupper($row['family'])), 
				'name' => $name, 
				'header' => $row['header']
			);
			
			if($row['family'] === DataSource_Hybrid_Field::TYPE_DOCUMENT)
			{
				$this->ds_fields[$id]['ds_type'] = $row['type'];
				$this->ds_fields[$id]['from_ds'] = $row['from_ds'];
			}
			
			$this->ds_field_names[$name] = $id;
		}
		
		return $this->ds_fields;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_system_fields()
	{
		if($this->sys_fields !== NULL)
		{
			$this->sys_fields = array(
				'id' => array(
					'ds_id' => $this->ds_id, 
					'type' => DataSource_Hybrid_Field::TYPE_PRIMITIVE, 
					'name' => 'ds.id', 
					'sys' => TRUE
				),
				'header' => array(
					'ds_id' => $this->ds_id, 
					'type' => DataSource_Hybrid_Field::TYPE_PRIMITIVE, 
					'name' => 'd.header', 
					'sys' => TRUE
				)
			);
		}

		return $this->sys_fields;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_field_names() 
	{
		if($this->ds_fields === NULL)
		{
			$this->get_fields();
		}

		return $this->ds_field_names;
	}
	
	/**
	 * 
	 * @param array $fields
	 * @param array $order
	 * @param array $filter
	 * @return Database_Query_Builder_Select
	 */
	public function get_query_props($fields, $order = array(), $filter = array())
	{
		$result = DB::select('d.id,', 'd.ds_id', 'd.header')
			->from(array('dshybrid_' . $this->ds_id,  'ds'))
			->join(array('dshybrid', 'd'))
				->on('d.id', '=', 'ds.id');
		
		$ds_fields = $this->get_fields();
		$sys_fields = $this->get_sys_fields();
		
		$t = array($this->ds_id => TRUE);
		$dss = $dds = array();

		for($i = 0, $l = count($fields); $i < $l; $i++) 
		{
			$fid = $fields[$i]['id'];
			$field = $ds_fields[$fid];
			
			if(!$field) continue;
			
			if(!isset($t[$field['ds_id']])) 
			{
				$result->join(array('dshybrid_'.$field['ds_id'], 'd' . $i))
					->on('d' . $i, '=', ds.id);
	
				$t[$field['ds_id']] = TRUE;
			}

			$result->select(array(DataSource_Hybrid_Field::PREFFIX . $field['name'], $fid));
			
			if($f['type'] == DataSource_Hybrid_Field::TYPE_DATASOURCE) 
			{
				$result->join(array('datasources', 'dss' . $fid), 'left')
					->on(DataSource_Hybrid_Field::PREFFIX . $field['name'], '=', 'dss' . $fid . '.ds_id')
					->select(array('dss'.$fid.'.docs', $fid . 'docs'));
	
				$dss[$fid] = TRUE;
			}
			// TODO протестировать
			elseif($field['type'] == DataSource_Hybrid_Field::TYPE_DOCUMENT AND empty($fields[$i]['fetcher'])) 
			{
				$result->join(array('ds' . $field['ds_type'], 'dss' . $fid), 'left')
					->on(DataSource_Hybrid_Field::PREFFIX . $field['name'], '=', 'dds' . $fid . '.id')
					->on('dds' . $fid . '.published', '=', DB::expr( 1 ))
					->select(array('dss'.$fid.'.header', $fid . 'header'));
				
				$dds[$fid] = TRUE;
			}

			unset($field);
		}
		
		$j = 0;
		
		if(sizeof($order))
		{
			for($l = sizeof($order); $j < $l; $j++) 
			{
				$field = NULL;
				
				if(Valid::numeric( $order[$j]['id'] ))
				{
					$fid = $order[$j]['id'];
					
					if(isset($ds_fields[$fid])) 
					{
						$field = $ds_fields[$fid];
						$dir = $order[$j]['id'] < 0 ? 'DESC' : 'ASC';
					}
					else
					{
						$dir = substr($order[$j]['id'], 0, 1) == '-' ? 'DESC' : '';
						$fid = $dir ? substr($order[$j]['id'], 1) : $order[$j]['id'];
						if(isset($sys_fields[$fid]))
						{
							$f = $sys_fields[$fid];
						}
					}
				}
				
				if(!$field)	continue;
				
				if(!isset($t[$field['ds_id']])) 
				{
					$result->join(array('dshybrid_'. $field['ds_id'], 'd' . ($i + $j)))
						->on('d' . ($i + $j) . '.id', '=', 'ds.id');
	
					$t[$field['ds_id']] = TRUE;
				}
				
				if($field['type'] == DataSource_Hybrid_Field::TYPE_DATASOURCE) 
				{
					if(!isset($dss[$fId]))
					{
						$result
							->join(array('datasources', 'dss' . $fid), 'left')
							->on(DataSource_Hybrid_Field::PREFFIX . $field['name'], '=', 'dss' . $fid . '.ds_id');
					}
					
					$result->order_by('dss' . $fid . '.docs', $dir);
				} 
				elseif($field['type'] == DataSource_Hybrid_Field::TYPE_DOCUMENT) 
				{
					if(!isset($dds[$fid])) 
					{
						$result
							->join(array('ds' . $field['ds_type'], 'dds' . $fid), 'left')
							->on(DataSource_Hybrid_Field::PREFFIX . $field['name'], '=', 'dds' . $fid . '.id')
							->on('dds' . $fid . '.published', '=', DB::expr( 1 ))
							->order_by('dds' . $fid . '.header', $dir);
					} 
					else
					{
						$result->order_by($fid . '.header', $dir);
					}
				}
				else
				{
					$field_name = isset($field['sys']) ? '': DataSource_Hybrid_Field::PREFFIX;
					$result->order_by($field_name . $field['name'], $dir);
				}
				
				unset($field);
			}
		}
		
		$i += $j;
		
		// TODO  добавить фильтры
		
		return $result;
	}
	
	protected static $_instance = array();

	/**
	 * 
	 * @param string|integer $ds_id
	 * @param string $type
	 * @param boolean $only_sub
	 * @return Datasource_Hybrid_Agent
	 */
	public static function instance($ds_id, $type = NULL, $only_sub = FALSE)
	{
		if(isset(self::$_instance[$ds_id]))
		{
			return self::$_instance[$ds_id];
		}
		
		$ds_key = NULL;
		if(!Valid::numeric( $ds_id ))
		{
			$ds_key = $ds_id;
		}
		
		$query = DB::select('hds.ds_id', 'hds.ds_key', 'hds.path', 'ds.name')
			->from(array('hybriddatasources', 'hds'), array('datasources', 'ds'))
			->where(DB::expr( 'INSTR(hds.ds_key, :ds_key_field)'), '=', 1)
			->where('hds.ds_id', '=', 'ds.ds_id')
			->order_by( 'hds.ds_key', 'asc')
			->param( ':ds_key_field', DB::expr($ds_key != NULL ? $ds_key : 'hds0.ds_key'));
		
		if($ds_key === NULL)
		{
			$query
				->from(array('hybriddatasources', 'hds0'))
				->where('hds0.ds_id', '=', $ds_id);
		}
		
		$result = $query->execute();
		
		if($result->count() > 0)
		{
			$current = $result->current();
			$ds_id = $current['ds_id'];
			$ds_key = $current['ds_key'];
			$ds_name = $current['ds_name'];
			
			$path = array_flip(explode(',', substr($current['path'], 2))); 
			$pos = 0;
			
			foreach($path as $id => $v) 
			{
				$pos = strpos($ds_key, '.', $pos + 1);
				$path[$id] = $pos > 0 ? substr($ds_key, 0, $pos) : $ds_key;
			}
			
			foreach($result as $row)
			{
				$path[$row['ds_id']] = $row['ds_key'];
			}
			
			self::$_instance[$ds_id] = new Datasource_Hybrid_Agent($ds_id, $ds_key, $path, $ds_name);
			self::$_instance[$ds_key] = self::$_instance[$ds_id];
		}
		else
		{
			self::$_instance[$ds_id] = NULL;
		}
		
		if(
			$type !== NULL 
		AND 
			self::$_instance[$ds_id] instanceof Datasource_Hybrid_Agent 
		AND 
			(($type != $ds_id)
				? (
						!isset(self::$_instance[$ds_id]->ds_path[$type]) 
					OR 
						strlen(self::$_instance[$ds_id]->ds_path[$type]) > strlen(self::$_instance[$ds_id]->ds_key)
				  )
				: $only_sub
			)
		) 
		{
			return NULL;
		} 
		else
		{
			return self::$_instance[$ds_id];
		}
	}
}