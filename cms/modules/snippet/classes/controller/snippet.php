<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Snippet
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Snippet extends Controller_System_Backend {

	public function before()
	{
		if($this->request->action() == 'edit' AND ACL::check('snippet.view' ))
		{
			$this->allowed_actions[] = 'edit';
		}
		
		parent::before();
		$this->breadcrumbs
			->add(__('Snippets'), Route::get('backend')->uri(array('controller' => 'snippet')));
	}

	public function action_index()
	{
		$this->template->title = __('Snippets');
		$this->template->content = View::factory( 'snippet/index', array(
			'snippets' => Model_File_Snippet::find_all()
		) );
	}

	public function action_add()
	{
		// check if trying to save
		if ( Request::current()->method() == Request::POST )
		{
			return $this->_add();
		}
		
		Assets::package('ace');
		
		$this->template->title = __('Add snippet');
		$this->breadcrumbs
			->add($this->template->title);

		// check if user have already enter something
		$snippet = Flash::get( 'post_data' );

		if ( empty( $snippet ) )
		{
			$snippet = new Model_File_Snippet;
		}

		$this->template->content = View::factory( 'snippet/edit', array(
			'action' => 'add',
			'filters' => WYSIWYG::findAll(),
			'snippet' => $snippet
		) );
	}

	private function _add()
	{
		$data = $this->request->post();
		Flash::set( 'post_data', (object) $data );

		$snippet = new Model_File_Snippet( $data['name'] );
		$snippet->content = $data['content'];
		
		try
		{
			$status = $snippet->save();
		}
		catch(Validation_Exception $e)
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}

		Kohana::$log->add(Log::INFO, 'Snippet :name has been added by :user', array(
			':name' => $snippet->name
		))->write();

		Messages::success( __( 'Snippet has been saved!' ) );
		Observer::notify( 'snippet_after_add', array( $snippet ) );
		
		Session::instance()->delete('post_data');

		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go();
		}
		else
		{
			$this->go(array('action' => 'edit', 'id' => $snippet->name));
		}
	}

	public function action_edit( )
	{
		$snippet_name = $this->request->param('id');
		$snippet = new Model_File_Snippet( $snippet_name );

		if ( ! $snippet->is_exists() )
		{
			if(($found_file = $snippet->find_file()) !== FALSE)
			{
				$snippet = new Model_File_Snippet( $found_file );
			}
			else
			{
				Messages::errors( __( 'Snippet not found!' ) );
				$this->go();
			}
		}

		$this->template->title = __('Edit snippet');
		$this->breadcrumbs
			->add($snippet_name);

		// check if trying to save
		if ( Request::current()->method() == Request::POST AND ACL::check('snippet.edit'))
		{
			return $this->_edit( $snippet_name );
		}

		Assets::package('ace');
		
		$this->template->content = View::factory( 'snippet/edit', array(
			'action' => 'edit',
			'filters' => WYSIWYG::findAll(),
			'snippet' => $snippet
		) );
	}

	private function _edit( $snippet_name )
	{
		$data = $this->request->post();

		$snippet = new Model_File_Snippet( $snippet_name );
		$snippet->name = $data['name'];
		$snippet->content = $data['content'];
		
		try
		{
			$status = $snippet->save();
		}
		catch(Validation_Exception $e)
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}

		Kohana::$log->add(Log::INFO, 'Snippet :name has been changed by :user', array(
			':name' => $snippet->name
		))->write();

		Messages::success( __( 'Snippet has been saved!' ) );
		Observer::notify( 'snippet_after_edit', array( $snippet ) );

		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go();
		}
		else
		{
			$this->go(array('action' => 'edit', 'id' => $snippet->name));
		}
	}

	public function action_delete( )
	{
		$this->auto_render = FALSE;
		$snippet_name = $this->request->param('id');

		$snippet = new Model_File_Snippet( $snippet_name );

		// find the user to delete
		if ( $snippet->is_exists() )
		{
			if ( $snippet->delete() )
			{
				Kohana::$log->add(Log::INFO, 'Snippet :name has been deleted by :user', array(
					':name' => $snippet_name
				))->write();
				
				Messages::success( __( 'Snippet has been deleted!' ) );
				Observer::notify( 'snippet_after_delete', array( $snippet_name ) );
			}
			else
			{
				Messages::errors( __( 'Something went wrong!' ) );
			}
		}
		else
		{
			Messages::errors( __( 'Snippet not found!' ) );
		}

		$this->go();
	}
}