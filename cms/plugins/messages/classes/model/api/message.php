<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package    KodiCMS/Api
 */
class Model_API_Message extends Model_API {
	
	const STATUS_READ	= 1; // Сообщение прочитано
	const STATUS_NEW	= 0; // Новое сообщение	
	
	protected $_table_name = 'messages';
	
	protected $_secured_columns = array(
		'text'
	);
	
	public function get_all($user_id, $parent_id, $fields)
	{
		$user_id = $this->prepare_param($user_id, array('Valid', 'numeric'));
		$parent_id = $this->prepare_param($parent_id, array('Valid', 'numeric'));
		$fields = $this->prepare_param($fields);

		$query = DB::select($this->_table_name . '.id')
			->select_array(  $this->filtered_fields( $fields ) )
			->from($this->_table_name)
			->join('messages_users', 'left')
				->on($this->_table_name.'.id', '=', 'messages_users.message_id')
			->where('messages_users.user_id', '=', $user_id)
			->where('messages_users.parent_id', '=', $parent_id)
			->order_by('created_on', 'desc');
		
		if(in_array('author', $fields))
		{
			$query->join( 'users', 'left' )
				->on( 'users.id', '=', $this->_table_name . '.from_user_id' )
				->select( array('users.username', 'author') );
		}
		
		if(in_array('status', $fields))
		{
			$query->select( 'messages_users.status' );
		}
		
		if(in_array('is_read', $fields))
		{
			$min_status = DB::select(DB::expr('MIN('.Database::instance()->quote_column('status').')'))
				->from(array('messages_users', 'ms'))
				->where('ms.parent_id', '=', DB::expr(Database::instance()->quote_column('messages_users.message_id')))
				->or_where('ms.message_id', '=', DB::expr(Database::instance()->quote_column('messages_users.message_id')));
			
			$query->select(array($min_status, 'is_read'))
				->order_by('is_read', 'asc');
		}
		
		return $query
			->execute()
			->as_array();
	}
	
	public function get_by_id($message_id, $user_id, $fields)
	{
		$message_id = $this->prepare_param($message_id, array('Valid', 'numeric'));
		$user_id = $this->prepare_param($user_id, array('Valid', 'numeric'));
		$fields = $this->prepare_param($fields);
		
		$query = DB::select('from_user_id', 'messages.id')
			->select_array(  $this->filtered_fields( $fields ) )
			->from('messages_users')
			->join('messages', 'left')
				->on('messages.id', '=', 'messages_users.message_id')
			->where('messages_users.user_id', '=', $user_id)
			->where('messages.id', '=', $message_id)
			->limit(1);
		
		if(in_array('author', $fields))
		{
			$query->join( 'users', 'left' )
				->select( array('users.username', 'author') )
				->on( 'users.id', '=', $this->_table_name . '.from_user_id' );
		}
		
		if(in_array('is_read', $fields))
		{
			$query->select( array('messages_users.status', 'is_read') );
		}
		
		return $query
			->execute()
			->current();
	}
	
	public function send($title, $text, $from = NULL, $to, $parent_id = 0, $to_from = TRUE)
	{
		if (!is_array($to)) 
		{
			$to = array($to);
		}
		
		if(!$from)
		{
			$from = NULL;
		}
		
		if(!empty($to)) 
		{
			if($from !== NULL AND $to_from === TRUE) $to[] = $from;
			$to = array_unique($to);

			$data = array(
				'created_on' => date('Y-m-d H:i:s'),
				'text' => Kses::filter($text, Kohana::$config->load('global')->get('allowed_html_tags')),
				'title' => $title,
				'from_user_id' => $from
			);
			
			list($message_id, $rows) = DB::insert($this->table_name())
				->columns(array_keys($data))
				->values($data)
				->execute($this->_db);
			
			if($message_id)
			{
				$insert = DB::insert('messages_users')
					->columns(array('status', 'user_id', 'message_id', 'parent_id'));

				foreach ($to as $id)
				{
					$insert->values(array(
						'status' => self::STATUS_NEW,
						'user_id' => (int) $id,
						'message_id' => $message_id,
						'parent_id' => (int)$parent_id
					));
					
					self::clear_cache($id);
					Observer::notify('send_message', (int) $id, $text);
				}

				$insert->execute($this->_db);
				
				if($from !== NULL)
				{
					Api::post('user-messages.mark_read', array(
						'id' => $message_id, 'uid' => $from
					));
				}
				
				return $message_id;
			}
		}
		
		return FALSE;
	}
	
	public function mark_read($message_id, $user_id)
	{		
		$update = DB::update('messages_users')
			->where('user_id', '=', (int) $user_id)
			->where('message_id', '=', $message_id)
			->set(array(
				'status' => self::STATUS_READ,
				'updated_on' => date('Y-m-d H:i:s')
			))
			->execute();
		
		self::clear_cache($user_id);
		
		return $update;
	}
	
	public function count_new($user_id)
	{
		return DB::select(array(DB::expr( 'COUNT(*)'), 'total'))
			->from('messages_users')
			->where('status', '=', self::STATUS_NEW)
			->where('user_id', '=', (int) $user_id)
			->execute()
			->get('total', 0);
	}
	
	public function delete_by_user($message_id, $user_id)
	{
		$message_id = $this->prepare_param($message_id, array('Valid', 'numeric'));

		if(empty($message_id))
		{
			return FALSE;
		}

		$delete = DB::delete('messages_users')
			->where('user_id', '=', (int) $user_id)
			->where_open()
				->where('message_id', '=', (int) $message_id)
				->or_where('parent_id', '=', (int) $message_id)
			->where_close()
			->execute();
		
		$count = DB::select(array(DB::expr( 'COUNT(*)'), 'total'))
			->from('messages_users')
			->where('message_id', '=', (int) $message_id)
			->execute()
			->get('total', 0);
		
		if( $count == 0 )
		{
			$this->delete_by_id($message_id);
		}
		
		self::clear_cache($user_id);
		
		return $delete;
	}
	
	public function delete_by_id($id)
	{
		DB::delete('messages')
			->where('id', '=', (int) $id)
			->execute();
		 
		return TRUE;
	}

	protected static function clear_cache($user_id)
	{
		Cache::instance()->delete('Database::cache(count_messages::'.$user_id.')');
	}
}