<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class Datasource_Hybrid_Factory {
	
	/**
	 *
	 * @var string
	 */
	public $prefix = 'dshybrid_';
	
	/**
	 *
	 * @var string
	 */
	public $separator = '.';
	
	/**
	 *
	 * @var string
	 */
	public $table = 'hybriddatasources';
	
	/**
	 * 
	 * @param string $key
	 * @param string $name
	 * @param string $description
	 * @param integer $parent
	 * @return null|\Datasource_Hybrid_Section
	 */
	public function create($key, $name, $description, $parent) 
	{
		$parent = (int) $parent;

		$key = $this->get_full_key($key, $parent);

		if($key == NULL OR $this->exists($key))
		{
			return NULL;
		}

		$ds = new Datasource_Hybrid_Section($key, $parent);

		if(!$ds->create($name, $description))
		{
			return NULL;
		}

		if($this->create_table($ds->ds_id)) 
		{
			if($this->create_folder($ds->ds_id)) 
			{
				$this->update_struct($ds);
				$ds->save();

				return $ds;
			}

			$this->remove_table($ds->ds_id);
		}

		$ds->remove();

		return NULL;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return boolean
	 */
	public function remove($id) 
	{
		$ids = $this->get_children($id);

		if(!sizeof($ids))
		{
			return FALSE;
		}

		foreach($ids as $id) 
		{
			$ds = Datasource_Manager::load($id);
			
			if(!$ds) continue;
				
			$ds->remove();
			
			
			$this->remove_table($id);
			$this->remove_folder($id);
		}
		
		return (bool) DB::delete($this->table)
			->where('ds_id', 'in', $ids)
			->execute();
	}
	
	/**
	 * 
	 * @param array $doc_ids
	 * @return null|boolean
	 */
	public function remove_documents($doc_ids) 
	{
		if( !is_array( $doc_ids ) AND strpos(',', $doc_ids ) !== FALSE)
		{
			$doc_ids = explode(',', $doc_ids);
		}
		else if(!is_array( $doc_ids ))
		{
			$doc_ids = array($doc_ids);
		}
		
		if(empty($doc_ids))
		{
			return NULL;
		}

		$query = DB::select('id', 'ds_id')
			->from('dshybrid')
			->where('id', 'in', $doc_ids)
			->order_by('ds_id', 'desc')
			->execute();
		
		$type = array();
		
		foreach ($query as $row)
		{
			$type[$row['ds_id']][] = $row['id'];
		}
		
		foreach ($type as $id => $docs)
		{
			$ds = Datasource_Manager::load($id);
			$ds->remove_own_documents($docs);
		}
		
		unset($ds, $type);
		
		return TRUE;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Hybrid_Factory
	 */
	public function publish_documents($ids) 
	{
		return $this->set_published($ids, 1);
	}

	/**
	 * 
	 * @param array $ids
	 * @return \Datasource_Hybrid_Factory
	 */
	public function unpublish_documents($ids) 
	{
		return $this->set_published($ids, 0);
	}
	
	/**
	 * 
	 * @param array $ids
	 * @param boolean $value
	 * @return \Datasource_Hybrid_Factory
	 */
	public function set_published($ids, $value) 
	{
		if(sizeof($ids) > 0) 
		{
			$res = DB::select('dsh.id', 'dsh.ds_id')
				->from(array('dshybrid', 'dsh'))
				->join(array('datasources', 'dss'), 'left')
					->on('dsh.ds_id', '=', 'dss.ds_id')
				->where('dsh.id', 'in', $ids)
				->execute();
			
			$docs = array();
			foreach ($res as $row)
			{
				$docs[$row['ds_id']][] = $row['id'];
			}

			if(sizeof($docs)) 
			{
				$ds_ids = array_keys($docs);

				foreach($ds_ids as $ds_id) 
				{
					$ds = Datasource_Manager::load($ds_id);
					$ids = $docs[$ds_id];
					
					if($value)
					{
						$ds->add_to_index($ids);
					}
					else
					{
						$ds->remove_from_index($ids);
					}
					
					DB::update('dshybrid')
						->set(array(
							'published' => $value,
							'updated_on' => date('Y-m-d H:i:s')
						))
						->where('ds_id', '=', $ds_id)
						->where('id', 'in', $ids)
						->execute();

					unset($ds, $ids);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $ids
	 * @param integer $fromId
	 * @param integer $toId
	 */
	public function cast_documents($ids, $fromId, $toId) 
	{
		if(sizeof($ids) > 0) 
		{

			$from = Datasource_Manager::load($fromId);
			$to = Datasource_Manager::load($toId);

			$res = DB::select('id')
				->from('dshybrid')
				->where('ds_id', '=', $from->ds_id)
				->where('id', 'in', $ids)
				->execute();
			
			if(count($res) > 0) 
			{
				$add = $remove = array();
				$fromRec = $from->get_record(); 
				$toRec = $to->get_record();
				
				$path1 = explode(',', $from->path); 
				$path2 = explode(',', $to->path);

				$removeDs = array_diff($path1, $path2);
				$addDs = array_diff($path2, $path1);
				$commonDs = (int) max(array_intersect($path1, $path2));

				foreach($fromRec->fields as $key => $field)
				{
					if(!(isset($toRec->fields[$key]) AND $toRec->fields[$key]->ds_id == $field->ds_id))
					{
						$remove[] = $fromRec->fields[$key];
					}
				}

				foreach($toRec->fields as $key => $field)
				{
					if(!(isset($fromRec->fields[$key]) && $fromRec->fields[$key]->ds_id == $field->ds_id))
					{
						$add[] = $toRec->fields[$key];
					}
				}

				$lr = sizeof($remove); 
				$la = sizeof($add);
				$ids = array();

				foreach ($res as $row)
				{
					$doc = $from->get_document($row['id']);
					for($r = 0; $r < $lr; $r++)
					{
						$remove[$r]->onRemoveDocument($doc);
					}
					
					$ids[] = $doc->id;
				}

				if(sizeof($ids)) 
				{
					$failed = array();

					if(sizeof($removeDs)) 
					{
						foreach($removeDs as $dsId)
						{
							DB::delete('dshybrid_'. (int) $dsId)
								->where('id', 'in', $ids)
								->execute();
						}
					}

					foreach($ids as $k => $id) 
					{
						$success = TRUE;
						foreach($addDs as $dsId) 
						{
							$query = DB::insert('dshybrid_'. (int) $dsId)
								->columns(array('id'))
								->values(array($id))
								->execute();

							$success = $success && ($query[1] > 0);
						}
						
						if(!$success) 
						{
							foreach($addDs as $dsId)
							{
								DB::delete('dshybrid_'. (int) $dsId)
									->where('id', '=', $id)
									->execute();
							}

							$failed[] = $id;
							unset($ids[$k]);
						}
					}

					if(sizeof($failed)) 
					{
						if($commonDs > 0)
						{
							DB::update('dshybrid')
								->set(array(
									'ds_id' => $commonDs
								))
								->where('id', 'in', $failed)
								->execute();
						}
						else
						{
							DB::delete('dshybrid')
									->where('id', 'in', $failed)
									->execute();
						}
					}

					if(sizeof($ids))
					{
						foreach($ids as $id) 
						{
							$doc = $to->get_document($id);
							for($a = 0; $a < $la; $a++)
							{
								$add[$a]->onCreateDocument($doc);
							}

							$query = $toRec->get_sql($doc);
							foreach($query as $q)
							{
								$db->query($q);
							}
						}

						DB::update('dshybrid')
							->set(array('ds_id' => $to->ds_id))
							->where('id', 'in', $ids)
							->execute();
			
						$from->update_size();
						$to->update_size();
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * @param integer $ds_id
	 * @return array
	 */
	public function get_children($ds_id) 
	{
		return DB::select(array('t2.ds_id', 'id'))
			->from(array($this->table, 't1'), array($this->table, 't2'))
			->where('t1.ds_id', '=', (int) $ds_id)
			->where(DB::expr('INSTR(t2.ds_key, t1.ds_key)'), '=', 1)
			->order_by('t2.ds_key', 'desc')
			->execute()
			->as_array(NULL, 'id');
	}
	
	/**
	 * 
	 * @param string $key
	 * @param integer $parent
	 * @return string
	 */
	public function get_full_key($key, $parent)
	{
		$key = $this->validate_key($key);
		if(!$parent)
		{
			return $key;
		}

		$fullkey = DB::select('ds_key', 'path')
			->from($this->table)
			->where('ds_id', '=', $parent)
			->execute()
			->get('ds_key');
		
		if($fullkey)
		{
			$fullkey .= $this->separator . $key;
		}
		
		return $fullkey;
	}
	
	/**
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key) 
	{
		return (bool) DB::select('ds_id')
			->from($this->table)
			->where('ds_key', '=', $key)
			->execute()
			->get('ds_id');
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return boolean
	 */
	public function create_table($id) 
	{
		DB::query(NULL, '
			CREATE TABLE IF NOT EXISTS `:name` (
			 `id` int(11) unsigned NOT NULL default "0",
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		')
			->param(':name', DB::expr($this->prefix . $id))
			->execute();
		
		return TRUE;
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return boolean
	 */
	public function remove_table($id) 
	{
		DB::query(NULL, 'DROP TABLE `:name`')
			->param(':name', DB::expr($this->prefix . $id))
			->execute();
		
		return TRUE;
	}
	
	/**
	 * 
	 * @param Datasource_Section $ds
	 * @return array
	 */
	public function update_struct($ds) 
	{
		if($ds->parent) 
		{
			$path = DB::select('path')
				->from($this->table)
				->where('ds_id', '=', $ds->parent)
				->execute()
				->get('path');

			$ds->path = $path . ',' . $ds->ds_id;
		}
		else
		{
			$ds->path = '0,' . $ds->ds_id;
		}
		
		$data = array(
			'ds_id' => $ds->ds_id, 
			'parent' => $ds->parent, 
			'ds_key' => $ds->key, 
			'path' => $ds->path
		);
		
		return DB::insert($this->table)
			->columns(array_keys($data))
			->values(array_values($data))
			->execute();
	}
	
	/**
	 * 
	 * @param integer $folder
	 * @return boolean
	 */
	public function create_folder($folder) 
	{
		settype($folder, 'int');
		$dir = PUBLICPATH . '/hybrid/' . $folder;

		if($folder > 0) 
		{
			if(!is_dir($dir))
			{
				mkdir($dir, 0755, TRUE);
			}
			
			chmod($dir, 0755);
			
			return TRUE;
		}

		return FALSE;
	}
	
	/**
	 * 
	 * @param integer $folder
	 * @return boolean
	 */
	public function remove_folder($folder) 
	{
		settype($folder, 'int');
		$dir = PUBLICPATH . '/hybrid/' . $folder;
	
		if($folder > 0 AND is_dir($dir)) 
		{
			unlink($dir);
		}

		return !is_dir($dir);
	}
	
	/**
	 * 
	 * @param string $key
	 * @return string
	 */
	public function validate_key($key) 
	{
		$key = preg_replace('/[^A-Za-z0-9]+/', '', $key);
		$key = strtolower($key);
		if(strlen($key) > 16)
		{
			$key = substr($key, 0, 16);
		}

		return $key;
	}
}