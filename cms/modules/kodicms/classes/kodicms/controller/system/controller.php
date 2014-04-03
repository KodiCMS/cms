<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	System Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_System_Controller extends Controller
{
	public $query_params = FALSE;
	
	public function before()
	{
		Context::instance()->request($this->request);
		parent::before();
	}
	
	public function go_home()
	{
		if(IS_BACKEND)
		{
			$this->go_backend();
		}
		else
		{
			$this->go( Route::get( 'default' )->uri() );
		}
	}
	
	public function go_backend()
	{
		$this->go( Route::get( 'backend' )->uri(array(
			'controller' => str_replace(ADMIN_DIR_NAME . '/', '', Config::get('site', 'default_tab')),
		) ) );
	}

	public function go_back()
	{
		if ( Valid::url( $this->request->referrer() ) )
		{
			$this->go( $this->request->referrer() );
		}
	}
	
	/**
	 * @param string  $event
	 * @param string  $default_uri
	 *
	 * @throws HTTP_Exception_301
	 */
	protected function _go_back($event, $default_uri = '')
	{
		$uri = Session::instance()->get_once($event, $default_uri);
		$this->go($uri);
	}

	public function go( $url = NULL, $code = 302 )
	{
		$route_params = array(
			'controller' => strtolower($this->request->controller())
		);

		if ( is_array( $url ) OR $url === NULL )
		{
			if(is_array( $url ))
			{
				$route_params = Arr::merge( $route_params, $url );
			}
			
			$url = Route::get('backend')->uri($route_params);
		}
		
		if( is_array( $this->query_params ) )
		{
			$url = preg_replace('/\?.*/', '', $url);
			$url .= URL::query( $this->query_params, TRUE);
		}

		$this->redirect( $url, $code );
	}

	protected function _changed_uri($params)
	{
		if (is_string($params))
		{
			// assume its an action name
			$params = array('action' => $params);
		}

		$current_params = $this->request->param();
		$current_params['controller'] = strtolower($this->request->controller());
		$current_params['directory'] = strtolower($this->request->directory());
		$current_params['action'] = strtolower($this->request->action());
		$params = $params + $current_params;
		return Route::get(Route::name(Request::current()->route()))->uri($params, TRUE);
	}
	
	protected function _save_referer($event, $referer = FALSE)
	{
		if ($referer === TRUE)
		{
			$referer = $this->request->uri();
		}
		elseif ($referer === FALSE)
		{
			$referer = Request::initial()->referrer();
		}
		else
		{
			$referer = (string) $referer;
		}

		$hostname = parse_url($referer, PHP_URL_HOST);
		$current_hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		if ($hostname == $current_hostname)
		{
			Session::instance()->set($event, $referer);
			return TRUE;
		}

		return FALSE;
	}

} // end Controller class