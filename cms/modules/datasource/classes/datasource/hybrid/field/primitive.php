<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */

class DataSource_Hybrid_Field_Primitive extends DataSource_Hybrid_Field {
	
	const PRIMITIVE_TYPE_DATE = 'date';
	const PRIMITIVE_TYPE_EMAIL = 'email';
	const PRIMITIVE_TYPE_TIME = 'time';
	const PRIMITIVE_TYPE_DATETIME = 'datetime';
	const PRIMITIVE_TYPE_TEXT = 'text';
	const PRIMITIVE_TYPE_HTML = 'html';
	const PRIMITIVE_TYPE_BOOLEAN = 'boolean';
	const PRIMITIVE_TYPE_INTEGER = 'integer';
	const PRIMITIVE_TYPE_FLOAT = 'float';
	const PRIMITIVE_TYPE_STRING = 'string';
	const PRIMITIVE_TYPE_SLUG = 'slug';
	

	protected $_props = array(
		'default' => NULL,
		'min' => NULL, 
		'max' => NULL,
		'length' => 0,
		'allowed_tags' => NULL,
		'regexp' => NULL,
		'isreq' => FALSE,
	);
	
	public static function types()
	{
		return array(
			self::PRIMITIVE_TYPE_STRING		=> __('String'),
			self::PRIMITIVE_TYPE_INTEGER	=> __('Integer'),
			self::PRIMITIVE_TYPE_FLOAT		=> __("Float"),
			self::PRIMITIVE_TYPE_BOOLEAN	=> __('Boolean'),
			self::PRIMITIVE_TYPE_DATE		=> __('Date'),
			self::PRIMITIVE_TYPE_TIME		=> __('Time'),
			self::PRIMITIVE_TYPE_DATETIME	=> __('Datetime'),
			self::PRIMITIVE_TYPE_HTML		=> __('HTML'),
			self::PRIMITIVE_TYPE_TEXT		=> __('Text'),
			self::PRIMITIVE_TYPE_EMAIL		=> __('Email'),
			self::PRIMITIVE_TYPE_SLUG		=> __('Slug')
		);
	}

	public function __construct( $data )
	{
		$this->family = self::TYPE_PRIMITIVE;
		
		parent::__construct( $data );
	}
	
	public function __set($key, $value)
	{
		switch ($key)
		{
			case 'length':
				$value = (int) $value;
				break;
			case 'isreq':
				$value = (bool) $value;
				break;
		}
		
		parent::__set($key, $value);
	}

	public function create() 
	{
		if(parent::create())
		{
			$this->update();
		}

		return $this->id;
	}
	
	public function onCreateDocument($doc) 
	{
		switch($this->type) 
		{
			case self::PRIMITIVE_TYPE_DATE: 
			case self::PRIMITIVE_TYPE_DATETIME:
				$doc->fields[$this->name] = $this->format_date($doc->fields[$this->name]); 
				break;

			case self::PRIMITIVE_TYPE_HTML:
				$doc->fields[$this->name] = $this->clean_html($doc->fields[$this->name]); 
				break;

			case self::PRIMITIVE_TYPE_BOOLEAN:
				$doc->fields[$this->name] = $doc->fields[$this->name] ? 1 : 0; 
				break;

			case self::PRIMITIVE_TYPE_INTEGER:
				$doc->fields[$this->name] = (int) $doc->fields[$this->name];
				break;

			case self::PRIMITIVE_TYPE_FLOAT:
					$doc->fields[$this->name] = (float) $doc->fields[$this->name];
				break;
			
			case self::PRIMITIVE_TYPE_SLUG:
					$doc->fields[$this->name] = URL::title($doc->fields[$this->name]);
				break;
		}
	}
	
	public function onUpdateDocument($old, $new) 
	{
		$this->onCreateDocument($new);

		if(!$this->is_valid($new->fields[$this->name]))
		{
			$new->fields[$this->name] = $old->fields[$this->name];
		}
	}
	
	public function fetch_value($doc) 
	{
		switch($this->type) 
		{
			case self::PRIMITIVE_TYPE_DATE: 
			case self::PRIMITIVE_TYPE_DATETIME:
				$doc->fields[$this->name] = $this->format_date($doc->fields[$this->name]);
				break;
			case self::PRIMITIVE_TYPE_EMAIL:
				$doc->fields[$this->name] = HTML::mailto($doc->fields[$this->name]);
				break;
		}
	}
	
	public function format_date($value, $format = 'Y-m-d') 
	{
		$time = strtotime(!empty($value) ? $value : 'now');
		return $time > 0 
			? date($this->type == self::PRIMITIVE_TYPE_DATE 
				? $format : $format.' H:i:s', $time) 
			: $value;
	}
	
	// TODO Сделать очистку HTML данных
	public function clean_html($html)
	{
		return $html;
	}
	
	public function is_valid($value) 
	{
		switch($this->type) 
		{
			case self::PRIMITIVE_TYPE_DATE: 
			case self::PRIMITIVE_TYPE_DATETIME:
				return Valid::date( $value );
			case self::PRIMITIVE_TYPE_EMAIL:
				return Valid::email( $value );
			case self::PRIMITIVE_TYPE_INTEGER:
				return Valid::digit($value);
			case self::PRIMITIVE_TYPE_FLOAT:
				return Valid::numeric($value);
		}
		
		if(!empty($this->regexp))
		{
			return Valid::regex($value, $this->regexp);
		}

		return TRUE;
	}
	
	public function get_type() 
	{
		switch($this->type) 
		{
			case self::PRIMITIVE_TYPE_BOOLEAN:	return 'TINYINT(1) UNSIGNED NOT NULL';
			case self::PRIMITIVE_TYPE_FLOAT:	return 'FLOAT NOT NULL';
			case self::PRIMITIVE_TYPE_DATE:		return 'DATE NOT NULL';
			case self::PRIMITIVE_TYPE_TIME:		return 'TIME NOT NULL';
			case self::PRIMITIVE_TYPE_DATETIME:	return 'DATETIME NOT NULL';
			
			case self::PRIMITIVE_TYPE_TEXT:
			case self::PRIMITIVE_TYPE_HTML:		return 'TEXT NOT NULL';
			
			case self::PRIMITIVE_TYPE_INTEGER:
				if($this->length < 1 OR $this->length > 11)
				{
					$this->length = 10;
				}

				return 'INT(' . $this->length . ') UNSIGNED NOT NULL';
			
			case self::PRIMITIVE_TYPE_STRING:
				if($this->length < 1 OR $this->length > 255)
				{
					$this->length = 32;
				}

				return 'VARCHAR ('.$this->length.') NOT NULL';
			case self::PRIMITIVE_TYPE_SLUG:
				return 'VARCHAR (255) NOT NULL';
			
			case self::PRIMITIVE_TYPE_EMAIL:
				return 'VARCHAR (50) NOT NULL';
		}

		return NULL; 
	}
}