<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/FileSystem
 * @author		ButscHSter
 */
class FileSystem {
	
	/**
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function normalize_path( $path )
	{
		return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	}

	/**
	 *
	 * @var string
	 */
	protected $_file;
	
	/**
	 *
	 * @var string 
	 */
	protected $_path;
	
	/**
	 *
	 * @var string 
	 */
	protected $_root;


	/**
	 *
	 * @var string 
	 */
	protected $_real_path;

	/**
	 * 
	 * @param string|SplFileInfo $file
	 * @return \FileSystem
	 * @throws Kohana_Exception
	 */
	public static function factory( $path )
	{
		if( ! ($path instanceof SplFileInfo) )
		{
			$path = FileSystem::normalize_path($path);
			
			if ( file_exists( $path ) ) 
			{
				if ( ! is_dir( $path ) )
				{
					$path = new FileSystem_File( $path );
				}
				else 
				{
					$path = new FileSystem_Directory( $path );
				}
			}
			else
			{
				throw new Kohana_Exception( 'Directory or file :path not found', array(
					':path' => $path
				) );
			}
		}

		return new FileSystem( $path );
	}

	/**
	 * 
	 * @param SplFileInfo $file
	 */
	public function __construct( SplFileInfo $file )
	{
		$this->_file = $file;
		
		$this->_path = $file->getPath();
		$this->_real_path = $file->getRealPath();
	}
	
	/**
	 * 
	 * @return SplFileInfo|DirectoryIterator
	 */
	public function get_object()
	{
		return $this->_file;		
	}

	public function __get( $name )
	{
		if(isset($this->_file->{$name}))
		{
			return $this->_file->{$name};
		}
	}

	public function __call( $method, $arguments = array() )
	{
		if( method_exists( $this->_file, $method ))
		{
			return call_user_func_array(array($this->_file, $method), $arguments);
		}
	}

	/**
	 * 
	 * @return string
	 */
	public function getPerms()
	{
		$perms = $this->_file->getPerms();

		if ( ($perms & 0xC000) == 0xC000 )
		{
			// Socket
			$info = 's';
		}
		elseif ( ($perms & 0xA000) == 0xA000 )
		{
			// Symbolic Link
			$info = 'l';
		}
		elseif ( ($perms & 0x8000) == 0x8000 )
		{
			// Regular
			$info = '-';
		}
		elseif ( ($perms & 0x6000) == 0x6000 )
		{
			// Block special
			$info = 'b';
		}
		elseif ( ($perms & 0x4000) == 0x4000 )
		{
			// Directory
			$info = 'd';
		}
		elseif ( ($perms & 0x2000) == 0x2000 )
		{
			// Character special
			$info = 'c';
		}
		elseif ( ($perms & 0x1000) == 0x1000 )
		{
			// FIFO pipe
			$info = 'p';
		}
		else
		{
			// Unknown
			$info = 'u';
		}

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
						(($perms & 0x0800) ? 's' : 'x' ) :
						(($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
						(($perms & 0x0400) ? 's' : 'x' ) :
						(($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		(($perms & 0x0200) ? 't' : 'x' ) :
		(($perms & 0x0200) ? 'T' : '-'));

		return substr( sprintf( '%o', $perms ), -4, 4 ); // (perm, chmod)
	}

	/**
	 * 
	 * @return string
	 */
	public function getUrl()
	{
		return BASE_URL . str_replace(array('/', '\\'), '/', $this->getRelativePath());
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getRelativePath($path = DOCROOT)
	{
		return trim(str_replace(rtrim($path, DIRECTORY_SEPARATOR), '', $this->_real_path), DIRECTORY_SEPARATOR);
	}

	/**
	 * 
	 * @return array
	 */
	public function getPathArray()
	{
		$dirs_array = explode(DIRECTORY_SEPARATOR, $this->getRelativePath());
		$path = '';
		$dirs_count = count($dirs_array);
		$paths_array = array();

		for($i = 0; $i < $dirs_count; ++$i)
		{
			if(
				empty($dirs_array[$i]) 
			OR 
				$dirs_array[$i] == '.' 
			OR  
				$dirs_array[$i] == '..'
			)
			{
				continue;
			}

			$paths_array[$path . $dirs_array[$i]] = $dirs_array[$i];
			$path .= $dirs_array[$i] . DIRECTORY_SEPARATOR;
		}
		
		return $paths_array;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function rename($name)
	{
		if (rename($this->_real_path, $this->_path . DIRECTORY_SEPARATOR . $name))
		{
			return FileSystem::factory($this->_path . DIRECTORY_SEPARATOR . $name);
		}
		
		return FALSE;
	}
	
	/**
	 * 
	 * @param integer $chmod
	 * @return boolean
	 */
	public function setPerms($chmod)
	{
		return chmod($this->_real_path, octdec((int) $chmod));
	}

	/**
	 * 
	 * @param string $ext
	 * @return array
	 */
	public function iteratePaths( $ext = NULL )
    {
		$array = array();

		while($this->_file->valid()) 
		{
			if( $ext !== NULL AND $this->_file->isFile() 
					AND !(substr($this->_file->getFilename(), -strlen($ext)) == $ext) )
			{
				$this->_file->next();
				continue;
			}

			$array[] = new FileSystem(clone($this->_file));
			$this->_file->next();
		}
		
		return $array;
    }
}