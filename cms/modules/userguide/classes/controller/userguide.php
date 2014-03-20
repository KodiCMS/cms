<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kohana user guide and api browser.
 *
 * @package    Kohana/Userguide
 * @category   Controllers
 * @author     Kohana Team
 */
class Controller_Userguide extends Controller_System_Backend {

	public $template = 'userguide/template';
	
	public $auth_required = FALSE;

	// Routes
	protected $media;
	protected $api;
	protected $guide;

	public function before()
	{
		if ($this->request->action() === 'media')
		{
			// Do not template media files
			$this->auto_render = FALSE;
		}
		else
		{
			// Grab the necessary routes
			$this->media = Route::get('docs/media');
			$this->guide = Route::get('docs/guide');

			if (defined('MARKDOWN_PARSER_CLASS'))
			{
				throw new Kohana_Exception('Markdown parser already registered. Live documentation will not work in your environment.');
			}

			// Use customized Markdown parser
			define('MARKDOWN_PARSER_CLASS', 'Kodoc_Markdown');

			if ( ! class_exists('Markdown', FALSE))
			{
				// Load Markdown support
				require Kohana::find_file('vendor', 'markdown/markdown');
			}

			// Set the base URL for links and images
			Kodoc_Markdown::$base_url  = URL::site($this->guide->uri()).'/';
			Kodoc_Markdown::$image_url = URL::site($this->media->uri()).'/';
		}

		parent::before();
		
		$this->breadcrumbs = Breadcrumbs::factory()
			->add(__('Home'), Config::get('site', 'default_tab'));
	}
	
	// List all modules that have userguides
	public function index()
	{
		$this->template->title = __('User guide');
		$this->template->content = View::factory('userguide/index', array('modules' => $this->_modules()));
		$this->template->menu = View::factory('userguide/menu', array('modules' => $this->_modules()));
	
		
		$this->breadcrumbs
			->add($this->template->title);
	}
	
	// Display an error if a page isn't found
	public function error($message)
	{
		throw new HTTP_Exception_404($message);
	}

	public function action_docs()
	{
		$module = $this->request->param('module');
		$page = $this->request->param('page');

		// Trim trailing slash
		$page = rtrim($page, '/');

		// If no module provided in the url, show the user guide index page, which lists the modules.
		if ( ! $module)
		{
			return $this->index();
		}
		
		// If this module's userguide pages are disabled, show the error page
		if ( ! Kohana::$config->load('userguide.modules.'.$module.'.enabled'))
		{
			return $this->error(__('That module doesn\'t exist, or has userguide pages disabled.'));
		}
		
		// Prevent "guide/module" and "guide/module/index" from having duplicate content
		if ( $page == 'index')
		{
			return $this->error(__('Userguide page not found'));
		}
		
		// If a module is set, but no page was provided in the url, show the index page
		if ( ! $page )
		{
			$page = 'index';
		}

		// Find the markdown file for this page
		$file = $this->file($module.'/'.$page);

		// If it's not found, show the error page
		if ( ! $file)
		{
			return $this->error(__('Userguide page not found'));
		}
		
		// Namespace the markdown parser
		Kodoc_Markdown::$base_url  = URL::site($this->guide->uri()).'/'.$module.'/';
		Kodoc_Markdown::$image_url = URL::site($this->media->uri()).'/'.$module.'/';

		// Set the page title
		$this->template->title = $page == 'index' ? Kohana::$config->load('userguide.modules.'.$module.'.name') : $this->title($page);

		// Parse the page contents into the template
		Kodoc_Markdown::$show_toc = true;
		$this->template->content = Markdown(file_get_contents($file));
		Kodoc_Markdown::$show_toc = false;

		// Attach this module's menu to the template
		$this->template->menu = Markdown($this->_get_all_menu_markdown());
		
		// Bind the copyright
		$this->template->copyright = Kohana::$config->load('userguide.modules.'.$module.'.copyright');

		// Add the breadcrumb trail
		$this->breadcrumbs
			->add(__('User Guide'), $this->guide->uri())
			->add(Kohana::$config->load('userguide.modules.'.$module.'.name'), $this->guide->uri(array('module' => $module)));
	}

