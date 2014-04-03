<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Snippet
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Snippet extends Controller_System_Api {
	
	public function before() 
	{
		parent::before();
	}

	public function rest_post()
	{
		$snippet_name = $this->param('snippet_name', NULL, TRUE);
		$snippet = new Model_File_Snippet( $snippet_name );

		if ( ! $snippet->is_exists() )
		{
			throw HTTP_API_Exception::factory(API::ERROR_PAGE_NOT_FOUND,
				'Snippet :name not found!',
				array(':name' => $snippet_name)
			);
		}

		$snippet->name = $this->param('name', NULL);
		$snippet->content = $this->param('content', NULL);

		try
		{
			$status = $snippet->save();
		}
		catch(Validation_Exception $e)
		{
			throw new API_Validation_Exception($e->errors('validation'));
		}
		
		if ( ! $status )
		{
			throw HTTP_API_Exception::factory(API::ERROR_UNKNOWN,
				'Snippet :name has not been saved!',
				array(':name' => $snippet_name)
			);
		}
		else
		{
			if($snippet->name != $snippet_name) 
			{
				$this->json_redirect('snippet/edit/' . $snippet->name);
			}

			$this->json['message'] = __( 'Snippet :name has been saved!', array( ':name' => $snippet->name ) );
			Observer::notify( 'snippet_after_edit', array( $snippet ) );
		}
		
		$this->response(array(
			'name' => $snippet->name,
			'content' => $snippet->content
		));
	}
	
	public function rest_put()
	{
		$snippet = new Model_File_Snippet( $this->param('name', NULL) );
		$snippet->content = $this->param('content', NULL);
		
		try
		{
			$status = $snippet->save();
		}
		catch(Validation_Exception $e)
		{
			throw new API_Validation_Exception($e->errors('validation'));
		}
		
		if ( ! $status )
		{			
			throw HTTP_API_Exception::factory(API::ERROR_UNKNOWN,
				'Snippet :name has not been added!',
				array(':name' => $snippet->name)
			);
		}
		else
		{
			$this->json_redirect('snippet/edit/' . $snippet->name);
			$this->json['message'] = __( 'Snippet :name has been added!', array( ':name' => $snippet->name ) );
			Observer::notify( 'snippet_after_add', array( $snippet ) );
		}
		
		$this->response(array(
			'name' => $snippet->name,
			'content' => $snippet->content
		));
	}
	
	public function rest_delete()
	{
		$snippet_name = $this->param('name', NULL, TRUE);
		
		$snippet = new Model_File_Snippet( $snippet_name );
		
		if ( ! $snippet->is_exists() )
		{
			throw HTTP_API_Exception::factory(API::ERROR_PAGE_NOT_FOUND,
				'Snippet :name not found!',
				array(':name' => $snippet_name)
			);
		}

		if ( $snippet->delete() )
		{
			$this->response($snippet);
		}
		else
		{
			throw HTTP_API_Exception::factory(API::ERROR_UNKNOWN,
				'Snippet :name has not been deleted!',
				array(':name' => $snippet_name)
			);
		}
	}
}