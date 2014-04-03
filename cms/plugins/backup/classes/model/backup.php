<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Backup
 * @category	Model
 * @author		ButscHSter
 */
abstract class Model_Backup {

	/**
	 *
	 * @var string
	 */
	public $file = NULL;

	/**
	 * 
	 * @param string $file
	 */
	protected function __construct($file = NULL) 
	{
		$this->file = $file;
	}
	
	/**
	 * 
	 * @param string $file
	 * @return Model_Backup
	 * @throws HTTP_Exception_404
	 */
	public static function factory($file = NULL)
	{
		if( is_dir($file))
			throw new HTTP_Exception_404('Must be file');

		$ext = pathinfo($file, PATHINFO_EXTENSION);
		
		$class = NULL;

		switch ( $ext )
		{
			case 'zip':
				$class = 'Model_Backup_FileSystem';
				break;
			case 'sql':
				$class = 'Model_Backup_Database';
				break;
			default:
				throw new HTTP_Exception_404('Extension not supported');
		}

		return new $class($file);
	}
	
	abstract public function create();
	abstract public function view();
	abstract public function restore();
}