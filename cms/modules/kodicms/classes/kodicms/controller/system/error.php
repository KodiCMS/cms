<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	System Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_System_Error extends Controller_System_Frontend {
	
	public $template = 'system/frontend';
	
	public function action_index()
	{
		$this->template->content = View::factory( 'system/error/default' );

		$uri = URL::site( rawurldecode( Request::initial()->uri() ) );
		
		$this->template->content->message = __( 'Critical error' );
		$this->template->content->uri = $uri;

		if ( Request::initial() !== Request::current() )
		{
			if ( $message = rawurldecode( $this->request->param( 'message' ) ) )
			{
				$this->template->content->message = $message;
			}
		}
		
		$this->template->title = __('Error');
		$this->template->content->code = (int) $this->request->param( 'code' );
		$this->template->content->error_type = Arr::get(Response::$messages, $this->template->content->code, 'Not found' );
	}
}
