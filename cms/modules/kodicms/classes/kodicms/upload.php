<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS
 * @category	Helper
 * @author		ButscHSter
 */
class KodiCMS_Upload extends Kohana_Upload {
	
	/**
	 * Загрузка файла и сохранение в папку TMPPATH
	 *
	 *		try
	 *		{
	 *			$filename = Upload::file($_FILES['file'], array('jpg', 'jpeg', 'gif', 'png'));
	 *			$path = TMPPATH . $filename;
	 *		}
	 *		catch (Validation_Exception $e)
	 *		{
	 *			echo debug::vars($e->errors('validation'));
	 *		}
	 * 
	 * При указании строки в качестве параметра $file, будет произведена 
	 * попытка загрузить файл по URL
	 * 
	 * @param string|array $file
	 * @param array $types Разрешенные типы файлов (При указании пустой строки, разрешены все файлы) array('jpg', '...')
	 * @return string|NULL Название файла.
	 * @throws Validation_Exception
	 */
	public static function file( $file, array $types = array('jpg', 'jpeg', 'gif', 'png') )
	{
		if( ! is_array($file) )
		{
			return Upload::from_url($file, $types);
		}

		$validation = Validation::factory( array('file' => $file ) )
			->rules( 'file', array(
				array('Upload::valid'),
				array('Upload::size', array(':value', 100000000))
			) );
		
		if( ! empty($types) )
		{
			$validation->rule('file', array('Upload::type', array(':value', $types)));
		}

		if ( ! $validation->check() )
		{
			throw new Validation_Exception($validation);
		}

		$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		$filename = uniqid() . '.' . $ext;
		
		if( ! is_dir( TMPPATH ))
		{
			mkdir(TMPPATH, 0777);
			chmod(TMPPATH, 0777);
		}

		Upload::save( $file, $filename, TMPPATH, 0777 );

		return $filename;
	}
	
	/**
	 * 
	 * Загрузка файла по URL и сохранение в папку TMPPATH
	 *
	 *		try
	 *		{
	 *			$filename = Upload::from_url('http://....', array('jpg', 'jpeg', 'gif', 'png'));
	 *			$path = TMPPATH . $filename;
	 *		}
	 *		catch (Validation_Exception $e)
	 *		{
	 *			echo debug::vars($e->errors('validation'));
	 *		}
	 * 
	 * 
	 * @param string $url Ссылка на файл (http://....)
	 * @param array $types Разрешенные типы файлов (При указании пустой строки, разрешены все файлы) array('jpg', '...')
	 * @return string|NULL Название файла
	 * @throws Validation_Exception
	 */
	public static function from_url($url, array $types = array('jpg', 'jpeg', 'gif', 'png') )
	{
		$url = trim($url);
		$ext = pathinfo($url, PATHINFO_EXTENSION);

		$validation = Validation::factory( array('url' => $url, 'ext' => $ext ) )
			->rules( 'url', array(
				array('url'),
				array('not_empty'),
			) );
		
		if( ! empty($types) )
		{
			$validation->rule('ext', array('in_array', array(':value', $types)));
		}
			
		if ( ! $validation->check() )
		{
			throw new Validation_Exception($validation);
		}

		$filename = uniqid() . '.' . $ext;
		
		if( ! is_dir( TMPPATH ))
		{
			mkdir(TMPPATH, 0777);
			chmod(TMPPATH, 0777);
		}

		// Make the filename into a complete path
		$path = TMPPATH.$filename;
		
		$file = fopen($url, 'rb');

		if( ! $file)
		{
			return array(TRUE, NULL);
		}
		
		$new_file = fopen($path, 'wb');
		
		if($new_file)
		{
			while(!feof($file))
			{
				// Write the url file to the directory.
				fwrite($new_file, fread($file, 1024 * 8), 1024 * 8);
			}

			// Set permissions on filename
			chmod($path, 0777);
			
			return $filename;
		}
		
		return NULL;
	}
}