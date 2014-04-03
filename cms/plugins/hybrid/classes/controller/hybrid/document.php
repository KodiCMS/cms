<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Hybrid_Document extends Controller_System_Datasource
{
	public function before()
	{
		$ds_id = (int) $this->request->query('ds_id');
		
		$this->section($ds_id);
		
		if(
			Acl::check('hybrid'.$ds_id.'.document.edit')
		OR
			Acl::check('hybrid'.$ds_id.'.document.view')
		)
		{
			$this->allowed_actions[] = 'view';
		}
		
		if(
			Acl::check('hybrid'.$ds_id.'.document.edit')
		)
		{
			$this->allowed_actions[] = 'create';
		}

		parent::before();
	}

	public function action_create()
	{
		return $this->action_view();
	}

	public function action_view()
	{
		Assets::package('backbone');
	
		$id = (int) $this->request->query('id');
		$action = $this->request->action();

		if( empty($id) )
		{
			$doc = $this->section()->get_empty_document();
		}
		else
		{
			$doc = $this->section()->get_document($id);
			
			if(!$doc)
			{
				throw new HTTP_Exception_404('Document ID :id not found', 
						array(':id' => $id));
			}
		}

		if($this->request->method() === Request::POST)
		{
			return $this->_save($this->section(), $doc);
		}
		
		WYSIWYG::load_filters();
		
		$post_data = Session::instance()->get_once('post_data');
		if( ! empty($post_data))
		{
			unset($post_data['id']);
			$doc->read_values($post_data);
		}
		$doc->convert_values();

		$this->breadcrumbs
			->add($this->section()->name, Route::get('datasources')->uri(array(
				'directory' => 'datasources',
				'controller' => 'data'
			)) . URL::query(array('ds_id' => $this->section()->id()), FALSE));
		
		if( ! $doc->loaded() )
		{
			$this->template->title = __('New document');
		}
		else
		{
			$this->template->title = $doc->header;
		}
		
		$this->breadcrumbs->add($this->template->title);
		
		$this->template->content = View::factory('datasource/hybrid/document/edit')->set( array(
			'fields' => $this->section()->record()->fields(),
			'ds' => $this->section(),
			'doc' => $doc,
			'action' => $this->request->action()
		));
	}
	
	private function _save($ds, $doc)
	{
		Session::instance()->set('post_data', $this->request->post());

		try
		{
			$doc
				->read_values($this->request->post())
				->read_files($_FILES)
				->validate();
		} 
		catch (Validation_Exception $e)
		{
			Messages::errors($e->errors('validation'));
			$this->go_back();
		}

		if( $doc->loaded() )
		{
			$ds->update_document($doc);
		}
		else
		{
			$doc = $ds->create_document($doc);
		}

		Messages::success('Document saved');
		
		Session::instance()->delete('post_data');
		
		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go(Route::get('datasources')->uri(array(
				'directory' => 'datasources',
				'controller' => 'data'
			)) . URL::query(array('ds_id' => $ds->id()), FALSE));
		}
		else
		{
			$this->go(Route::get('datasources')->uri(array(
				'directory' => 'hybrid',
				'controller' => 'document',
				'action' => 'view'
			)) . URL::query(array('ds_id' => $ds->id(), 'id' => $doc->id), FALSE));
		}
	}
}
