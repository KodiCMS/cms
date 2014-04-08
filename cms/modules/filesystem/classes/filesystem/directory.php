<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/FileSystem
 * @author		ButscHSter
 */
class FileSystem_Directory extends DirectoryIterator {

	/**
	 * 
	 * @param string $name
	 * @param integer $chmod
	 * @return boolean
	 */
	public function create( $name, $chmod = 755 )
	{
		$folder_path = $this->getPath() . DIRECTORY_SEPARATOR . $name;
		if ( ! is_dir($folder_path) )
		{
			if( mkdir($folder_path) )
			{
				chmod($folder_path, $chmod);
				return TRUE;
			}
		}
		
		return FALSE;
	}

	/**
	 * 
	 * @param boolean $preserve
	 * @return boolean
	 */
	public function delete( $preserve = FALSE )
	{
		$dirHandle = opendir($this->getPath());
			
		while (FALSE !== ($file = readdir($dirHandle))) 
		{
			if ($file != '.' AND $file != '..')
			{
				$tmpPath = $this->getPath() . DIRECTORY_SEPARATOR . $file;
				chmod($tmpPath, 0777);

				FileSystem::factory($tmpPath)->delete();
			}
		}

		closedir($dirHandle);

		if ( file_exists($this->getPath()) AND $preserve === FALSE )
		{
			return rmdir($this->getPath());
		}
		
		return FALSE;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function clean()
	{
		return $this->delete(TRUE);
	}

	/**
	 * 
	 * @return FileSystem
	 */
	public function getParent()
	{
		$path = $this->getPath();
		$parent_path = (!empty($path) ? substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR)): '');
		return FileSystem::factory($parent_path);
	}
}