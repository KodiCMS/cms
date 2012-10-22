<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Hybrid_Document extends Controller_System_Datasource
{
	
	public function action_create()
	{
		return $this->action_view();
	}

	public function action_view()
	{
		$id = (int) $this->request->query('id');
		$ds_id = (int) $this->request->query('ds_id');
		
		$ds = Datasource_Manager::load($ds_id);

		if(!$id)
		{
			$doc = $ds->get_empty_document();
		}
		else
		{
			$doc = $ds->get_document($id);
			
			if(!$doc)
			{
				throw new HTTP_Exception_404('Document ID :id not found', array(':id' => $id));
			}
		}
		
		if($this->request->method() === Request::POST)
		{
			return $this->_save($ds, $doc);
		}
		
		$post_data = Session::instance()->get_once('post_data');
		$doc->read_values($post_data);

		$this->breadcrumbs
			->add($ds->name, 'hybrid/section/edit/' . $ds->ds_id)
			->add(__(':action document', array(':action' => ucfirst($this->request->action()))));
		
		$this->template->content = View::factory('datasource/data/hybrid/document/edit', array(
			'record' => $ds->get_record(),
			'ds' => $ds,
			'doc' => $doc
		));
	}
	
	private function _save($ds, $doc)
	{
		Session::instance()->set('post_data', $this->request->post());

		if(($errors = $doc->validate($this->request->post())) !== TRUE)
		{
			Messages::errors($errors);
			$this->go_back();
		}

		$doc->read_values($this->request->post());
		$doc->read_files($_FILES);

		if($doc->id)
		{
			$ds->update_document($doc);
		}
		else
		{
			$doc = $ds->create_document($doc);
		}

		Messages::success('Document saved');
		
		// save and quit or save and continue editing?
		if ( $this->request->post('commit') )
		{
			$this->go( URL::site('datasources/data' . URL::query(array('ds_id' => $ds->ds_id), FALSE)));
		}
		else
		{
			$this->go('hybrid/document/view' . URL::query(array('ds_id' => $ds->ds_id, 'id' => $doc->id), FALSE));
		}
	}
	
	public function action_remove()
	{
		$this->auto_render = FALSE;

		$doc_ids = $this->request->post('doc');
		
		if(empty($doc_ids))
		{
			Messages::errors(__('Error'));
			return;
		}
		
		$dsf = new Datasource_Hybrid_Factory;
		$dsf->remove_documents($doc_ids);
	}
	
	public function action_move()
	{
		$this->auto_render = FALSE;

		$doc_ids = $this->request->post('doc');
		$ds_id = (int) $this->request->post('ds_id');
		$dest_ds_id = (int) $this->request->post('dest_ds_id');
		
		$dsf = new Datasource_Hybrid_Factory;
	
		$dsf->cast_documents($ids, $ds_id, $dest_ds_id);
	}
	
	public function action_publish()
	{
		$this->auto_render = FALSE;

		$doc_ids = $this->request->post('doc');
		
		if(empty($doc_ids))
		{
			Messages::errors(__('Error'));
			return;
		}
		
		$dsf = new Datasource_Hybrid_Factory;
		$dsf->publish_documents($doc_ids);
	}
	
	public function action_unpublish()
	{
		$this->auto_render = FALSE;

		$doc_ids = $this->request->post('doc');
		
		if(empty($doc_ids))
		{
			Messages::errors(__('Error'));
			return;
		}
		
		$dsf = new Datasource_Hybrid_Factory;
		$dsf->unpublish_documents($doc_ids);
	}
}