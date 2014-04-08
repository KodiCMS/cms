<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Archive
 * @author		ButscHSter
 */
class Model_Widget_Archive_Month extends Model_Widget_Archive {

	public function fetch_data()
	{
		$page = $this->get_page();

		$result = DB::select(array(DB::expr( 'DATE_FORMAT('. Database::instance()->quote_column('published_on').', "%Y/%m")' ), 'date'))
			->distinct( TRUE )
			->from('pages')
			->where('parent_id', '=', $page->id)
			->where('status_id', '!=', Model_Page::STATUS_HIDDEN)
			->order_by( 'published_on', 'desc' )
			->execute()
			->as_array(NULL, 'date');

		$data = array();
		foreach($result as $date)
		{
			$data[] = array(
				'href' => URL::site($page->url .'/'. $date, TRUE),
				'title' => strftime('%B %Y', strtotime(strtr($date, '/', '-')))
			);
		}

		return array(
			'links' => $data
		);
	}
}