	public function action_api()
	{
		// Enable the missing class autoloader.  If a class cannot be found a
		// fake class will be created that extends Kodoc_Missing
		spl_autoload_register(array('Kodoc_Missing', 'create_class'));
		
		set_time_limit(0);

		// Get the class from the request
		$class = $this->request->param('class');
		
		
		// Add the breadcrumb
		$this->breadcrumbs
			->add(__('User Guide'), $this->guide->uri(array('page' => NULL)))
			->add(__('API Browser'), $this->request->route()->uri());

		// If no class was passed to the url, display the API index page
		if ( ! $class)
		{
			$this->template->title = __('Table of Contents');

			$this->template->content = View::factory('userguide/api/toc')
				->set('classes', Kodoc::class_methods())
				->set('route', $this->request->route());
		}
		else
		{
			// Create the Kodoc_Class version of this class.
			$_class = Kodoc_Class::factory($class);
			
			// If the class requested and the actual class name are different
			// (different case, orm vs ORM, auth vs Auth) redirect
			if ($_class->class->name != $class)
			{
				$this->request->redirect($this->request->route()->uri(array('class'=>$_class->class->name)));
			}

			// If this classes immediate parent is Kodoc_Missing, then it should 404
			if ($_class->class->getParentClass() AND $_class->class->getParentClass()->name == 'Kodoc_Missing')
				return $this->error('That class was not found. Check your url and make sure that the module with that class is enabled.');

			// If this classes package has been disabled via the config, 404
			if ( ! Kodoc::show_class($_class))
				return $this->error('That class is in package that is hidden.  Check the <code>api_packages</code> config setting.');
		
			// Everything is fine, display the class.
			$this->template->title = $class;

			$this->template->content = View::factory('userguide/api/class')
				->set('doc', Kodoc::factory($class))
				->set('route', $this->request->route());
		}
		
		$this->breadcrumbs->add($this->template->title);

		// Attach the menu to the template
		$this->template->menu = Kodoc::menu();

		// Get the docs URI
		$guide = Route::get('docs/guide');
	}

	public function action_media()
	{
		// Get the file path from the request
		$file = $this->request->param('file');

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));

		if ($file = Kohana::find_file('media/guide', $file, $ext))
		{
			// Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
			$this->check_cache(sha1($this->request->uri()).filemtime($file));
			
			// Send the file content as the response
			$this->response->body(file_get_contents($file));

			// Set the proper headers to allow caching
			$this->response->headers('content-type',  File::mime_by_ext($ext));
			$this->response->headers('last-modified', date('r', filemtime($file)));
		}
		else
		{
			// Return a 404 status
			$this->response->status(404);
		}
	}

	public function after()
	{
		if ($this->auto_render)
		{
			// Get the media route
			$media = Route::get('docs/media');
			
			Assets::css('kodoc', $media->uri(array('file' => 'css/kodoc.css')), 'jquery');
			Assets::css('shcore', $media->uri(array('file' => 'css/shcore.css')), 'jquery');
			Assets::css('shthemekodoc', $media->uri(array('file' => 'css/shthemekodoc.css')), 'jquery');
			
			Assets::js('kodoc', $media->uri(array('file' => 'js/kodoc.js')), 'jquery');
			Assets::js('shcore', $media->uri(array('file' => 'js/shcore.js')), 'jquery');
			Assets::js('shbrushphp', $media->uri(array('file' => 'js/shbrushphp.js')), 'jquery');

			// Add languages
			$this->template->translations = Kohana::message('userguide', 'translations');
		}

		return parent::after();
	}

	public function file($page)
	{
		return Kohana::find_file('guide', $page, 'md');
	}

	public function section($page)
	{
		$markdown = $this->_get_all_menu_markdown();
		
		if (preg_match('~\*{2}(.+?)\*{2}[^*]+\[[^\]]+\]\('.preg_quote($page).'\)~mu', $markdown, $matches))
		{
			return $matches[1];
		}
		
		return $page;
	}

	public function title($page)
	{
		$markdown = $this->_get_all_menu_markdown();
		
		if (preg_match('~\[([^\]]+)\]\('.preg_quote($page).'\)~mu', $markdown, $matches))
		{
			// Found a title for this link
			return $matches[1];
		}
		
		return $page;
	}
	
	protected function _get_all_menu_markdown()
	{
		// Only do this once per request...
		static $markdown = '';
		
		if (empty($markdown))
		{
			// Get menu items
			$file = $this->file($this->request->param('module').'/menu');
	
			if ($file AND $text = file_get_contents($file))
			{
				// Add spans around non-link categories. This is a terrible hack.
				//echo Kohana::debug($text);
				
				//$text = preg_replace('/(\s*[\-\*\+]\s*)(.*)/','$1<span>$2</span>',$text);
				$text = preg_replace('/^(\s*[\-\*\+]\s*)([^\[\]]+)$/m','$1<span>$2</span>',$text);
				//echo Kohana::debug($text);
				$markdown .= $text;
			}
			
		}
		
		return $markdown;
	}
	
	// Get the list of modules from the config, and reverses it so it displays in the order the modules are added, but move Kohana to the top.
	protected function _modules()
	{
		$modules = array_reverse(Kohana::$config->load('userguide.modules'));
		
		if (isset($modules['kohana']))
		{
			$kohana = $modules['kohana'];
			unset($modules['kohana']);
			$modules = array_merge(array('kohana' => $kohana), $modules);
		}
		
		// Remove modules that have been disabled via config
		foreach ($modules as $key => $value)
		{
			if ( ! Kohana::$config->load('userguide.modules.'.$key.'.enabled'))
			{
				unset($modules[$key]);
			}
		}
		
		return $modules;
	}

} // End Userguide
