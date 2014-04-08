<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Email_Templates extends Controller_System_Backend {
	
	public function before()
	{
		parent::before();

		$this->breadcrumbs
			->add(__('Email'))
			->add(__('Email templates'), Route::get('email_controllers')->uri(array('controller' => 'templates')));
	}
	
	public function action_index()
	{		
		$templates = ORM::factory('email_template');
		$pager = Pagination::factory(array(
			'total_items' => $templates->reset(FALSE)->count_all(),
			'items_per_page' => 20
		));
		
		$this->template->content = View::factory( 'email/templates/index', array(
			'templates' => $templates->with('type')->find_all(),
			'pager' => $pager
		));
	}
	
	public function action_add()
	{
		// check if user have already enter something
		$data = Flash::get( 'post_data', array() );
		
		$email_type_id = (int) $this->request->query('email_type');
		if($email_type_id > 0 AND ORM::factory('email_type', array('id' => $email_type_id))->loaded())
		{
			$data['email_type'] = $email_type_id;
		}

		$template = ORM::factory('email_template')
			->values($data);
		
		// check if trying to save
		if ( Request::current()->method() == Request::POST )
		{
			return $this->_add($template);
		}
		
		WYSIWYG::load_filters();
		
		$this->template->title = __('Add email template');
		$this->breadcrumbs
			->add($this->template->title);

		$this->template->content = View::factory( 'email/templates/edit', array(
			'action' => 'add',
			'template' => $template,
			'types' => ORM::factory('email_type')->select_array()
		) );
	}
	
	private function _add($template)
	{
		$data = $this->request->post();
		$this->auto_render = FALSE;
		
		if( empty($data['status'] ))
		{
			$data['status'] = Model_Email_Template::INACTIVE;
		}
		
		Flash::set( 'post_data', $data );

		$template->values($data);

		try 
		{
			if ( $template->create() )
			{
				Kohana::$log->add(Log::INFO, 'Template :template has been added by :user', array(
					':template' => HTML::anchor(Route::get('email_controllers')->uri(array(
						'controller' => 'templates',
						'action' => 'edit',
						'id' => $template->id
					)), $template->subject),
				))->write();

				Messages::success(__( 'Email template has been saved!' ) );
				Observer::notify( 'email_templates_add', $template );
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
				'controller' => 'templates'
			)));
		}
		else
		{
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'templates',
				'action' => 'edit',
				'id' => $template->id
			)));
		}
	}
	
	public function action_edit( )
	{
		$id = $this->request->param('id');
		
		$template = ORM::factory('email_template', $id);
		
		if( ! $template->loaded() )
		{
			Messages::errors( __('Email template not found!') );
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'templates'
			)));
		}

		// check if trying to save
		if ( Request::current()->method() == Request::POST )
		{
			return $this->_edit( $template );
		}
		
		WYSIWYG::load_filters();

		$this->template->title = __('Edit email template');
		$this->breadcrumbs
			->add($this->template->title);

		$this->template->content = View::factory( 'email/templates/edit', array(
			'action' => 'edit',
			'template' => $template,
			'types' => ORM::factory('email_type')->select_array()
		) );
	}
	
	private function _edit( $template )
	{
		$data = $this->request->post();
		$this->auto_render = FALSE;
		
		if( empty($data['status'] ))
		{
			$data['status'] = Model_Email_Template::INACTIVE;
		}

		$template->values($data);

		try
		{
			if ( $template->update() )
			{
				Kohana::$log->add(Log::INFO, 'Template :template has been updated by :user', array(
					':template' => HTML::anchor(Route::get('email_controllers')->uri(array(
						'controller' => 'templates',
						'action' => 'edit',
						'id' => $template->id
					)), $template->subject),
				))->write();

				Messages::success( __( 'Email template has been saved!' ) );
				Observer::notify( 'email_template_after_edit', $template );
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
				'controller' => 'templates'
			)));
		}
		else
		{
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'templates',
				'action' => 'edit',
				'id' => $template->id
			)));
		}
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$id = (int) $this->request->param('id');
		
		$template = ORM::factory('email_template', $id);
		
		if( ! $template->loaded() )
		{
			Messages::errors( __('Email template not found!') );
			$this->go(Route::get('email_controllers')->uri(array(
				'controller' => 'templates'
			)));
		}
		
		try
		{
			$template->delete();
			Messages::success( __( 'Email template has been deleted!' ) );
		} 
		catch ( Kohana_Exception $e ) 
		{
			Messages::errors( __( 'Something went wrong!' ) );
			$this->go_back();
		}

		$this->go(Route::get('email_controllers')->uri(array(
			'controller' => 'templates'
		)));
	}
}
