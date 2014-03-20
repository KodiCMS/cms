<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_Page extends Controller_System_Backend {
	
	public $allowed_actions = array(
		'children'
	);

	public function before()
	{
		parent::before();

		$this->breadcrumbs
			->add(__('Pages'), Route::url('backend', array('controller' => 'page')));
	}

	public function action_index()
	{
		$this->template->title = __('Pages');

		Assets::js('nestable', ADMIN_RESOURCES . 'libs/nestable/jquery.nestable.js', 'jquery');
		
		$this->template->content = View::factory( 'page/index', array(
			'page' => ORM::factory('page', 1),
			'content_children' => $this->children( 1, 0, TRUE )
		) );
	}

	public function action_add( )
	{
		WYSIWYG::load_filters();
		Assets::package('backbone');

		$parent_id = (int) $this->request->param('id', 1);
		
		$values = Flash::get('page::add::data', array());
		$page = ORM::factory('page')->values($values);
		
		$page->parent_id = $parent_id;
		
		// Устанавливаем статус по умолчанию
		$page->status_id = Config::get('site', 'default_status_id' );
		$page->needs_login = Model_Page::LOGIN_INHERIT;

		$page->published_on = date('Y-m-d H:i:s');

		// check if trying to save
		if ( $this->request->method() == Request::POST )
		{
			return $this->_add( $page );
		}
		
		$this->set_title(__('Add page'));

		$this->template->content = View::factory('page/edit', array(
			'action' => 'add',
			'parent_id' => $parent_id,
			'page' => $page,
			'permissions' => ORM::factory('role')->find_all()->as_array('id', 'name'),
			'page_permissions' => $page->get_permissions()
		) );
	}

	private function _add( ORM $page )
	{
		$page_data = $this->request->post('page');

		// Сохраняем полученые данные в сесиию
		Flash::set( 'page::add::data', $page_data );

		// Создаем новую страницу
		try
		{
			$page = $page->values($page_data)->create();
			
			// Если есть права на управление ролями
			if ( ACL::check( 'page.permissions' ) )
			{
				$page->save_permissions( $this->request->post('page_permissions') );
			}

			Messages::success( __( 'Page has been saved!' ) );
			
			Flash::clear( 'page::add::data' );
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
				'id' => $page->id
			));
		}
	}

	public function action_edit( )
	{
		WYSIWYG::load_filters();
		Assets::package('backbone');

		$page_id = (int) $this->request->param('id');

		$page = ORM::factory('page', $page_id);

		if ( ! $page->loaded() )
		{
			Messages::errors( __( 'Page not found!' ) );
			$this->go();
		}

		// Проверка пользователя на доступ к редактированию текущей страницы
		if ( ! AuthUser::hasPermission( $page->get_permissions() ) )
		{
			Messages::errors( __( 'You do not have permission to access the requested page!' ) );
			$this->go();
		}

		// check if trying to save
		if ( $this->request->method() == Request::POST )
		{
			return $this->_edit( $page );
		}
		
		$this->set_title($page->title);

		$this->template->content = View::factory( 'page/edit', array(
			'action' => 'edit',
			'page' => $page,
			'permissions' => ORM::factory('role')->find_all()->as_array('id', 'name'),
			'page_permissions' => $page->get_permissions()
		) );
	}

	private function _edit( ORM $page )
	{
		$page_data = $this->request->post('page');

		try
		{
			$page = $page->values($page_data)->update();
			
			if ( ACL::check( 'page.permissions' ) )
			{
				$page->save_permissions( $this->request->post('page_permissions') );
			}
			
			Messages::success( __( 'Page has been saved!' ) );
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

	public function action_delete( )
	{
		$this->auto_render = FALSE;
		$page_id = (int) $this->request->param('id');
		
		if($page_id == 1)
		{
			Messages::errors( __( 'Root page can not be removed.' ) );
			$this->go_back();
		}
		
		$page = ORM::factory('page', $page_id );
		
		if( ! $page->loaded() )
		{
			Messages::errors( __( 'Page not found!' ) );
			$this->go_back();
		}

		// check for permission to delete this page
		if ( ! AuthUser::hasPermission( $page->get_permissions() ) )
		{
			Kohana::$log->add(Log::ALERT, 'Trying to delete page :id by :user', array(
				':id' => $page_id
			))->write();

			Messages::errors( __( 'You do not have permission.' ) );
			$this->go_back();
		}
		
		try
		{
			$page->delete();
			Messages::success( __( 'Page has been deleted!' ) );
	
		} 
		catch ( Kohana_Exception $e ) 
		{
			Messages::errors( __( 'Something went wrong!' ) );
			$this->go_back();
		}

		$this->go();
	}
	
	public function children( $parent_id, $level, $return = FALSE )
	{
		$expanded_rows = Arr::get($_COOKIE, 'expanded_rows');
		
		$expanded_rows = $expanded_rows == NULL ? array() : explode(',', $expanded_rows);

		$page = ORM::factory('page', $parent_id);

		if( ! $page->loaded() ) return;
		
		$pages = ORM::factory('page')->children_of($parent_id);
		$behavior = Behavior::get( $page->behavior_id );
		
		if( ! empty($behavior['limit']))
		{
			$pages->limit((int) $behavior['limit']);
		}

		$childrens = $pages->find_all()->as_array('id');

		foreach ( $childrens as $index => $child )
		{
			$childrens[$index]->has_children = $child->has_children();
			
			$child_behavior = Behavior::get( $child->behavior_id );
			
			if( ! empty($child_behavior['link']))
			{
				$childrens[$index]->has_children = TRUE;
			}
			
			$childrens[$index]->is_expanded = in_array( $child->id, $expanded_rows );
			
			if ( $childrens[$index]->is_expanded === TRUE )
			{
				$childrens[$index]->children_rows = $this->children( $child->id, $level + 1, true );
			}
		}
		
		if( ! empty($behavior['limit']) )
		{
			$childrens[] = '...';
		}
		
		if( ! empty($behavior['link']))
		{
			$link = strtr($behavior['link'], array(':id' => $parent_id));
			$childrens[] = __(':icon :link', array(
				':icon' => UI::icon('book'),
				':link' => HTML::anchor( URL::backend($link), __(ucfirst($page->behavior_id)))
			));
		}

		$content = View::factory( 'page/children', array(
			'childrens'			=> $childrens,
			'level'				=> $level + 1,
			'expanded_rows'		=> $expanded_rows
		) );

		if ( $return === TRUE )
		{
			return $content;
		}

		echo $content;
	}

	public function action_children( )
	{
		$this->auto_render = FALSE;

		$parent_id = $this->request->query('parent_id');
		$level = $this->request->query('level');
		
		return $this->children($parent_id, $level);
	}
}