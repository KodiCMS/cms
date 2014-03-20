<?php defined('SYSPATH') or die('No direct access allowed.');

class DataSource_Hybrid_Field_Source_User extends DataSource_Hybrid_Field {
	
	protected $_props = array(
		'default' => NULL,
		'isreq' => FALSE,
		'only_current' => FALSE
	);

	public function __construct( array $data )
	{
		parent::__construct( $data );
		
		$this->family = DataSource_Hybrid_Field::FAMILY_SOURCE;
	}
	
	public function set( array $data )
	{
		$data['only_current'] = !empty($data['only_current']) ? TRUE : FALSE;
		
		return parent::set( $data );
	}
	
	public function onCreateDocument(DataSource_Hybrid_Document $doc) 
	{
		$doc->set($this->name, AuthUser::getId());
	}
	
	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new)
	{
		if($this->only_current === TRUE)
		{
			$new->set($this->name, AuthUser::getId());
		}
		
		if( ! $this->is_exists( $new->get($this->name) ))
		{
			$new->set($this->name, $old->get($this->name));
		}
	}
	
	public function get_user($id)
	{
		return ORM::factory('user', $id);
	}
	
	public function is_exists($id)
	{
		return $this->get_user($id)->loaded();
	}
	
	public function get_users()
	{
		$users = array('--------');
		$users = $users + ORM::factory('user')->find_all()->as_array('id', 'username');
		
		return $users;
	}

	public static function fetch_widget_field( $widget, $field, $row, $fid )
	{
		return !empty($row[$fid]) 
			? array(
				'username' => $row[$fid],
				'id' => $row['user_id']
			)
			: array(
				'username' => '',
				'id' => ''
			);
	}
	
	public function get_type()
	{
		return 'TINYINT(4)';
	}
	
	public function get_query_props(\Database_Query $query)
	{
		return $query->join('users', 'left')
			->on(DataSource_Hybrid_Field::PREFFIX . $this->key, '=', 'users' . '.id')
			->select(array('users.username', $this->id))
			->select(array('users.id', 'user_id'));
	}
}