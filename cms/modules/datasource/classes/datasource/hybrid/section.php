<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class Datasource_Hybrid_Section extends Datasource_Section {
	
	/**
	 *
	 * @var string
	 */
	public $ds_table = 'dshybrid';
	
	/**
	 *
	 * @var string
	 */
	public $ds_type = 'hybrid';
	
	/**
	 *
	 * @var string
	 */
	public $key;
	
	/**
	 *
	 * @var integer
	 */
	public $parent;
	
	/**
	 *
	 * @var string
	 */
	public $path = NULL;
	
	/**
	 *
	 * @var Datasource_Hybrid_Record
	 */
	public $record = NULL;
	public $read_sql = NULL;
	public $indexed_doc;
	public $doc_intro;
	public $indexed_doc_query;
	
	public $all_doc = TRUE;
	public $auto_cast = TRUE;

	public function __construct($key = NULL, $parent = NULL)
	{
		$this->key = $key;
		$this->parent = $parent;
		
		$this->all_doc = Cookie::get('all_doc', 'enabled') != 'disabled';
		$this->auto_cast = Cookie::get('auto_cast', 'enabled') != 'disabled';
		$this->page_size = Cookie::get('page_size', 30);
	}
	
	/**
	 * 
	 * @return array
	 */
	public function fields( )
	{
		$fields = array(
			'ID' => 50, 
			'Header' => NULL,
			'Section' => 150,
			'Date' => 150
		);
		
		if(!$this->all_doc)
		{
			unset($fields['type']);
		}
		
		return $fields;
	}
	
	/**
	 * 
	 * @return \Datasource_Hybrid_Section
	 */
	public function remove() 
	{
		$ids = DB::select('id')
			->from('dshybrid')
			->where('ds_id', '=', $this->ds_id)
			->execute()
			->as_array(NULL, 'id');
		
		$this->remove_own_documents($ids);
	
		DB::delete('datasources')
			->where('ds_id', '=', $this->ds_id)
			->execute();

		$record = $this->get_record();
		$record->destroy();
		
		return $this;
	}
	
	/**
	 * 
	 * @param Datasource_Document $doc
	 * @return Datasource_Document
	 */
	public function create_document($doc) 
	{
		$id = $this->create_empty_document($doc->header);
		$doc->id = $id;

		$record = $this->get_record();
		$record->initialize_document($doc);
		$query = $record->get_sql($doc);

		$success = TRUE;
	
		foreach($query as $q)
		{
			$_query = DB::query(Database::UPDATE, $q)->execute();
		}

		if($success) 
		{
			$this->update_size();
			$this->add_to_index($id);
		} 
		else 
		{
			$record->destroy_document($doc);
			$this->remove_empty_documents(array($doc->id));
			$doc->id = 0;
		}
		
		return $doc;
	}
	
	/**
	 * 
	 * @param Datasource_Document $doc
	 * @return boolean
	 */
	public function update_document($doc) 
	{
		$old = $this->get_document($doc->id);
	
		if($old !== NULL AND !$old->id)
		{
			return FALSE;
		}

		$record = $this->get_record();
		$record->document_changed($old, $doc);
		$query = $record->get_sql($doc, TRUE);

		$result = TRUE;
		foreach($query as $q)
		{
			$result = DB::query(NULL, $q)->execute() AND $result;
		}

		if($old->published != $doc->published) 
		{
			if($doc->published)
			{
				$this->add_to_index($old->id);
			}
			else
			{
				$this->remove_from_index($old->id);
			}
		} 
		elseif($old->published)
		{
			$this->update_index($old->id);
		}

		return $result;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return \Datasource_Hybrid_Document
	 */
	public function get_document($id)
	{
		$doc = NULL;

		if($id > 0) 
		{
			$doc = new Datasource_Hybrid_Document($this->get_record());
			
			if(!$this->read_sql) 
			{
				$record = $this->get_record();
				$parents = $this->get_parents();
				
				$query = DB::select(array('dshybrid.id', 'id'))
					->select('ds_id', 'published', 'header')
					->from('dshybrid')
					->where('dshybrid.id', '=', $id)
					->limit(1);
				
				foreach ($record->fields as $key => $data)
				{
					$query->select($key);
				}

				foreach($parents as $parent) 
				{
					$query
						->from("dshybrid_$parent")
						->where("dshybrid_$parent.id", '=', DB::expr('`dshybrid`.`id`'));
				}
				
				$this->read_sql = (string) $query;
			}
			
			$result = DB::query( Database::SELECT, $this->read_sql )
				->execute()
				->current();

			if($result)
			{
				$doc->read_values($result);
			}
		}

		return $doc;
	}
	
	/**
	 * 
	 * @return \Datasource_Hybrid_Document
	 */
	public function get_empty_document() 
	{
		$record = $this->get_record();
		$doc = new Datasource_Hybrid_Document($record);
		
		return $doc;
	}
	
	/**
	 * @return Datasource_Hybrid_Record
	 */
	public function get_record($id = NULL, $alias = false) 
	{
		if($this->record === NULL)
		{
			$this->record = new Datasource_Hybrid_Record($this);
		}

		return $this->record;
	}
	
	/**
	 * 
	 * @param string $header
	 * @return null|integer
	 */
	public function create_empty_document($header) 
	{
		$data = array(
			'ds_id' => $this->ds_id,
			'header' => $header,
			'created_on' => date('Y-m-d H:i:s'),
		);
		
		$query = DB::insert('dshybrid')
			->columns(array_keys($data))
			->values(array_values($data))
			->execute();

		$id = $query[0];

		$parents = $this->get_parents();

		$success = TRUE;

		foreach($parents as $parent) 
		{
			$query = DB::insert("dshybrid_$parent")
				->columns(array('id'))
				->values(array($id))
				->execute();
			$success = $success AND ($query[1] > 0);
		}

		if($success AND $id)
		{
			return $id;
		}
		
		$this->remove_empty_documents(array($id));

		return NULL;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Hybrid_Section
	 */
	public function remove_empty_documents($ids) 
	{
		if(empty($ids))
		{
			return $this;
		}

		DB::delete("dshybrid")
			->where('id', 'in', $ids)
			->execute();

		$parents = $this->get_parents();

		foreach($parents as $parent)
		{
			DB::delete("dshybrid_$parent")
				->where('id', 'in', $ids)
				->execute();
		}

		$this->remove_from_index($ids);
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Hybrid_Section
	 */
	public function delete($ids) 
	{
		$dsf = new Datasource_Hybrid_Factory();
		$dsf->remove_documents($ids);
		
		return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_parents()
	{
		$parents = explode(',', $this->path);
		unset($parents[0]);
		
		return $parents;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Hybrid_Section
	 */
	public function remove_own_documents($ids) 
	{
		$record = $this->get_record();
		$this->remove_empty_documents($ids);
		$this->update_size();
		
		return $this;
	}

	/**
	 * 
	 * @param integer $ds_id
	 * @return array
	 */
	public function get_headline($ds_id)
	{
		$ds_id = (int) $ds_id;
		
		$query = DB::select()
			->select('d.published', 'd.header', array('d.created_on', 'date'))
			->order_by('d.created_on', 'desc');
		
		if($this->all_doc)
		{
			$query->select('dss.name', 'ds.id')
				->from(array('dshybrid_' . $ds_id, 'ds'))
				->join(array('dshybrid', 'd'))
					->on('ds.id', '=', 'd.id');
					
			if($this->auto_cast)
			{
				$query->select(array('d.ds_id', 'ds_id'));
			}
		}
		else
		{
			$query->select('d.id')
					->from(array('dshybrid', 'd'))
				->where('d.ds_id', '=', $ds_id);
		}
		
		$query->join(array('datasources', 'dss'))
				->on('d.ds_id', '=', 'dss.ds_id');
		
		$result = array(0, array());

		$query = $query->execute();
		if($query->count() > 0)
		{
			$result[0] = $query->count();
			
			foreach ( $query as $row )
			{
				$hl[$row['id']] = array(
					'published' => $row['published'] == 1,
					'header' => $row['header'],
					'date' => Date::format($row['date'])
				);

				if($this->auto_cast AND $this->all_doc) 
				{
					$hl[$row['id']]['ds_id'] = $row['ds_id'];
					$hl[$row['id']]['type'] = $row['name'];
				}
			}
			
			$result[1] = $hl;
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param \DataSource_Document $doc
	 * @param string $field
	 * @param integer $id
	 * @return boolean
	 */
	function set_field($doc, $field, $id) 
	{
		$db_field = DB::select('id', 'ds_id', 'name', 'family', 'isown')
			->from('dshfields')
			->where('id', '=', $field)
			->where('from_ds', '=', $this->ds_id)
			->limit(1)
			->execute()
			->current();
		
		if($db_field === NULL)
		{
			return FALSE;
		}

		$ds_id = (int) $db_field['ds_id'];
		$field_name = $db_field['name'];
		$family = $r['family'];
		
		$doc_filed = DB::select($field_name)
			->from('dshybrid_' . $ds_id)
			->where('id', '=', $doc)
			->limit(1)
			->execute()
			->get($field_name);

		if($doc_filed === NULL)
		{
			return FALSE;
		}

		$oldvalue = $doc_filed;
		$newvalue = ($oldvalue ? $oldvalue . ',' : '') . $id;
		if(UTF8::strlen($newvalue) > 255)
		{
			return FALSE;
		}
		
		DB::update('dshybrid_' . $ds_id)
			->set(array(
				$field_name => $newvalue
			))
			->where('id', '=', $doc)
			->limit(1)
			->execute();

		return TRUE;
	}
	
	/**
	 * @param integer $doc_id
	 * @return \DataSource_Document
	 * @throws Kohana_Exception
	 */
	public function get_doc($doc_id) 
	{
		static $ds, $doc;
		$result = NULL;
		
		$doc_id = (int) $doc_id;

		if(isset($doc[$doc_id]))
		{
			$result = $doc[$doc_id];
		}
		else 
		{
			$ds_id = DB::select('ds_id')
				->from('dshybrid')
				->where('id', '=', $doc_id)
				->execute()
				->get('ds_id');
	
			if(!isset($ds[$ds_id])) 
			{
				$ds[$ds_id] = Datasource_Manager::load($ds_id);

				if($ds[$ds_id] === NULL)
				{
					throw new Kohana_Exception('NULL object');
				}
			}

			$doc[$doc_id] = $ds[$ds_id]->get_document($doc_id);
			$result = $doc[$doc_id];
		}

		return $result;
	}

	public function __sleep()
	{
		$vars = array_keys(get_object_vars($this));
		unset($vars['docs'], $vars['is_indexable'], $vars['record'], $vars['read_sql'], $vars['indexed_doc_query']);

		return $vars;
	}
	
	public function __wakeup()
	{
		$this->record = NULL;
		$this->read_sql = NULL;
		$this->indexed_doc_query = NULL;
		
		parent::__wakeup();
	}
}