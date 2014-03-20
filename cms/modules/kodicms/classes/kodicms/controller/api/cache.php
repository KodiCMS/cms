<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS
 * @category	API
 * @author		ButscHSter
 */
class KodiCMS_Controller_API_Cache extends Controller_System_Api {
	
	public function before() 
	{
		parent::before();
	}
	
	public function get_clear()
	{
		if( ! ACL::check('system.cache.clear'))
		{
			throw HTTP_API_Exception::factory(API::ERROR_PERMISSIONS, 'You dont hanve permissions to clear cache');
		}
			
		if(Kohana::$caching === TRUE)
		{
			Cache::instance()->delete_all();
			Kohana::cache('Kohana::find_file()', NULL, -1);
			Kohana::cache('Route::cache()', NULL, -1);
			Kohana::cache('profiler_application_stats', NULL, -1);
		}
		
		Kohana::$log->add(Log::INFO, ':user clear cache')->write();
		
		$this->json['message'] = __( 'Cache has been cleared!' );
	}
}