<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/FileSystem
 * @author		ButscHSter
 */
class FileSystem_File extends SplFileInfo {

	/**
	 * 
	 * @param string $content
	 * @return int The function returns the number of bytes that were written to the file, or
	 * <b>FALSE</b> on failure.
	 */
	public function setContent($content)
	{
		return file_put_contents($this->getRealPath(), $content);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return file_get_contents($this->getRealPath());
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getMime()
	{
		return File::mime($this->getRealPath());
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function delete()
	{
		chmod($this->getRealPath(), 0777);
		return unlink($this->getRealPath());
	}
	
	/**
	 * 
	 * @return FileSystem
	 */
	public function getParent()
	{
		return FileSystem::factory($this->getPathInfo());
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isImage()
	{
		try
		{
			// Get the image information
			return getimagesize($this->getRealPath());
		}
		catch (Exception $e)
		{
			return FALSE;
		}
	}
}