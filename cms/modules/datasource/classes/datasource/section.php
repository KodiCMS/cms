<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class Datasource_Section {
	
	/**
	 *
	 * @var integer
	 */
	public $ds_id = NULL;
	
	/**
	 *
	 * @var string
	 */
	public $ds_type;
	
	/**
	 *
	 * @var string
	 */
	public $name;
	
	/**
	 *
	 * @var string
	 */
	public $description;
	
	/**
	 *
	 * @var integer
	 */
	public $docs = 0;
	
	/**
	 *
	 * @var integer
	 */
	public $size = 0;
	
	/**
	 *
	 * @var string
	 */
	public $ds_table;
	
	/**
	 *
	 * @var boolean
	 */
	public $is_indexable = FALSE;
	
	/**
	 *
	 * @var boolean
	 */
	public $is_internal = FALSE;
	
	/**
	 *
	 * @var integer
	 */
	public $lock;

	/**
	 * 
	 * @param string $name
	 * @param string $description
	 * @param integer $internal
	 *
	 * @return integer DataSource ID
	 */
	public function create($name, $description, $internal = FALSE) 
	{
		$this->name = $name;
		$this->description = $description;

		$doc = $this->get_empty_document();

		$data = array(
			'ds_type' => $this->ds_type,
			'indexed' => $this->is_indexable,
			'description' => $this->description,
			'name' => $this->name,
			'created_on' => date('Y-m-d H:i:s'),
			'code' => serialize($this),
			'internal' => $internal
		);
		
		$query = DB::insert('datasources')
			->columns(array_keys($data))
			->values(array_values($data))
			->execute();

		$this->ds_id = $query[0];
		
		return $this->ds_id;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return null|Datasource_Section
	 */
	public static function load($id) 
	{
		$result = NULL;
		
		if($id === NULL)
		{
			return FALSE;
		}
		
		$query = DB::select('docs', 'indexed', 'locks', 'code', 'internal')
			->from('datasources')
			->where('ds_id', '=', (int) $id)
			->execute()
			->current();
	
		if(!$query)
		{
			return FALSE;
		}

		$result = unserialize($query['code']);

		$result->ds_id = $id;
		$result->lock = (int) $query['locks'];
		$result->is_internal = $query['internal'] != 0;
		$result->is_indexable = $query['indexed'] != 0;
		$result->docs = (int) $query['docs'];

		return $result;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function save() 
	{
		if($this->ds_id === NULL)
		{
			return FALSE;
		}
		
		$data = array(
			'indexed' => $this->is_indexable,
			'name' => $this->name,
			'description' => $this->description,
			'updated_on' => date('Y-m-d H:i:s'),
			'code' => serialize( $this )
		);
		
		DB::update('datasources')
			->set($data)
			->where( 'ds_id', '=', $this->ds_id )
			->execute();

		$this->update_size();
		
		return TRUE;
	}
	
	/**
	 * 
	 * @return \Datasource_Section
	 */
	public function remove() 
	{
		$query = DB::select('id')
			->from(array('datasources', 'ds'), array($this->ds_table, 'd'))
			->where('ds.ds_id', '=', $this->ds_id)
			->execute();
		
		$ids = array();
		foreach ($query as $row) 
		{
			$ids[] = $row['id'];
		}
		
		$this->delete($ids);
		
		DB::delete('datasources')
			->where('ds_id', '=', $this->ds_id)
			->execute();

		$this->postremove();
		$this->ds_id = NULL;
		
		return $this;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @param boolean $alias
	 * @return array
	 */
	public function get_record($id, $alias = FALSE)
	{
		$query = DB::select()
			->from(array($this->ds_table, 'd'))
			->join(array($this->ds_table, 'd0'))
				->on('d.real_id', '=', 'd0.id')
			->where('d.id', '=', (int) $id)
			->limit(1);
		
		if($alias)
		{
			$query->where('d.ds_id', '=', $this->ds_id);
		}

		return $query
			->execute()
			->current();
	}
	
	/**
	 * 
	 * @param integer $doc_id
	 * @return Datasource_Section
	 */
	public function get_document($doc_id) 
	{
		$record = $this->get_record($doc_id);
		$result = $this->wrap_document($record);
		
		return $result;
	}
	
	/**
	 * 
	 * @param array $record
	 * @return Datasource_Section
	 */
	public function wrap_document($record) 
	{
		$result = $this->get_empty_document();
		$result->read_values($record);
		
		return $result;
	}
	
	/**
	 * 
	 * @return \DataSource_Document
	 */
	public function get_empty_document() 
	{
		return new DataSource_Document();
	}

	
	/**
	 * 
	 * @return \Datasource_Section
	 */
	public function increase_lock() 
	{
		DB::update('datasources')
			->set(array(
				'locks' => DB::expr('locks + 1')
			))
			->where('ds_id', '=', $this->ds_id)
			->execute();

		$this->lock++;
		
		return $this;
	}

	/**
	 * 
	 * @return \Datasource_Section
	 */
	public function decrease_lock()	
	{
		DB::update('datasources')
			->set(array(
				'locks' => DB::expr('locks - 1')
			))
			->where('ds_id', '=', $this->ds_id)
			->execute();

		$this->lock--;
		
		return $this;
	}
	
	public function postremove() 
	{
		
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Section
	 */
	public function publish($ids) 
	{
		return $this->_publish($ids, TRUE);
	}

	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Section
	 */
	public function unpublish($ids) 
	{
		return $this->_publish($ids, FALSE);
	}
	
	/**
	 * 
	 * @param array $ids
	 * @param boolean $value
	 * @return \Datasource_Section
	 */
	protected function _publish($ids, $value) 
	{
		DB::update($this->ds_table)
			->set(array(
				'published' => $value,
				'updated_on' => date('Y-m-d H:i:s'),
			))
			->where('id', 'in', $ids)
			->where('ds_id', '=', $this->ds_id)
			->execute();

		if($value == 1)
		{
			$this->add_to_index($ids);
		}
		else
		{
			$this->remove_from_index($ids);
		}

		return $this;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function is_full() 
	{
		if(!$this->size) return FALSE;

		return $this->size <= $this->docs;
	}
	
	/**
	 * 
	 * @param string $html
	 * @return string
	 */
	public function clean_html($html)
	{
		return $html;
	}
	
	/**
	 * 
	 * @param string $html
	 * @param integer $size
	 * @return string
	 */
	public function create_intro($html, $size = 200) 
	{
		$text = strip_tags($html);
		preg_match('/^.{'.($size - 1).'}[^\s\.,:]*/ums', $text, $intro);
		
		return isset($intro[0]) ? $intro[0] : $txt;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_real_ids() 
	{
		return DB::select('real_id')
			->from($this->ds_table)
			->where('ds_id', '=', $this->ds_id)
			->execute()
			->as_array(NULL, 'real_id');
	}

	/**
	 * 
	 * @param integer $size
	 * @return \Datasource_Section
	 */
	public function set_size($size) 
	{
		$this->size = $size == 0 || $this->docs <= $size ? $size : $this->size;
		
		return $this;
	}
	
	/**
	 * 
	 * @return \Datasource_Section
	 */
	public function update_size() 
	{
		if($this->ds_table) 
		{
			DB::update('datasources')
				->set(array(
					'docs' => DB::select('COUNT("*")')
						->from($this->ds_table)
						->where('ds_id', '=', $this->ds_id)
				))
				->where('ds_id', '=', $this->ds_id)
				->execute();
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $allowedTags
	 * @return \Datasource_Section
	 */
	function set_allowed_tags($allowedTags) 
	{
		if(isset($this->allowed_tags) AND preg_match('~^[\s\<A-Za-z0-9_\-:,\*\>!]+$~u', $allowedTags))
		{
			$this->allowed_tags = $allowedTags;
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param \Datasource_Section $from
	 * @return \Datasource_Section
	 * @throws Kohana_Exception
	 */
	public function copy_props($from) 
	{
		if($this->ds_type != $from->ds_type)
		{
			throw new Kohana_Exception('Types must be equals');
		}

		$this->set_size($from->size);
		$this->set_indexable($from->is_indexable);

		$props = $this->get_copiable_props();
		
		for($i = 0, $l = sizeof($props); $i < $l; $i++)
		{
			$this->$props[$i] = $from->$props[$i];
		}
		
		return $this;
	}
	
	public function get_copiable_props() 
	{

	}
	
	/**
	 * 
	 * @param boolean $newState
	 * @return \Datasource_Section
	 */
	public function set_indexable($newState) 
	{
		if(!$this->ds_id)
		{
			$this->is_indexable = $newState;
			
			return $this;
		}

		if($newState == $this->is_indexable)
		{
			return $this;
		}

		if($newState) 
		{
			$this->is_indexable = $newState;
			$this->add_to_index();
		} 
		else 
		{
			$this->remove_from_index();
			$this->is_indexable = $newState;
		}
		
		return $this;
	}
	
	public function add_to_index($ids = NULL, $header = NULL, $content = NULL, $intro = NULL) 
	{
		if(!$this->is_indexable)
		{
			return $this;
		}

		// TODO Add to index
	}
	
	public function update_index($id, $header = NULL, $content = NULL, $intro = NULL) 
	{
		if(!$this->is_indexable)
		{
			return $this;
		}

		// TODO Update index
	}
	
	public function remove_from_index($ids = NULL) 
	{
		if(!$this->is_indexable)
		{
			return $this;
		}
		
		// TODO Remove index
	}
	
	public function get_indexable_docs($id = NULL) 
	{
		$result = array();
		$cond = ($id != null ? "id IN ($id) AND " : '')."ds_id = {$this->ds_id}";
		
		$query = DB::select('id', 'header', 'content', 'intro')
			->from($this->ds_table)
			->where('published', '=', 1)
			->where('ds_id', '=', $this->ds_id);
		
		if($id !== NULL)
		{
			if(is_array($id))
			{
				$query->where('id', 'in', $id);
			}
			else
			{
				$query->where('id', '=', $id);
			}
		}
		
		foreach ($query as $row)
		{
			$result[] = $row;
		}

		return $result;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return array
	 */
	public function filter_docs($ids) 
	{
		return DB::select('id')
			->from($this->ds_table)
			->where('id', 'in', $ids)
			->where('ds_id', '=', $this->ds_id)
			->execute()
			->as_array(NULL, 'id');
	}
	
	/**
	 * 
	 * @param integer $id
	 * @param boolean $include_real
	 * @return array
	 */
	public function get_aliases($id, $include_real = FALSE) 
	{
		$query = DB::select('id')
			->from($this->ds_table)
			->where('real_id', '=', (int) $id);
		
		if($include_real)
		{
			$query->where('id', '!=', $id);
		}
		
		return $query
			->execute()
			->as_array(NULL, 'id');
	}
	
	public function get_sdoc()
	{
		return $this->wrap_document();
	}
	
	public function __sleep()
	{
		$vars = array_keys(get_object_vars($this));
		unset($vars['docs'], $vars['is_indexable']);

		return $vars;
	}
	
	public function __wakeup()
	{
		$this->docs = 0;
		$this->is_indexable = FALSE;
	}
}