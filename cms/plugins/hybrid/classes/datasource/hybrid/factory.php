<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class DataSource_Hybrid_Factory {

	/**
	 * Преффикс таблиц разделов
	 */
	const PREFIX = 'dshybrid_';
	
	/**
	 * Типы виджетов для которых очищать кеш при обновлении данных в документах
	 * 
	 * @var array
	 */
	public static $widget_types = array('hybrid_headline', 'hybrid_document');

	/**
	 * Создание таблицы раздела и директории
	 * 
	 * @param string $key
	 * @param DataSource_Section_Hybrid
	 * @param integer $parent
	 * @return null|\DataSource_Hybrid_Section
	 */
	public static function create( DataSource_Section_Hybrid $ds ) 
	{
		if(self::create_table($ds->id())) 
		{
			if(self::create_folder($ds->id())) 
			{
				$ds->save();

				return $ds;
			}

			self::remove_table($ds->id());
		}

		$ds->remove();

		return NULL;
	}
	
	/**
	 * Удалении таблицы раздела и директории
	 * 
	 * @param integer $id
	 * @return boolean
	 */
	public static function remove($id) 
	{
		self::remove_table($id);
		self::remove_folder($id);
	}
	
	/**
	 * Удаление документов по ID.
	 * Поиск документов будет происходить во всех разделах
	 * 
	 * Если идентификаторы переданы в виде строки будет применена функция
	 * explode с разделителем ","
	 * 
	 * Обычно данный метод используется для удаления связанных документов
	 * 
	 * @param array|string $doc_ids array(1,2,..) OR "1,2,3,.."
	 * @return null|boolean
	 */
	public static function remove_documents( $doc_ids = NULL ) 
	{
		if( !is_array( $doc_ids ) AND strpos(',', $doc_ids ) !== FALSE)
		{
			$doc_ids = explode(',', $doc_ids);
		}
		else if(!is_array( $doc_ids ))
		{
			$doc_ids = array($doc_ids);
		}
		
		if( empty($doc_ids) )
		{
			return NULL;
		}

		$query = DB::select('id', 'ds_id')
			->from('dshybrid')
			->where('id', 'in', $doc_ids)
			->order_by('ds_id', 'desc')
			->execute();
		
		$documents = array();
		
		foreach ($query as $row)
		{
			$documents[$row['ds_id']][] = $row['id'];
		}
		
		foreach ($documents as $ds_id => $ids)
		{
			$ds = Datasource_Data_Manager::load( $ds_id );
			$ds->remove_documents( $ids );
			Datasource_Data_Manager::clear_cache( $ds_id, self::$widget_types);
		}
		
		unset($ds, $documents, $query);
		
		return TRUE;
	}
	
	/**
	 * Опубликовать документы раздела по ID
	 * 
	 * @param array $ids
	 * @return \DataSource_Hybrid_Factory
	 */
	public function publish_documents( array $ids) 
	{
		return $this->set_published($ids, TRUE);
	}

	/**
	 * Снять с публикации документы раздела по ID
	 * 
	 * @param array $ids
	 * @return \DataSource_Hybrid_Factory
	 */
	public function unpublish_documents( array $ids) 
	{
		return $this->set_published($ids, FALSE);
	}
	
	/**
	 * Опубликовать или снять с публикации документы раздела по ID
	 * 
	 * @param array $ids
	 * @param boolean $value
	 * @return \DataSource_Hybrid_Factory
	 */
	public function set_published( array $ids, $value) 
	{
		if( empty($ids) ) return $this;

		$res = DB::select('dsh.id', 'dsh.ds_id')
			->from(array('dshybrid', 'dsh'))
			->join(array('datasources', 'dss'), 'left')
				->on('dsh.ds_id', '=', 'dss.id')
			->where('dsh.id', 'in', $ids)
			->execute();

		$docs = array();
		foreach ($res as $row)
		{
			$docs[$row['ds_id']][] = $row['id'];
		}

		if( !empty($docs) ) 
		{
			$ds_ids = array_keys($docs);

			foreach($ds_ids as $ds_id) 
			{
				$ds = Datasource_Data_Manager::load($ds_id);
				$ids = $docs[$ds_id];

				if($value === TRUE)
				{
					$ds->add_to_index($ids);
				}
				else
				{
					$ds->remove_from_index($ids);
				}

				DB::update('dshybrid')
					->set(array(
						'published' => (int) $value,
						'updated_on' => date('Y-m-d H:i:s')
					))
					->where('ds_id', '=', $ds_id)
					->where('id', 'in', $ids)
					->execute();

				unset($ds, $ids, $docs);

				Datasource_Data_Manager::clear_cache( $ds_id, self::$widget_types);
			}
		}
		
		return $this;
	}

	/**
	 * Создание таблицы раздела
	 * 
	 * @see DataSource_Hybrid_Factory::create()
	 * 
	 * @param integer $id
	 * @return boolean
	 */
	public static function create_table($id) 
	{
		DB::query(NULL, '
			CREATE TABLE IF NOT EXISTS `:name` (
			 `id` int(11) unsigned NOT NULL default "0",
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		')
			->param(':name', DB::expr(self::PREFIX . $id))
			->execute();
		
		return TRUE;
	}
	
	/**
	 * Создание директории для файлов раздела
	 * 
	 * @see DataSource_Hybrid_Factory::create()
	 * 
	 * @param integer $folder
	 * @return boolean
	 */
	public static function create_folder($folder) 
	{
		settype($folder, 'int');
		$dir = PUBLICPATH . 'hybrid' . DIRECTORY_SEPARATOR . $folder;

		if($folder > 0) 
		{
			if(!is_dir($dir))
			{
				mkdir($dir, 0777, TRUE);
			}
			
			chmod($dir, 0777);
			
			return TRUE;
		}

		return FALSE;
	}
	
	/**
	 * Удаление таблицы раздела
	 * 
	 * @see DataSource_Hybrid_Factory::remove()
	 * 
	 * @param integer $id
	 * @return boolean
	 */
	public static function remove_table($id) 
	{
		DB::query(NULL, 'DROP TABLE `:name`')
			->param(':name', DB::expr( self::PREFIX . $id))
			->execute();
		
		return TRUE;
	}
	
	/**
	 * Удаление директории для файлов раздела
	 * 
	 * @see DataSource_Hybrid_Factory::remove()
	 * 
	 * @param integer $folder
	 * @return boolean
	 */
	public static function remove_folder($folder) 
	{
		settype($folder, 'int');
		$dir = PUBLICPATH . 'hybrid' . DIRECTORY_SEPARATOR . $folder;
	
		if($folder > 0 AND is_dir($dir)) 
		{
			$dir_handle = opendir($dir);
			
			if (!$dir_handle) return FALSE;
			
			while($file = readdir($dir_handle))
			{
				if ($file != "." AND $file != "..")
				{
					unlink($dir . DIRECTORY_SEPARATOR . $file);
				}
			}

			closedir($dir_handle);
			rmdir($dir);
		}

		return !is_dir($dir);
	}
}