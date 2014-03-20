<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Categories extends Controller_System_Backend {

	public function before()
	{
		parent::before();

		$this->breadcrumbs
			->add(__('Categories'), Route::url('backend', array('controller' => 'categories')));
	}

	public function action_index()
	{
		$this->template->title = __('Categories');

		Assets::js('nestable', ADMIN_RESOURCES . 'libs/nestable/jquery.nestable.js', 'jquery');
		
		$this->template->content = View::factory( 'categories/index', array(
			'categories' => ORM::factory('category')->full_tree()
		) );
	}
	
	public function action_add()
	{
		$parent_id = (int) $this->request->param('id');
		$values = Flash::get('category::add::data', array());
		$category = ORM::factory('category')->values($values);

		// check if trying to save
		if ( $this->request->method() == Request::POST )
		{
			return $this->_add( $category, $parent_id );
		}
		
		$this->set_title(__('Add category'));

		$this->template->content = View::factory('categories/edit', array(
			'action' => 'add',
			'parent_id' => $parent_id,
			'category' => $category
		) );
	}
	
	private function _add( ORM $category, $parent_id )
	{
		$category_data = $this->request->post('category');

		// Сохраняем полученые данные в сесиию
		Flash::set( 'category::add::data', $category_data );

		try
		{
			$category->values($category_data);
			
			if( empty($parent_id) )
			{
				$category->new_scope();
			}
			else
			{
				$parent_category = ORM::factory('category', $parent_id);
				$category = $category->insert_as_last_child($parent_category);
			}

			Messages::success( __( 'Category has been saved!' ) );
			Flash::clear( 'category::add::data' );
		} 
		catch (ORM_Validation_Exception $e) 
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}
		catch ( Kohana_Exception $e)
		{
			Messages::errors( __( 'Something went wrong!' ) );
			$this->go_back();
		}
		
		// save and quit or save and continue editing ?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go();
		}
		else
		{
			$this->go( array(
				'action' => 'edit',
				'id' => $category->id
			));
		}
	}
	
	public function action_edit()
	{
		$category_id = (int) $this->request->param('id');

		$category = ORM::factory('category', $category_id);

		if ( ! $category->loaded() )
		{
			Messages::errors( __( 'Category not found!' ) );
			$this->go();
		}

		// check if trying to save
		if ( $this->request->method() == Request::POST )
		{
			return $this->_edit( $category );
		}
		
		$this->set_title($category->name);

		$this->template->content = View::factory( 'categories/edit', array(
			'action' => 'edit',
			'category' => $category
		) );
	}
	
	private function _edit( ORM $category )
	{
		$category_data = $this->request->post('category');

		try
		{
			$category = $category->values($category_data);
			$category->update_path();
			$category->update();

			Messages::success( __( 'Category has been saved!' ) );
		} 
		catch (ORM_Validation_Exception $e) 
		{
			Messages::errors( $e->errors('validation') );
			$this->go_back();
		}
		catch ( Kohana_Exception $e)
		{
			Messages::errors( __( 'Something went wrong!' ) );
			$this->go_back();
		}

		// save and quit or save and continue editing ?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go();
		}
		else
		{
			$this->go_back();
		}
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;
		$category_id = (int) $this->request->param('id');
		
		$category = ORM::factory('category', $category_id );
		
		if( ! $category->loaded() )
		{
			Messages::errors( __( 'Category not found!' ) );
			$this->go_back();
		}
		
		try
		{
			$category->delete();
			Messages::success( __( 'Category has been deleted!' ) );
	
		} 
		catch ( Kohana_Exception $e ) 
		{
			Messages::errors( __( 'Something went wrong!' ) );
			$this->go_back();
		}

		$this->go();
	}
}