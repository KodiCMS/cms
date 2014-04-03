<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	System Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_System_Ajax extends Controller_System_Security {

	public function before()
	{
//		if(
//			Request::current()->is_ajax() === FALSE 
//			OR
//			Request::current()->method() != Request::POST
//		)
//		{
//			throw new Http_Exception_404('Доступ запрещен');
//			return;
//		}

		parent::before();
	}

}