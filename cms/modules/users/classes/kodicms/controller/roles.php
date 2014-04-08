<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Users
 * @category	Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_Roles extends Controller_System_Backend {
	
	public function before()
	{
		parent::before();
		$this->breadcrumbs
			->add(__('Users'), Route::get('backend')->uri(array('controller' => 'users')))
			->add(__('Roles'), Route::get('backend')->uri(array('controller' => 'roles')));
	}

	public function action_index()
	{
		$this->set_title(__('Roles'), FALSE);
		
		$roles = ORM::factory('role');
		$pager = $roles->add_pager();
		
		$this->template->content = View::factory( 'roles/index', array(
			'roles' => $roles->find_all(),
			'pager' => $pager
		) );
	}
	
	public function action_add()
	{
		$data = Flash::get( 'roles::add::data', array() );

		$role = ORM::factory('role')->values($data);

		if ( Request::current()->method() == Request::POST )
		{
			return $this->_add($role);
		}
		
		$this->set_title(__('Add role'));

		$this->template->content = View::factory('roles/edit', array(
			'action' => 'add',
			'role' => $role
		) );
	}
	
	private function _add( ORM $role)
	{
		$data = $this->request->post('role');
		$this->auto_render = FALSE;
		
		Flash::set( 'roles::add::data', $data );
		try 
		{
			
			$role = $role->values($data)->create();
	
			if(Acl::check( 'roles.change_permissions') )
			{
				$role->set_permissions($data['permissions']);
			}

			Messages::success(__( 'Role has been added!' ) );
		}
		catch (ORM_Validation_Exception $e)
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}
		
		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go();
		}
		else
		{
			$this->go( array(
				'action' => 'edit',
				'id' => $role->id
			));
		}
	}

	public function action_edit( )
	{
		$id = (int) $this->request->param('id');
		
		$role = ORM::factory('role', $id);
		
		if( ! $role->loaded() )
		{
			Messages::errors( __('Role not found!') );
			$this->go();
		}

		if ( Request::current()->method() == Request::POST )
		{
			return $this->_edit( $role );
		}

		$this->set_title(__('Edit role'));

		$this->template->content = View::factory( 'roles/edit', array(
			'action' => 'edit',
			'role' => $role
		) );
	}

	private function _edit( ORM $role )
	{
		$data = $this->request->post('role');
		$this->auto_render = FALSE;

		try
		{
			$role = $role->values($data)->update();

			if ( Acl::check( 'roles.change_permissions') AND ! empty($data['permissions']))
			{
				$role->set_permissions($data['permissions']);
			}

			Messages::success( __( 'Role has been saved!' ) );
		}
		catch (ORM_Validation_Exception $e)
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}

		if ( $this->request->post('commit') !== NULL )
		{
			$this->go();
		}
		else
		{
			$this->go( array(
				'action' => 'edit',
				'id' => $role->id
			));
		}
	}

	public function action_delete( )
	{
		$this->auto_render = FALSE;
		$id = $this->request->param('id');

		if ( $id < 2 )
		{
			Messages::success( __( 'Action disabled!' ) );
			$this->go();
		}

		$role = ORM::factory('role', $id);

		if( ! $role->loaded() )
		{
			Messages::errors( __('Role not found!') );
			$this->go();
		}

		try
		{
			$role->delete();
			Messages::success( __( 'Role has been deleted!' ) );
		} 
		catch (Kohana_Exception $e)
		{
			Messages::errors( __( 'Something went wrong!' ) );
		}

		$this->go();
	}
}