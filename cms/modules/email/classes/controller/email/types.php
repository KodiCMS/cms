<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Email_Types extends Controller_System_Backend {
	
	public function before()
	{
		parent::before();

		$this->breadcrumbs
			->add(__('Email'))
			->add(__('Email types'), Route::get('email_controllers')->uri(array('controller' => 'types')));
	}
	
	public function action_index()
	{
		$types = ORM::factory('email_type');
		$pager = Pagination::factory(array(
			'total_items' => $types->reset(FALSE)->count_all(),
			'items_per_page' => 20
		));
		
		$this->template->content = View::factory( 'email/types/index', array(
			'types' => $types->find_all(),
			'pager' => $pager
		));
	}
	
	public function action_add()
	{
		// check if user have already enter something
		$data = Flash::get( 'post_data', array() );
		$type = ORM::factory('email_type');
		
		// check if trying to save
		if ( Request::current()->method() == Request::POST )
		{
			return $this->_add($type);
		}
		
		$this->template->title = __('Add email type');
		$this->breadcrumbs
			->add($this->template->title);

		$this->template->content = View::factory( 'email/types/edit', array(
			'action' => 'add',
			'type' => $type,
			'templates' => $type->templates->find_all()
		) );
	}
	
	private function _add($type)
	{
		$data = $this->request->post();
		$this->auto_render = FALSE;
		
		Flash::set( 'post_data', $data );
		
		$type->values($data, array('code', 'data', 'name'));

		try 
		{
			if ( $type->create() )
			{
				Kohana::$log->add(Log::INFO, 'Email type :type has been added by :user', array(
					':template' => HTML::anchor(Route::get('email_controllers')->uri(array(
						'controller' => 'types',
						'action' => 'edit',
						'id' => $type->id
					)), $type->name),
				))->write();

				Messages::success(__( 'Email type has been saved!' ) );
				Observer::notify( 'email_type_add', $type );
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}
		
		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'types'
			)));
		}
		else
		{
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'types',
				'action' => 'edit',
				'id' => $type->id
			)));
		}
	}
	
	public function action_edit( )
	{
		$id = $this->request->param('id');
		
		$type = ORM::factory('email_type', $id);
		
		if( ! $type->loaded() )
		{
			Messages::errors( __('Email type not found!') );
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'types'
			)));
		}

		// check if trying to save
		if ( Request::current()->method() == Request::POST )
		{
			return $this->_edit( $type );
		}

		$this->template->title = __('Edit email type');
		$this->breadcrumbs
			->add($this->template->title);

		$this->template->content = View::factory( 'email/types/edit', array(
			'action' => 'edit',
			'type' => $type,
			'templates' => $type->templates->find_all()
		) );
	}
	
	private function _edit( $type )
	{
		$data = $this->request->post();
		$this->auto_render = FALSE;
		
		if(!empty($data['code'])) unset($data['code']);

		$type->values($data);

		try
		{
			if ( $type->update() )
			{
				Kohana::$log->add(Log::INFO, 'Email type :type has been updated by :user', array(
					':template' => HTML::anchor(Route::get('email_controllers')->uri(array(
						'controller' => 'types',
						'action' => 'edit',
						'id' => $type->id
					)), $type->name),
				))->write();

				Messages::success(__( 'Email type has been saved!' ) );
				Observer::notify( 'email_type_edit', $type );
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}

		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'types'
			)));
		}
		else
		{
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'types',
				'action' => 'edit',
				'id' => $type->id
			)));
		}
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$id = (int) $this->request->param('id');
		
		$type = ORM::factory('email_type', $id);
		
		if( ! $type->loaded() )
		{
			Messages::errors( __('Email type not found!') );
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'types'
			)));
		}
		
		try
		{
			$type->delete();
			Messages::success( __( 'Email type has been deleted!' ) );
		} 
		catch ( Kohana_Exception $e ) 
		{
			Messages::errors( __( 'Something went wrong!' ) );
			$this->go_back();
		}

		$this->go(Route::get('email_controllers')->uri(array(
			'controller' => 'types'
		)));
	}
}
