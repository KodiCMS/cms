<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	System Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_System_Backend extends Controller_System_Template
{
	public $auth_required = TRUE;

	/**
	 *
	 * @var Model_Navigation_Page 
	 */
	public $page;

	public function before()
	{
		$page = strtolower(substr(get_class($this), 11));
		
		Model_Navigation::init(Kohana::$config->load('sitemap')->as_array());

		parent::before();
		$navigation = Model_Navigation::get();
		$this->page = Model_Navigation::$current;
		
		if($this->auto_render === TRUE)
		{
			$this->template->set_global(array(
				'page_body_id' => $this->get_path(),
				'page_name' => $page,
				'page' => $this->page
			));
			
			if( $this->request->is_iframe() )
			{
				$navigation = NULL;
				$this->template->footer = NULL;
				$this->template->breadcrumbs = NULL;
				Config::set('site', 'profiling', 'no');
				
				$this->query_params = array('type' => 'iframe');
			}
			else
			{
				$this->template->breadcrumbs = Config::get('site', 'breadcrumbs' ) == Config::YES ? $this->breadcrumbs : NULL;
				$this->template->footer = View::factory('system/blocks/footer');
			}
			
			$this->template->bind_global('navigation', $navigation);
			
			Assets::js('jquery', ADMIN_RESOURCES . 'libs/jquery.min.js');
			// Assets::js('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
	
			Assets::css('dropzone', ADMIN_RESOURCES . 'libs/dropzone/css/basic.css', 'jquery');
			Assets::js('dropzone', ADMIN_RESOURCES . 'libs/dropzone/dropzone.min.js', 'jquery');
			
			Assets::css('jgrowl', ADMIN_RESOURCES . 'libs/jgrowl/jquery.jgrowl.css', 'jquery');
			Assets::js('jgrowl', ADMIN_RESOURCES . 'libs/jgrowl/jquery.jgrowl_minimized.js', 'jquery');

			Assets::js('bootstrap', ADMIN_RESOURCES . 'libs/bootstrap/js/bootstrap.min.js', 'jquery');
			
			Assets::css('fancybox', ADMIN_RESOURCES . 'libs/fancybox/jquery.fancybox.css', 'jquery');
			Assets::js('fancybox', ADMIN_RESOURCES . 'libs/fancybox/jquery.fancybox.pack.js', 'jquery');
			
			Assets::css('select2', ADMIN_RESOURCES . 'libs/select2/select2.css', 'jquery');
			Assets::js('select2', ADMIN_RESOURCES . 'libs/select2/select2.min.js', 'jquery');
			
			Assets::css('datepicker', ADMIN_RESOURCES . 'libs/datepicker/jquery.datetimepicker.css', 'jquery');
			Assets::js('datepicker', ADMIN_RESOURCES . 'libs/datepicker/jquery.datetimepicker.js', 'jquery');
			
			Assets::css('global', ADMIN_RESOURCES . 'css/common.css');
			Assets::js('global', ADMIN_RESOURCES . 'js/backend.js', 'backbone');

			$lang_file = CMSPATH . FileSystem::normalize_path('media/js/i18n/'.I18n::lang().'.js');
			if( file_exists($lang_file))
			{
				Assets::js('i18n', ADMIN_RESOURCES . 'js/i18n/'.I18n::lang().'.js', 'global');
			}
			
			$file = strtolower($this->request->controller());
			if( Kohana::find_file('media', FileSystem::normalize_path('js/controller/' . $file), 'js'))
			{
				Assets::js('controller.' . $file, ADMIN_RESOURCES . 'js/controller/' . $file . '.js', 'global');
			}
			
			Observer::notify('controller_before_' . $this->get_path());
		}
	}
	
//	public function after()
//	{
//		Assets::minify();
//		parent::after();
//	}
}