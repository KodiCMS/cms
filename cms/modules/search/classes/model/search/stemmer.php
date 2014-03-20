<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Model_Search_Stemmer {
	
	protected $_tokenizer = NULL;

	public function __construct(  Model_Search_Tokenizer $tokenizer)
	{
		$this->_tokenizer = $tokenizer;
	}
	
	public function stem($word)
	{
		$type = $this->_tokenizer->get_type( $word );

		switch ( $type )
		{
			case Model_Search_Tokenizer::TOKEN_WORD_EN:
				$result = Model_Search_Stemmer_English::instance()->stem( $word, true ) . ' ';
				break;
			case Model_Search_Tokenizer::TOKEN_WORD_RU:
				$stem = Model_Search_Stemmer_Russian::instance()->stem( $word );
				$result = $stem . ' ';
				break;
			default:
				$result = $word . ' ';
		}
		
		return $result;
	}
	
	protected function _pad_word( &$word, $letter, $length = 4 )
	{
		$l = UTF8::strlen( $word );
		if ( $l < $length )
		{
			$word = str_repeat( $letter, $length - $l ) . $word;
		}
	}
}