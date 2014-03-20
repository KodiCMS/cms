<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Categories extends Controller_System_Api {

	public function get_sort()
	{
		$categories = ORM::factory('category')->full_tree();

		$this->response((string) View::factory( 'categories/sort', array(
			'categories' => $categories
		)));
	}
	
	public function post_sort()
	{
		$category_id = $this->param('id', 0, TRUE);
		$to_category_id = $this->param('target_id', 0);
		$type = $this->param('type');

		$target_category = ORM::factory('category', $to_category_id);
		
		$moveable_category = ORM::factory('category', $category_id);
		
		switch ($type)
		{
			case 'next':
				$moveable_category->move_to_prev_sibling($target_category);
				break;
			case 'prev':
				$moveable_category->move_to_next_sibling($target_category);
				break;
			case 'parent':
				$moveable_category->move_to_last_child($target_category);
				break;
			case 'scope':
				$moveable_category->new_scope();
				break;
		}
	}
}