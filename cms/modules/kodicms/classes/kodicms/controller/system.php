<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_System extends Controller_System_Backend {

	public function before()
	{
		parent::before();

		$this->breadcrumbs
			->add(__('System'), Route::get('backend')->uri(array('controller' => 'system', 'action' => 'information')));
	}
	
	public function action_index()
	{
		return $this->action_information();
	}
	
	public function action_information()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			// Clear out the cache to prevent errors. This typically happens on Windows/FastCGI.
			clearstatcache();
		}
		else
		{
			// Clearing the realpath() cache is only possible PHP 5.3+
			clearstatcache(TRUE);
		}
		
		$this->template->content = View::factory( 'system/information', array(
			'failed' => FALSE
		));
		
		$this->template->title = __('Information');
		$this->breadcrumbs
			->add($this->template->title);
	}
	
	public function action_settings()
	{
		$this->template->title = __('Settings');
		
		$site_pages = array();

		foreach ( Model_Navigation::get()->sections() as $section )
		{
			foreach ( $section->get_pages() as $item )
			{
				$url = trim(str_replace(ADMIN_DIR_NAME, '', $item->url()), '/');
				if(empty($url))
				{
					$url = Config::get('site', 'default_tab');
				}

				$site_pages[$section->name()][$url] = $item->name();
			}
		}

		$this->template->content = View::factory( 'system/settings', array(
			'filters' => Arr::merge(array('--none--'), WYSIWYG::findAll()),
			'dates' => Date::formats(),
			'site_pages' => $site_pages,
			'default_status_id' => array(
				Model_Page::STATUS_DRAFT => __( 'Draft' ),
				Model_Page::STATUS_PUBLISHED => __( 'Published' )
			)
		) );		
	}
	
	public function action_phpinfo()
	{
		$this->auto_render = FALSE;
		phpinfo();
	}
}