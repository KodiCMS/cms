<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Model_Search_Tokenizer {

	const TOKEN_NUMBER = 0;
	const TOKEN_WORD_EN = 1;
	const TOKEN_WORD_RU = 2;
	const TOKEN_URL = 3;
	const TOKEN_EMAIL = 4;
	const TOKEN_UNDEFINED = 5;

	public $position;
	public $text = '';
	
	protected $_token;
	protected $_tokens;
	
	public $stopwords = array();

	public function __construct()
	{
		;
	}
	
	public function set_text($text)
	{
		$this->text = $text;
		$this->_split_text($text);
	}
	
	protected function _split_text($text) 
	{
		$str = str_replace(array(',','(',')','?','&nbsp;','"','{','}','!','=','&lt;','&gt;','&quot;','&#039;','&laquo;','&raquo;','&trade;','&copy;','&reg;',';'), ' ', $text);

		$this->_token = preg_split('/[\s\.:\']*(\s|$)[\.\']*/', $str, -1, PREG_SPLIT_NO_EMPTY);
		$this->_tokens = sizeof($this->_token);
		$this->_reset();
	}
	
	protected function _reset() 
	{
		$this->position = 0;
	}
	
	public function next() 
	{
		$result = NULL;
	
		while($this->position < $this->_tokens && !$this->_is_valid($result))
		{
			$result = $this->_token[$this->position++];
		}

		return $result;
	}
	
	protected function _is_valid(&$token) 
	{
		if(!$token)
		{
			return FALSE;
		}

		if(UTF8::strlen($token) > 32)
		{
			return FALSE;
		}
	
		if(isset($this->stopwords[$token]))
		{
			return FALSE;
		}

		if(strspn($token,'[]<>-_$.\\+*/') == strlen($token))
		{
			return FALSE;
		}

		return TRUE;
	}
	
	public function get_type($token) 
	{
		if(strspn($token,'0123456789') == strlen($token))
		{
			return self::TOKEN_NUMBER;
		}

		if(preg_match('/^[а-яё]+$/u',$token))
		{
			return self::TOKEN_WORD_RU;
		}

		if(preg_match('/^[a-z][a-z\'’]*$/',$token))
		{
			return self::TOKEN_WORD_EN;
		}

		if(preg_match('/^(?:http:\/\/)?(?:[a-z0-9][a-z0-9\-\.]*\.[a-z]{2,5}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/',$token))
		{
			return self::TOKEN_URL;
		}
		if(preg_match('/^[a-z][a-z0-9_\-\.]*@(?:[a-z0-9][a-z0-9\-\.]*\.[a-z]{2,5}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/',$token))
		{
			return self::TOKEN_EMAIL;
		}

		return self::TOKEN_UNDEFINED;
	}
	
	
	protected static $instances = NULL;

	/**
	 * @return Model_Search_Tokenizer
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