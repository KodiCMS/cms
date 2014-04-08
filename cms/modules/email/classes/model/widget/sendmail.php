<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	Other
 * @author		ButscHSter
 */
class Model_Widget_SendMail extends Model_Widget_Decorator {

	const CTX = 0;
	const GET = 1;
	const POST = 2;
	const COOKIE = 3;
	const SESSION = 4;

	const TXT = 10;
	const HTML = 20;

	protected $_errors = array();
	protected $_values = array();
	
	public $use_template = FALSE;
	public $use_caching = FALSE;

	public $fields = array();
	
	protected $_data = array(
		'allowed_tags' => '<b><i><u><p>',
		'next_url' => NULL
	);

	public function src_types()
	{
		return array(
			self::CTX => __('Context'),
			self::GET => __('GET array'),
			self::POST => __('POST array'),
			self::COOKIE => __('Cookie'),
			self::SESSION => __('Session'),
		);
	}
	
	public function value_types()
	{
		return array(
			self::TXT => __('Plain text'),
			self::HTML => __('HTML')
		);
	}
	
	public function get_code()
	{
		return 'wiget_sendmail_' . $this->id;
	}

	public function set_values(array $data)
	{
		if( ! Valid::url($data['next_url']))
		{
			$data['next_url'] = NULL;
		}

		$data['fields'] = array();
		if(!empty($data['field']) AND is_array( $data['field'] ))
		{
			foreach($data['field'] as $key => $values)
			{
				foreach($values as $index => $value )
				{
					if($index == 0)	continue;

					if($key == 'source')
					{
						$value = URL::title($value, '_');
					}

					$data['fields'][$index][$key] = $value;
				}
			}

			$data['field'] = NULL;
		}

		$email_type = ORM::factory('email_type', array('code' => $this->get_code()));
		
		if(!$email_type->loaded())
		{
			$email_type->values(array(
				'code' => $this->get_code(),
				'name' => $this->name
			))->create();
		}
		
		$email_type_fields = array();
		foreach ($data['fields'] as $field)
		{
			$email_type_fields['key'][] = $field['id'];
			$email_type_fields['value'][] = ! empty($field['name']) ? $field['name'] : Inflector::humanize($field['id']);
		}

		$email_type->set('data', $email_type_fields)->update();
		
		return parent::set_values($data);
	}

	public function fetch_data()
	{
		return array();
	}

	public function on_page_load() 
	{
		$this->_errors = array();

		$this->_fetch_fields();
		
		$next_url = $this->next_url;
		
		if(Request::current()->is_ajax())
		{
			$json = array('status' => FALSE);
			
			if( !empty($this->_errors))
			{
				$json['errors'] = $this->_errors;
				$json['values'] = $this->_values;
			} 
			else if( $this->send_message() )
			{
				$json = array('status' => TRUE);
			}
			
			Request::current()->headers( 'Content-type', 'application/json' );		
			$this->_ctx->response()->body(json_encode($json));
		}
		else
		{
			$referrer = Request::current()->referrer();
	
			if( !empty($this->_errors))
			{
				Flash::set('form_errors', $this->_errors);
				Flash::set('form_values', $this->_values);

				$query = URL::query(array('status' => 'error'), FALSE);
				$next_url = $referrer;
			} 
			else if( $this->send_message() )
			{
				$query = URL::query(array('status' => 'ok'), FALSE);
				
				if(empty($next_url))
				{
					$next_url = $referrer;
				}
			}
			else
			{
				$query = URL::query(array('status' => 'error'), FALSE);
				$next_url = $referrer;
			}

			
			HTTP::redirect( preg_replace('/\?.*/', '', $next_url) . $query, 302);
		}
	}

	public function send_message()
	{
		return Email_Type::get($this->get_code())->send($this->_values);
	}

	protected function _fetch_fields( ) 
	{
		foreach($this->fields as $field) 
		{
			$value = $this->_get_field_value($field);
			$field_name = !empty($field['name']) ? $field['name'] : $field['id'];
			
			$this->_values[$field['id']] = $value;
	
			if( ! empty($field['validator']))
			{
				$validations = explode(',', $field['validator']);
				
				foreach($validations as $validator)
				{
					$validator = trim($validator);

					if( strpos( $validator, '::' ) !== FALSE )
					{
						list($class, $method) = explode('::', $validator);
					}
					else
					{
						$class = 'Valid';
						$method = $validator;
					}
					
					if( $class.'::'.$method != 'Valid::not_empty' AND empty($value) )
					{
						continue;
					}

					if( method_exists($class, $method) )
					{
						if( ! call_user_func_array($class . '::' . $method, array($value)))
						{
							$message = Kohana::message('validation', $method);
							$this->_errors[$field['id']][] = array(
								'value' => $value,
								'message' => !empty($field['error']) ? $field['error'] : __($message, array(':field' => $field_name))
							);
						}
					}
					else if( ! preg_match($validator, $value) )
					{
						$this->_errors[$field['id']][] = array(
							'value' => $value,
							'message' => !empty($field['error']) ? $field['error'] : NULL
						);
					}
				}
			}

			unset($field);
		}
	}

	protected function _get_field_value( $field ) 
	{
		$key = $field['id'];
		$value = NULL;

		$src = $field['source'] % 10;
		switch($src) 
		{
			case self::CTX:
				$value = Context::instance()->get($key);
				break;
			case self::GET:
				$value = Request::current()->query($key);	
				break;
			case self::POST:
				$value = Request::current()->post($key);	
				break;
			case self::COOKIE:
				$value = Cookie::get($key);	
				break;
			case self::SESSION:
				$value = Session::instance()->get($key);
		}

		switch($field['type']) 
		{
			case self::HTML:	
				$value = Kses::filter( $value, $this->allowed_tags );
				break;
			case self::TXT:		
				$value = HTML::chars($value);
		}

		return $value;
	}
	
	public function render( array $params = array( ) )
	{
		return NULL;
	}
}