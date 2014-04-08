<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	API
 * @author		ButscHSter
 */
class KodiCMS_Controller_API_Layout extends Controller_System_Api {
	
	public function before() 
	{
		parent::before();
	}

	public function rest_post()
	{
		$layout_name = $this->param('layout_name', NULL, TRUE);
		$layout = new Model_File_Layout( $layout_name );

		if ( ! $layout->is_exists() )
		{
			throw HTTP_API_Exception::factory(API::ERROR_PAGE_NOT_FOUND,
				'Layout not found!');
		}

		$layout->name = $this->param('name', NULL);
		$layout->content = $this->param('content', NULL);

		$status = $layout->save();
		
		if ( ! $status )
		{
			throw HTTP_API_Exception::factory(API::ERROR_UNKNOWN,
				'Something went wrong!');
		}
		else
		{
			if($layout->name != $layout_name) 
			{
				$this->json_redirect('layout/edit/' . $layout->name);
			}

			$this->json['message'] = __( 'Layout has been saved!' );
			Observer::notify( 'layout_after_edit', $layout );
		}
		
		$this->response($layout);
	}
	
	public function rest_put()
	{
		$layout = new Model_File_Layout( $this->param('name', NULL) );
		$layout->content = $this->param('content', NULL);
		
		$status = $layout->save();
		
		if ( ! $status )
		{			
			throw HTTP_API_Exception::factory(API::ERROR_UNKNOWN,
				'Something went wrong!');
		}
		else
		{
			$this->json_redirect('layout/edit/' . $layout->name);
			$this->json['message'] = __( 'Layout has been saved!' );
			Observer::notify( 'layout_after_add', $layout );
		}
		
		$this->response($layout);
	}
	
	public function rest_delete()
	{
		$layout_name = $this->param('name', NULL, TRUE);
		
		$layout = new Model_File_Layout( $layout_name );
		
		if ( ! $layout->is_exists() )
		{
			throw HTTP_API_Exception::factory(API::ERROR_PAGE_NOT_FOUND,
				'Layout not found!',
				array(':name' => $layout_name)
			);
		}

		// find the user to delete
		if ( ! $layout->is_used() )
		{
			if ( $layout->delete() )
			{
				$this->response($layout);
			}
			else
			{
				throw HTTP_API_Exception::factory(API::ERROR_UNKNOWN,
					'Something went wrong!');
			}
		}
		else
		{
			throw HTTP_API_Exception::factory(API::ERROR_PERMISSIONS,
				'Layout is used! It CAN NOT be deleted!');
		}
	}
}