<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Archive
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Archive extends Controller_System_Backend
{
	public function action_index() 
	{
		$page_id = $this->request->param('id');
		$page = ORM::factory('page', (int) $page_id);

		if ( ! $page->loaded() )
		{
			Flash::set('error', __('Page not found!'));
			throw new HTTP_Exception_404('Page not found');
		}
		
		$this->template->title = $page->title;
		$this->breadcrumbs
			->add(__('Pages'), Route::get('backend')->uri(array('controller' => 'page')))
			->add($this->template->title);
		
		$pages = ORM::factory('page')->where('parent_id', '=', (int) $page_id);

		$pager = Pagination::factory(array(
			'total_items' => $pages->reset(FALSE)->count_all()
		));
		
		$this->template->content = View::factory('archive/index', array(
			'items' => $pages
				->order_by('created_on', 'desc')
				->limit($pager->items_per_page)
				->offset($pager->offset)
				->find_all(),
			'page'	=> $page,
			'pager' => $pager
		));
	}
}