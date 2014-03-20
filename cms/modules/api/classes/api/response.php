<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/API
 * @author		ButscHSter
 */
class API_Response {
	
	const OBJ = 'object';
	const ARR = 'array';
	
	/**
	 *
	 * @var Response 
	 */
	protected $_response = NULL;

	/**
	 *
	 * @var integer 
	 */
	protected $_code = 200;
	
	/**
	 *
	 * @var string 
	 */
	protected $_data_type = NULL;
	
	/**
	 *
	 * @var mixed 
	 */
	protected $_data = NULL;

	/**
	 * 
	 * @param Response $response
	 */
	public function __construct(Response $response) 
	{
		$this->_response = $response;

		if(isset($this->code))
		{
			$this->_code = $this->get('code');
		}
	}
	
	/**
	 * 
	 * @return \API_Response
	 */
	public function as_array()
	{
		$this->_data_type = self::ARR;
		$this->_data = json_decode( $this->body(), TRUE );
		
		return $this;
	}
	
	/**
	 * 
	 * @return \API_Response
	 */
	public function as_object()
	{
		$this->_data_type = self::OBJ;
		$this->_data = json_decode( $this->body() );
		
		return $this;
	}

	/**
	 * 
	 * @return \API_Response
	 */
	public function debug()
	{
		echo debug::vars($this->body());
		return $this;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function code()
	{
		return (int) $this->_code;
	}
	
	/**
	 * 
	 * @return mixed
	 */
	public function error()
	{
		if($this->status()) return NULL;

		return $this->get('message');
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function status()
	{
		return $this->code() == API::NO_ERROR;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function body()
	{
		return $this->_response->body();
	}
	
	public function __toString() 
	{
		return $this->body();
	}
	
	public function __isset($name)
	{
		if( $this->_data_type === NULL )
		{
			$this->as_array();
		}

		if($this->_data_type == self::OBJ)
		{
			return isset($this->_data->{$name});
		}
		else if($this->_data_type == self::ARR)
		{
			return isset($this->_data[$name]);
		}
	}

		/**
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->get($name);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($name, $default = NULL)
	{
		if($this->_data_type == self::OBJ)
		{
			return isset($this->_data->{$name})
				? $this->_data->{$name}
				: $default;
		}
		else if($this->_data_type == self::ARR)
		{
			return Arr::path($this->_data, $name, $default);
		}
		
		return $this->as_array()->get($name, $default);
	}
}