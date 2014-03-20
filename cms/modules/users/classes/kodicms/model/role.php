<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/Users
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Role extends Model_Auth_Role {
	
	public function labels()
	{
		return array(
			'name' => __('Name'),
			'description' => __('Description')
		);
	}
	
	public function filters()
	{
		return array(
			'name' => array(
				array('strtolower'),
				array('URL::title')
			)
		);
	}
	
	public function form_columns()
	{
		return array(
			'id' => array(
				'type' => 'input',
				'editable' => FALSE,
				'length' => 10
			),
			'name' => array(
				'type' => 'input',
				'length' => 50
			),
			'description' => array(
				'type' => 'textarea'
			)
		);
	}

	public function permissions()
	{
		return DB::select('action')
			->from('roles_permissions')
			->where('role_id', '=', $this->id)
			->execute()
			->as_array(NULL, 'action');
	}
	
	public function set_permissions( array $new_permissions = NULL )
	{
		DB::delete('roles_permissions')
			->where('role_id', '=', $this->id)
			->execute();
		
		if(!empty($new_permissions))
		{
			$insert = DB::insert('roles_permissions')
				->columns(array('role_id', 'action'));

			foreach($new_permissions as $action => $status)
			{
				$insert->values(array($this->id, $action));
			}
			
			$insert->execute();
		}
		
		return $this;
	}
	
	public function after_create()
	{	
		Kohana::$log->add(Log::INFO, 'Role :role has been added by :user', array(
			':role' => HTML::anchor(Route::url('backend', array(
				'controller' => 'roles',
				'action' => 'edit',
				'id' => $this->id
			)), $this->name),
		))->write();

		Observer::notify( 'role_after_add', $this );
	}
	
	public function after_update()
	{
		Kohana::$log->add(Log::INFO, 'Role :role has been updated by :user', array(
			':role' => HTML::anchor(Route::url('backend', array(
				'controller' => 'roles',
				'action' => 'edit',
				'id' => $this->id
			)), $this->name),
		))->write();

		Observer::notify( 'role_after_edit', $this );
	}
	
	public function before_delete()
	{
		Kohana::$log->add(Log::INFO, 'Role :role has been deleted by :user', array(
			':role' => HTML::anchor(Route::url('backend', array(
				'controller' => 'roles',
				'action' => 'edit',
				'id' => $this->id
			)), $this->name),
		))->write();

		Observer::notify( 'role_delete', $this->id );
		
		return TRUE;
	}
	
	public function after_delete($id)
	{
		DB::delete('roles_permissions')
			->where('role_id', '=', $id)
			->execute();
	}
}