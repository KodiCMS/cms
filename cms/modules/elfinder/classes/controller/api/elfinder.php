<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

/**
 * @package		KodiCMS/Elfinder
 * @category	API
 * @author		ButscHSter
 */
class Controller_API_Elfinder extends Controller_System_Api {
	
	public function before()
	{
		parent::before();
		
		include_once Kohana::find_file('media', 'libs/elfinder/php/elFinderConnector.class');
		include_once Kohana::find_file('media', 'libs/elfinder/php/elFinder.class');
		include_once Kohana::find_file('media', 'libs/elfinder/php/elFinderVolumeDriver.class');
		include_once Kohana::find_file('media', 'libs/elfinder/php/elFinderVolumeLocalFileSystem.class');
		
		$opts = array(
			'roots' => array(
				array(
					'driver'        => 'LocalFileSystem',	// driver for accessing file system (REQUIRED)
					'path'          => substr(PUBLICPATH, 0, -1),			// path to files (REQUIRED)
					'URL'           => PUBLIC_URL,			// URL to files (REQUIRED),
					'rootAlias'     => __('Public'),
					'uploadMaxSize'	=> Config::get('elfinder', 'uploadMaxSize'),
					'mimeDetect'	=> Config::get('elfinder', 'mimeDetect'),
					'imgLib'		=> Config::get('elfinder', 'imgLib'),
					
				)
			)
		);
		
		$this->json = NULL;

		// run elFinder
		$connector = new elFinderConnector(new elFinder($opts));
		$connector->run();
	}
	
	public function rest_get()
	{
		
	}
	
	public function rest_put()
	{
		
	}
	
	public function rest_delete()
	{
		
	}
}

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}