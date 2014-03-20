<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Model/Api
 * @author		ButscHSter
 */
class KodiCMS_Model_API_Page extends Model_API {
	
	protected $_table_name = 'pages';
	
	protected $_secured_columns = array(
		'email', 'logins', 'last_login'
	);
	
	public function get_all($uids, $pid, $fields)
	{
		$uids = $this->prepare_param($uids, array('Valid', 'numeric'));
		$fields = $this->prepare_param($fields);
		
		$pages = DB::select('id', 'parent_id', 'slug', 'title')
			->select_array( $this->filtered_fields( $fields ) )
			->from($this->_table_name);
		
		if(!empty($uids))
		{
			$pages->where('id', 'in', $uids);
		}
		
		if(!empty($pid))
		{
			$pages->where('parent_id', '=', (int) $pid);
		}

		$pages = $pages
			->execute()
			->as_array('id');

		if(in_array('parts', $fields))
		{
			$parts = Model::factory('api_page_part')
				->get(array_keys($pages), array_merge($fields, array('page_id')));
			
			if(is_array($parts))
			{
				foreach ($parts as $part)
				{
					if(isset($pages[$part['page_id']]))
					{
						$pages[$part['page_id']]['parts'][$part['id']] = $part;

						unset($pages[$part['page_id']]['parts'][$part['id']]['page_id']);
					}
				}
			}
		}
		
		return $this->create_tree($pages, $fields);
	}
	
	public function create_tree(array $pages, $fields) 
	{
		$indexed = array();

		// first pass - get the array indexed by the primary id
		foreach ($pages as $row) 
		{
			$indexed[$row['id']] = $row;
			$indexed[$row['id']]['level'] = 0;
			$indexed[$row['id']]['pages'] = array();
		}

		$root = NULL;
		foreach ($indexed as $id => $row) 
		{
			$slug = $row['slug'];

			if(isset($indexed[$row['parent_id']]))
			{
				$indexed[$row['parent_id']]['pages'][] =& $indexed[$id];
				$indexed[$id]['slug'] = $indexed[$row['parent_id']]['slug'] . '/' . $slug;
				$indexed[$id]['level'] = $indexed[$row['parent_id']]['level'] + 1;
				
				

				if (!$row['parent_id']) 
				{
					$root = $id;
				}
			}
			
			$indexed[$id]['url'] = URL::frontend($indexed[$id]['slug'], TRUE);
		}
		
		if($root == NULL)
		{
			$indexed = array_shift($indexed);
		}
		else
		{
			$indexed = $indexed[$root];
		}

		return array($indexed);
	}
	
	public function find_by_uri($uri, $fields = array())
	{
		$fields = $this->prepare_param($fields);
		
		$page = Model_Page_Front::find($uri);
		
		if(!$page)
		{
			throw new HTTP_Exception_404('Page :uri not found', array(
				':uri' => $uri ));
		}
		
		// If page needs login, redirect to login
		if ($page->needs_login() == Model_Page::LOGIN_REQUIRED)
		{
			throw new HTTP_Exception_401('You don`t have access to view page :uri. Please login', array(
				':uri' => $uri ));
		}
		
		$fields = array_merge(array(
			'id', 'title', 'url'
		), $fields);
		
		$allowed_fields = array(
			'id', 'title', 'url', 'breadcrumb', 'author', 'author_id',
			'updator', 'updator_id', 'slug', 'keywords', 'description',
			'level', 'tags', 'is_active', 'date', 'breadcrumbs',
			'layout', 'content'
		);
		
		foreach ($fields as $field)
		{
			if( strpos( $field, 'part::') === 0)
			{
				$allowed_fields[] = $field;
			}
		}
		
		$fields = array_intersect($allowed_fields, $fields);
		
		$array = array();
		
		foreach ($fields as $field)
		{
			if($field == 'content')
			{
				$array['content'] = (string) $page->render_layout();
				continue;
			}
			elseif(strpos( $field, 'part::') === 0)
			{
				list($part, $name) = explode('::', $field);
				$array[$part][$name] = $page->content($name);
			}
			else if( method_exists( $page, $field ))
			{
				$array[$field] = $page->$field();
			}
		}
		return array('page' => $array);
	}
}