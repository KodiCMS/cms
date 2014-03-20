<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_Front extends Controller_System_Controller
{
	/**
	 *
	 * @var Context 
	 */
	protected $_ctx = NULL;

	public function before()
	{
		parent::before();
		
		$this->_ctx =& Context::instance();

		$this->_ctx
			->request( $this->request )
			->response( $this->response );
		
		View_Front::bind_global('ctx', $this->_ctx);
	}

	public function action_index()
	{
		Observer::notify('frontpage_requested', array($this->request->uri()));
		
		$page = Model_Page_Front::find($this->request->uri());

		if ($page instanceof Model_Page_Front)
		{
			return $this->_render($page);
		}
		else
		{
			// Если включен поиск похожей страницы и она найдена, производим
			// редирект на найденую страницу
			if(Config::get('site', 'find_similar') == Config::YES)
			{
				$uri = Model_Page_Front::find_similar($this->request->uri());
				
				if($uri !== FALSE)
				{
					HTTP::redirect($uri, 301);
				}
			}
			
			Model_Page_Front::not_found();
		}
	}
	
	/**
	 * 
	 * @param type Model_Page_Front
	 */
	private function _render( Model_Page_Front $page)
	{
		$this->_ctx->set_page($page);

		// If page needs login, redirect to login
		if ($page->needs_login() == Model_Page::LOGIN_REQUIRED)
		{
			Observer::notify('frontpage_login_required', $page);

			if ( ! AuthUser::isLoggedIn())
			{
				Flash::set('redirect', $page->url());

				$this->redirect(Route::get('user')->uri(array( 
					'action' => 'login'
				) ));
			}
		}
		
		View_Front::bind_global('page', $page);
		Observer::notify('frontpage_found', $page);
		
		$this->_ctx->build_crumbs();
		
		// Если установлен статус 404, то выводим страницу 404
		// Страницу 404 могут выкидывать также Виджеты
		if( Request::current()->is_initial() AND $this->response->status() == 404)
		{
			$message = $this->_ctx->get('throw_message');
			
			$this->_ctx = NULL;
			Model_Page_Front::not_found($message);
		}

		$html = (string) $page->render_layout();

		// Если пользователь Администраторо или девелопер, в конец шаблона 
		// добавляем View 'system/blocks/toolbar', в котором можно добавлять 
		// собственный HTML, например панель администратора
		if ( AuthUser::isLoggedIn() AND AuthUser::hasPermission(array(
			'administrator', 'developer'
		)))
		{
			$inject_html = (string) View::factory( 'system/blocks/toolbar' );
			
			// Insert system HTML before closed tag body
			$matches = preg_split('/(<\/body>)/i', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE); 
			
			if(count($matches) > 1)
			{
				/* assemble the HTML output back with the iframe code in it */
				$html = $matches[0] . $inject_html . $matches[1] . $matches[2];
			}
		}
		
		// Если в начтройках выключен режим отладки, то включить etag кеширование
		if( Config::get('site', 'debug ') == 'no' )
		{
			$this->check_cache(sha1($html));
			$this->response->headers('last-modified', date('r', strtotime($page->updated_on)));
		}
		
		$this->response->headers('Content-Type',  $page->mime() );
		$this->response->headers('X-Powered-CMS',  CMS_NAME . ' ' . CMS_VERSION );
		
		echo $html;
	}
	
	public function after()
	{
		parent::after();

		Observer::notify('frontpage_after_render');
	}
}