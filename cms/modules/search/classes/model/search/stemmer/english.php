<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Model_Search_Stemmer_English {

	var $_c = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';
	var $_v = '(?:[aeiou]|(?<![aeiou])y)';
	var $_cache = array( );
	
	public function __construct(  )
	{
		
	}

	function stem( $word, $cache = false )
	{
		if ( strlen( $word ) <= 2 )
			return $word;

		if ( $cache && !empty( $this->_cache[$word] ) )
			return $this->_cache[$word];

		$stem = preg_replace( "~('ve|n't|'d)$~", '', $word );

		$stem = $this->_step1ab( $stem );
		$stem = $this->_step1c( $stem );
		$stem = $this->_step2( $stem );
		$stem = $this->_step3( $stem );
		$stem = $this->_step4( $stem );
		$stem = $this->_step5( $stem );

		if ( $cache )
			$this->_cache[$word] = $stem;

		return $stem;
	}

	function _step1ab( $word )
	{
		// Part a
		if ( substr( $word, -1 ) == 's' )
			$this->_replace( $word, 'sses', 'ss' )
					|| $this->_replace( $word, 'ies', 'i' )
					|| $this->_replace( $word, 'ss', 'ss' )
					|| $this->_replace( $word, 's', '' );

		// Part b
		if ( substr( $word, -2, 1 ) != 'e' || !$this->_replace( $word, 'eed', 'ee', 0 ) )
		{ // First rule
			$v = &$this->_v;

			if ( preg_match( "~$v+~", substr( $word, 0, -3 ) ) && $this->_replace( $word, 'ing', '' )
					|| preg_match( "~$v+~", substr( $word, 0, -2 ) ) && $this->_replace( $word, 'ed', '' ) )
			{ // Note use of && and ||, for precedence reasons
				if ( !$this->_replace( $word, 'at', 'ate' )
						&& !$this->_replace( $word, 'bl', 'ble' )
						&& !$this->_replace( $word, 'iz', 'ize' ) )
				{

					if ( $this->_doubleConsonant( $word )
							&& substr( $word, -2 ) != 'll'
							&& substr( $word, -2 ) != 'ss'
							&& substr( $word, -2 ) != 'zz' )
					{

						$word = substr( $word, 0, -1 );
					}
					elseif ( $this->_occurrences( $word ) == 1 && $this->_cvc( $word ) )
					{
						$word .= 'e';
					}
				}
			}
		}

		return $word;
	}

	function _step1c( $word )
	{
		$v = &$this->_v;
		if ( substr( $word, -1 ) == 'y' && preg_match( "~$v+~", substr( $word, 0, -1 ) ) )
			$this->_replace( $word, 'y', 'i' );
		return $word;
	}

	function _step2( $word )
	{
		switch ( substr( $word, -2, 1 ) )
		{
			case 'a':
				$this->_replace( $word, 'ational', 'ate', 0 )
						|| $this->_replace( $word, 'tional', 'tion', 0 );
				break;

			case 'c':
				$this->_replace( $word, 'enci', 'ence', 0 )
						|| $this->_replace( $word, 'anci', 'ance', 0 );
				break;

			case 'e':
				$this->_replace( $word, 'izer', 'ize', 0 );
				break;

			case 'g':
				$this->_replace( $word, 'logi', 'log', 0 );
				break;

			case 'l':
				$this->_replace( $word, 'entli', 'ent', 0 )
						|| $this->_replace( $word, 'ousli', 'ous', 0 )
						|| $this->_replace( $word, 'alli', 'al', 0 )
						|| $this->_replace( $word, 'bli', 'ble', 0 )
						|| $this->_replace( $word, 'eli', 'e', 0 );
				break;

			case 'o':
				$this->_replace( $word, 'ization', 'ize', 0 )
						|| $this->_replace( $word, 'ation', 'ate', 0 )
						|| $this->_replace( $word, 'ator', 'ate', 0 );
				break;

			case 's':
				$this->_replace( $word, 'iveness', 'ive', 0 )
						|| $this->_replace( $word, 'fulness', 'ful', 0 )
						|| $this->_replace( $word, 'ousness', 'ous', 0 )
						|| $this->_replace( $word, 'alism', 'al', 0 );
				break;

			case 't':
				$this->_replace( $word, 'biliti', 'ble', 0 )
						|| $this->_replace( $word, 'aliti', 'al', 0 )
						|| $this->_replace( $word, 'iviti', 'ive', 0 );
				break;
		}

		return $word;
	}

	function _step3( $word )
	{
		switch ( substr( $word, -2, 1 ) )
		{
			case 'a':
				$this->_replace( $word, 'ical', 'ic', 0 );
				break;

			case 's':
				$this->_replace( $word, 'alise', 'al', 0 )
						|| $this->_replace( $word, 'ness', '', 0 );
				break;

			case 't':
				$this->_replace( $word, 'icate', 'ic', 0 )
						|| $this->_replace( $word, 'iciti', 'ic', 0 );
				break;

			case 'u':
				$this->_replace( $word, 'ful', '', 0 );
				break;

			case 'v':
				$this->_replace( $word, 'ative', '', 0 );
				break;

			case 'z':
				$this->_replace( $word, 'alize', 'al', 0 );
				break;
		}

		return $word;
	}

	function _step4( $word )
	{
		switch ( substr( $word, -2, 1 ) )
		{
			case 'a':
				$this->_replace( $word, 'al', '', 1 );
				break;

			case 'c':
				$this->_replace( $word, 'ance', '', 1 )
						|| $this->_replace( $word, 'ence', '', 1 );
				break;

			case 'e':
				$this->_replace( $word, 'er', '', 1 );
				break;

			case 'i':
				$this->_replace( $word, 'ic', '', 1 );
				break;

			case 'l':
				$this->_replace( $word, 'able', '', 1 )
						|| $this->_replace( $word, 'ible', '', 1 );
				break;

			case 'n':
				$this->_replace( $word, 'ant', '', 1 )
						|| $this->_replace( $word, 'ement', '', 1 )
						|| $this->_replace( $word, 'ment', '', 1 )
						|| $this->_replace( $word, 'ent', '', 1 );
				break;

			case 'o':
				if ( substr( $word, -4 ) == 'tion' || substr( $word, -4 ) == 'sion' )
					$this->_replace( $word, 'ion', '', 1 );
				else
					$this->_replace( $word, 'ou', '', 1 );
				break;

			case 's':
				$this->_replace( $word, 'ism', '', 1 );
				break;

			case 't':
				$this->_replace( $word, 'ate', '', 1 )
						|| $this->_replace( $word, 'iti', '', 1 );
				break;

			case 'u':
				$this->_replace( $word, 'ous', '', 1 );
				break;

			case 'v':
				$this->_replace( $word, 'ive', '', 1 );
				break;

			case 'z':
				$this->_replace( $word, 'ize', '', 1 );
				break;
		}

		return $word;
	}

	function _step5( $word )
	{
		// Part a
		if ( substr( $word, -1 ) == 'e' )
		{
			if ( $this->_occurrences( substr( $word, 0, -1 ) ) > 1 )
			{
				$this->_replace( $word, 'e', '' );
			}
			elseif ( $this->_occurrences( substr( $word, 0, -1 ) ) == 1 )
			{
				if ( !$this->_cvc( substr( $word, 0, -1 ) ) )
					$this->_replace( $word, 'e', '' );
			}
		}

		// Part b
		if ( $this->_occurrences( $word ) > 1 && $this->_doubleConsonant( $word ) && substr( $word, -1 ) == 'l' )
			$word = substr( $word, 0, -1 );

		return $word;
	}

	function _replace( &$str, $check, $repl, $o = null )
	{
		$len = 0 - strlen( $check );
		if ( substr( $str, $len ) == $check )
		{
			$substr = substr( $str, 0, $len );
			if ( is_null( $o ) || $this->_occurrences( $substr ) > $o )
				$str = $substr . $repl;
			return true;
		}
		return false;
	}

	function _occurrences( $str )
	{
		$c = &$this->_c;
		$v = &$this->_v;

		$str = preg_replace( "~^$c+~", '', $str );
		$str = preg_replace( "~$v+$~", '', $str );

		$matches = array( );
		return preg_match_all( "~($v+$c+)~", $str, $matches ) !== false ? count( $matches[1] ) : 0;
	}

	function _doubleConsonant( $str )
	{
		$c = &$this->_c;
		$matches = array( );
		return preg_match( "~$c{2}$~", $str, $matches ) && substr( $matches[0], 0, 1 ) == $matches[0]{1};
	}

	function _cvc( $str )
	{
		$c = &$this->_c;
		$v = &$this->_v;
		$matches = array( );
		return preg_match( "~($c$v$c)$~", $str, $matches ) && strlen( $matches[1] ) == 3
				&& ($l = substr( $matches[1], 2, 1 )) != 'w' && $l != 'x' && $l != 'y';
	}
	
	
	protected static $instances = NULL;

	/**
	 * @return Model_Search_Stemmer_English
	 */
	public static function instance()
	{
		if ( !isset( self::$instances ) )
		{
			self::$instances = new self;
		}

		return self::$instances;
	}

}