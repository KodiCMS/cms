<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class DataSource_Hybrid_Field_Factory {

	/**
	 * Создание нового поля в таблице полей
	 * и создание нового поля в таблице текущего раздела
	 * 
	 * @see Controller_Hybrid_Field::_add()
	 * 
	 * @param DataSource_Hybrid_Record $record
	 * @param DataSource_Hybrid_Field $field
	 * @return boolean
	 */
	public static function create_field( 
			DataSource_Hybrid_Record $record, DataSource_Hybrid_Field $field) 
	{
		$field->name = self::get_full_key($field->name);
		
		$field->set_ds($record->ds_id());
		$field->get_type();

		if( ! self::field_not_exists($field->name, $record->ds_id()) )
		{
			return FALSE;
		}

		if($field->create()) 
		{
			self::alter_table_add_field($field);

			$record->fields[$field->name] = $field;
			
			return $field->id;
		}

		return FALSE;
	}
	
	/**
	 * Обновление поля в разделе и в таблице полей
	 * 
	 * @see Controller_Hybrid_Field::_edit()
	 * 
	 * @param DataSource_Hybrid_Field $old
	 * @param DataSource_Hybrid_Field $new
	 * 
	 * @return DataSource_Hybrid_Field
	 */
	public static function update_field( DataSource_Hybrid_Field $old, DataSource_Hybrid_Field $new ) 
	{
		$new->get_type();
		
		$new->name = self::get_full_key($new->name);
		
		$new->update();

		switch ($old->family) 
		{
			case DataSource_Hybrid_Field::FAMILY_PRIMITIVE:
				self::alter_table_update_field($old, $new);
				break;
		}

		return $new;
	}
	
	/**
	 * Удаление полей по их ключу из раздела в из таблицы полей
	 * 
	 * @param DataSource_Hybrid_Record $record
	 * @param array $keys
	 */
	public static function remove_fields( DataSource_Hybrid_Record $record, $keys) 
	{
		if($keys === NULL)
		{
			return;
		}

		if(!is_array( $keys ))
		{
			$keys = array($keys);
		}
		
		$fields = $record->fields();

		foreach($keys as $key)
		{
			if(
				isset($fields[$key]) 
			AND
				$fields[$key]->ds_id == $record->ds_id()
			) 
			{
				$fields[$key]->remove();
				
				self::alter_table_drop_field($fields[$key]);
			}
		}
	}
	
	/**
	 * Загрузка поля по его ID
	 * 
	 * @param integer $id
	 * @return null|DataSource_Hybrid_Field
	 */
	public static function get_field($id) 
	{
		$id = (int) $id;

		$result = self::get_fields(array($id));
		
		if(empty($result))
		{
			$result[0] = NULL;
		}
			
		return $result[0];
	}
	
	/**
	 * Загрузка массива полей по массиву идентификаторов
	 * 
	 * @param array $ids
	 * @return array
	 */
	public static function get_fields( array $ids = NULL ) 
	{
		$result = array();
		
		if( empty($ids) )
		{
			return $result;
		}
		
		$query = DB::select()
			->from('dshfields')
			->where('id', 'in', $ids)
			->order_by('position', 'asc')
			->execute();

		if($query)
		{
			foreach ($query as $row)
			{
				$result[] = self::get_field_from_array($row);
			}
		}

		return $result;
	}
	
	/**
	 * Загрузка полей раздела
	 * 
	 * @staticvar $cached_fields Кеш загруженных полей разделов
	 * 
	 * @see DataSource_Hybrid_Record::load()
	 * 
	 * @param integer $ds_id Идентификатор раздела
	 * @param array $type Ключ типа поля
	 * @return array DataSource_Hybrid_Field
	 */
	public static function get_section_fields($ds_id, array $type = NULL) 
	{
		static $cached_fields;

		$ds_id = (int) $ds_id;
		
		if(isset( $cached_fields[$ds_id]) )
		{
			return $cached_fields[$ds_id];
		}
		
		$fields = array();

		$query = DB::select()
			->from('dshfields')
			->where('ds_id', '=', $ds_id)
			->order_by('position');
		
		if( ! empty($type) )
		{
			$query->where('type', 'in', $type);
		}
		
		$query = $query
			->execute()
			->as_array('id');

		foreach ($query as $id => $row)
		{
			$fields[$id] = self::get_field_from_array($row);
		}
		
		$cached_fields[$ds_id] = $fields;

		return $fields;
	}
	
	/**
	 * Преобразование массива в объект поля
	 * 
	 * @see DataSource_Hybrid_Field_Factory::get_fields()
	 * 
	 * @param array $array
	 * @return null|\DataSource_Hybrid_Field
	 * @throws Kohana_Exception
	 */
	public static function get_field_from_array( array $array = NULL ) 
	{
		if( empty($array) OR !isset($array['type']) )
		{
			return NULL;
		}
			
		$class_name = 'DataSource_Hybrid_Field_' . $array['type'];

		if( ! class_exists( $class_name ))
		{
			throw new Kohana_Exception('Class :class_name not exists', array(
				':class_name' => $class_name));
		}
		
		if(isset($array['props']))
		{
			$props = unserialize($array['props']);
			unset($array['props']);

			if( is_array( $props))
			{
				$array = array_merge($array, $props);
			}
		}

		$result = DataSource_Hybrid_Field::factory($array['type'], $array);
		$result->set_id( Arr::get($array, 'id') );
		$result->set_ds( Arr::get($array, 'ds_id') );

		return $result;
	}
	
	/**
	 * Генерация ключа для поля
	 * 
	 * @param string $key
	 * @return string
	 */
	public static function get_full_key( $key )
	{
		$key = str_replace(DataSource_Hybrid_Field::PREFFIX, '', $key);
		$key = URL::title($key, '_');
		$key = strtolower($key);
		
		if(strlen($key) > 32)
		{
			$key = substr($key, 0, 32);
		}
		
		if(empty($key))
		{
			return NULL;
		}
		
		return DataSource_Hybrid_Field::PREFFIX . $key;
	}
	
	/**
	 * Проверка на уникальность ключа поля в разделе
	 * 
	 * @param string $key
	 * @param integer $ds_id
	 * @return boolean
	 */
	public static function field_not_exists($key, $ds_id)
	{
		return DB::select('id')
			->from('dshfields')
			->where('ds_id', '=', (int) $ds_id)
			->where('name', '=', $key)
			->limit(1)
			->execute()
			->get('id') === NULL;
	}

	/**
	 * Добавление поле в таблицу раздела
	 * 
	 * @param DataSource_Hybrid_Field $field
	 * @return boolean
	 */
	public static function alter_table_add_field($field) 
	{
		$params = array(
			':table' => DB::expr($field->ds_table),
			':key' => DB::expr($field->name),
			':type' => DB::expr($field->get_type()),
			':default' => DB::expr('')
		);
		
		if(!empty($field->default))
		{
			$params[':default'] = DB::expr('DEFAULT "' .  $field->default . '"');
		}
		
		return (bool) DB::query(NULL, 
				'ALTER TABLE `:table`  ADD `:key` :type :default'
			)
			->parameters($params)
			->execute();
	}
	
	/**
	 * Удаление поля из таблицы раздела
	 * 
	 * @param DataSource_Hybrid_Field $field
	 * @return boolean
	 */
	public static function alter_table_drop_field($field)
	{
		$params = array(
			':table' => DB::expr($field->ds_table),
			':key' => DB::expr($field->name)
		);

		return (bool) DB::query(NULL, 
				'ALTER TABLE `:table` DROP `:key`'
			)
			->parameters($params)
			->execute();
	}
	
	/**
	 * Обновление поля в таблице раздела
	 * 
	 * @param DataSource_Hybrid_Field $field
	 * @return boolean
	 */
	public static function alter_table_update_field($old, $field)
	{
		$params = array(
			':table' => DB::expr($field->ds_table),
			':old_key' => DB::expr($old->name),
			':new_key' => DB::expr($field->name),
			':type' => DB::expr($field->get_type()),
			':default' => DB::expr('')
		);
		
		if(!empty($field->default))
		{
			$params[':default'] = DB::expr('DEFAULT "' .  $field->default . '"');
		}

		return (bool) DB::query(NULL, 
				'ALTER TABLE `:table` CHANGE `:old_key` `:new_key` :type :default'
			)
			->parameters($params)
			->execute();
	}
}