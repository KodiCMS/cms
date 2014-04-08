<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class URL extends Kohana_URL {
	
	/**
	 * 
	 * @param string $uri
	 * @param string $suffix
	 * @return boolean
	 */
	public static function check_suffix($uri, $suffix = NULL)
	{
		if($suffix === NULL AND defined('URL_SUFFIX') AND strlen(URL_SUFFIX) > 0)
		{
			$suffix = URL_SUFFIX;
		}
		
		return ! (strstr($uri, $suffix) === FALSE);
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param string $protocol
	 * @param boolean $index
	 * @return string
	 */
	public static function backend($uri = '', $protocol = NULL, $index = TRUE)
	{
		if( ! URL::match( ADMIN_DIR_NAME, $uri ))
		{
			$uri = ADMIN_DIR_NAME . '/' . ltrim( $uri, '/');
		}

		return parent::site($uri, $protocol, $index);
	}
	
	/**
	 * 
	 * @param string $uri
	 * @param string $protocol
	 * @param boolean $index
	 * @return string
	 */
	public static function frontend($uri = '', $protocol = NULL, $index = TRUE)
	{
		$hash = '';
		if( strpos($uri, '#') !== FALSE)
		{
			list($uri, $hash) = preg_split('/#/', $uri);
			
			$hash = '#' . $hash;
		}
		
		if( IS_INSTALLED AND ! empty($uri) AND $uri != '/' AND ! URL::check_suffix( $uri ))
		{
			$uri .= URL_SUFFIX . $hash;
		}

		return parent::site($uri, $protocol, $index);
	}

	/**
	 * 
	 * @param string $uri
	 * @param string $current
	 * @return boolean
	 */
	public static function match( $uri, $current = NULL )
	{
		$uri = trim( $uri, '/' );

		if ( $current === NULL AND Request::current() )
		{
			$current = Request::current()->uri();
		}

		$current = trim( $current, '/' );

		if ( $current == $uri )
		{
			return TRUE;
		}
		
		if(empty($uri))
		{
			return FALSE;
		}

		if ( strpos( $current, $uri ) !== FALSE )
		{
			return TRUE;
		}

		return FALSE;
	}
}

// End url