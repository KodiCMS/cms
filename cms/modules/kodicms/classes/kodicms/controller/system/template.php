<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	System Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_System_Template extends Controller_System_Security
{
	/**
	 * @var  View  page template
	 */
	public $template = 'system/backend';

	/**
	 *
	 * @var \Breadcrumbs 
	 */
	public $breadcrumbs;

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;
	
	/**
	 *
	 * @var boolean
	 */
	public $only_content = FALSE;
	
	/**
	 *
	 * @var mixed
	 */
	public $json = NULL;

	/**
	 * Loads the template [View] object.
	 */
	public function before()
	{
		parent::before();
		
		if($this->request->method() === Request::POST)
		{
//			$token = Arr::get($_POST, 'token');
//			if(empty($token) OR !Security::check($token))
//			{
//				throw new Exception('Security token not check');
//			}
		}

		if ($this->auto_render === TRUE)
		{
			if ( $this->request->is_ajax() === TRUE )
			{
				// Load the template
				$this->template = View::factory( 'system/ajax' );
			}
			else
			{
				$this->template = View::factory( $this->template );
			}
			
			// Initialize empty values
			$this->template->title = NULL;
			$this->template->content = NULL;
			
			$index_page_url = FALSE;
			
			$this->breadcrumbs = Breadcrumbs::factory();
			
			$routes = Route::all();
			if( isset($routes['backend']) )
			{
				$this->breadcrumbs
						->add(UI::icon('home'), Route::get( 'backend' )->uri());
			}
		}
	}
	
	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		parent::after();

		if ($this->auto_render === TRUE)
		{
			if ( $this->request->is_ajax() === TRUE OR $this->json !== NULL)
			{
				if ( $this->json !== NULL )
				{
					if ( is_array( $this->json ) AND !isset( $this->json['status'] ) )
					{
						$this->json['status'] = TRUE;
					}

					$this->response
						->headers( 'Content-type', 'application/json' );

					$this->template = json_encode( $this->json );
				}
				else
				{
					$this->only_content = TRUE;
				}
			}
			else
			{
				$this->template->messages = View::factory('system/blocks/messages', array(
					'messages' => Messages::get() 
				));
			}
			
			if($this->only_content)
			{
				$this->template = $this->template->content;
			}
			
			$this->template->set('request', $this->request);
			
			Observer::notify( 'template_before_render', $this->request );
			$this->response->body( $this->template );
		}
	}
	
	/**
	 * 
	 * @param string $separator
	 * @return string
	 */
	public function get_path($separator = '_')
	{
		$path = $this->request->controller() . $separator . $this->request->action();
		$dir = $this->request->directory();

		if ( !empty( $dir ) )
		{
			$path = $dir . $separator . $path;
		}

		return strtolower($path);
	}
	
	/**
	 *
	 * @param string $title
	 * @param boolean $set_breadcrumbs
	 * @return Controller
	 */
	public function set_title( $title, $set_breadcrumbs = TRUE )
	{
		$this->template->title = $title;
	
		if($set_breadcrumbs === TRUE)
		{
			$this->breadcrumbs->add($title);
		}
	
		return $this;
	}
}