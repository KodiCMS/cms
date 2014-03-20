<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Model_Search_Stemmer_Russian {
	
	const CHAR_LENGTH = 2;

	public function __construct(  )
	{
		
	}
	
	function stem($word)
	{
		$a = $this->rv( $word );
		
		$result = $this->step1($a[1]);
		$result = $this->step2($result);
		$result = $this->step3($result);
		$result = $this->step4($result);
		
		return $a[0] . $result;
	}

	function rv( $word )
	{
		$vowels = array( 'а', 'е', 'и', 'о', 'у', 'ы', 'э', 'ю', 'я' );
		$flag = 0;
		$rv = $start = '';
		for ( $i = 0; $i < strlen( $word ); $i+=self::CHAR_LENGTH )
		{
			if ( $flag == 1 )
				$rv .= substr( $word, $i, self::CHAR_LENGTH ); else
				$start .= substr( $word, $i, self::CHAR_LENGTH );
			if ( array_search( substr( $word, $i, self::CHAR_LENGTH ), $vowels ) !== FALSE )
				$flag = 1;
		}
		return array( $start, $rv );
	}

	function step1( $word )
	{
		$perfective1 = array( 'в', 'вши', 'вшись' );
		foreach ( $perfective1 as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix && (substr( $word, -strlen( $suffix ) - self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'а' || substr( $word, -strlen( $suffix ) - self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'я') )
				return substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
		$perfective2 = array( 'ив', 'ивши', 'ившись', 'ывши', 'ывшись' );
		foreach ( $perfective2 as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
				return substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
		$reflexive = array( 'ся', 'сь' );
		foreach ( $reflexive as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
				$word = substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
		$adjective = array( 'ее', 'ие', 'ые', 'ое', 'ими', 'ыми', 'ей', 'ий', 'ый', 'ой', 'ем', 'им', 'ым', 'ом', 'его', 'ого', 'ему', 'ому', 'их', 'ых', 'ую', 'юю', 'ая', 'яя', 'ою', 'ею' );
		$participle2 = array( 'ем', 'нн', 'вш', 'ющ', 'щ' );
		$participle1 = array( 'ивш', 'ывш', 'ующ' );
		foreach ( $adjective as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
			{
				$word = substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
				foreach ( $participle1 as $suffix )
					if ( substr( $word, -(strlen( $suffix )) ) == $suffix && (substr( $word, -strlen( $suffix ) - self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'а' || substr( $word, -strlen( $suffix ) - self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'я') )
						$word = substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
				foreach ( $participle2 as $suffix )
					if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
						$word = substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
				return $word;
			}
		$verb1 = array( 'ла', 'на', 'ете', 'йте', 'ли', 'й', 'л', 'ем', 'н', 'ло', 'но', 'ет', 'ют', 'ны', 'ть', 'ешь', 'нно' );
		foreach ( $verb1 as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix && (substr( $word, -strlen( $suffix ) - self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'а' || substr( $word, -strlen( $suffix ) - self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'я') )
				return substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
		$verb2 = array( 'ила', 'ыла', 'ена', 'ейте', 'уйте', 'ите', 'или', 'ыли', 'ей', 'уй', 'ил', 'ыл', 'им', 'ым', 'ен', 'ило', 'ыло', 'ено', 'ят', 'ует', 'уют', 'ит', 'ыт', 'ены', 'ить', 'ыть', 'ишь', 'ую', 'ю' );
		foreach ( $verb2 as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
				return substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
		$noun = array( 'а', 'ев', 'ов', 'ие', 'ье', 'е', 'иями', 'ями', 'ами', 'еи', 'ии', 'и', 'ией', 'ей', 'ой', 'ий', 'й', 'иям', 'ям', 'ием', 'ем', 'ам', 'ом', 'о', 'у', 'ах', 'иях', 'ях', 'ы', 'ь', 'ию', 'ью', 'ю', 'ия', 'ья', 'я' );
		foreach ( $noun as $suffix )
			if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
				return substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
		return $word;
	}

	function step2( $word )
	{
		return substr( $word, -self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'и' ? substr( $word, 0, strlen( $word ) - self::CHAR_LENGTH ) : $word;
	}

	function step3( $word )
	{
		$vowels = array( 'а', 'е', 'и', 'о', 'у', 'ы', 'э', 'ю', 'я' );
		$flag = 0;
		$r1 = $r2 = '';
		for ( $i = 0; $i < strlen( $word ); $i+=self::CHAR_LENGTH )
		{
			if ( $flag == 2 )
				$r1 .= substr( $word, $i, self::CHAR_LENGTH );
			if ( array_search( substr( $word, $i, self::CHAR_LENGTH ), $vowels ) !== FALSE )
				$flag = 1;
			if ( $flag = 1 && array_search( substr( $word, $i, self::CHAR_LENGTH ), $vowels ) === FALSE )
				$flag = 2;
		}
		$flag = 0;
		for ( $i = 0; $i < strlen( $r1 ); $i+=self::CHAR_LENGTH )
		{
			if ( $flag == 2 )
				$r2 .= substr( $r1, $i, self::CHAR_LENGTH );
			if ( array_search( substr( $r1, $i, self::CHAR_LENGTH ), $vowels ) !== FALSE )
				$flag = 1;
			if ( $flag = 1 && array_search( substr( $r1, $i, self::CHAR_LENGTH ), $vowels ) === FALSE )
				$flag = 2;
		}
		$derivational = array( 'ост', 'ость' );
		foreach ( $derivational as $suffix )
			if ( substr( $r2, -(strlen( $suffix )) ) == $suffix )
				$word = substr( $word, 0, strlen( $r2 ) - strlen( $suffix ) );
		return $word;
	}

	function step4( $word )
	{
		if ( substr( $word, -self::CHAR_LENGTH * 2 ) == 'нн' )
			$word = substr( $word, 0, strlen( $word ) - self::CHAR_LENGTH );
		else
		{
			$superlative = array( 'ейш', 'ейше' );
			foreach ( $superlative as $suffix )
				if ( substr( $word, -(strlen( $suffix )) ) == $suffix )
					$word = substr( $word, 0, strlen( $word ) - strlen( $suffix ) );
			if ( substr( $word, -self::CHAR_LENGTH * 2 ) == 'нн' )
				$word = substr( $word, 0, strlen( $word ) - self::CHAR_LENGTH );
		}
		if ( substr( $word, -self::CHAR_LENGTH, self::CHAR_LENGTH ) == 'ь' )
			$word = substr( $word, 0, strlen( $word ) - self::CHAR_LENGTH );
		return $word;
	}
	
	protected static $instances = NULL;

	/**
	 * @return Model_Search_Stemmer_Russian
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