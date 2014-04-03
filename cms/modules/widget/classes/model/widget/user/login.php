<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	User
 * @author		ButscHSter
 */
class Model_Widget_User_Login extends Model_Widget_Decorator {
	
	public $use_template = FALSE;
	
	protected $_data = array(
		'login_field' => 'username',
		'password_field' => 'password',
		'remember_field' => 'remember',
		'next_url' => '/',
		'remember' => TRUE,
		'roles_redirect' => array(
			array(
				'roles' => array(),
				'next_url' => '/'
			)
		)
	);
	
	public function backend_data()
	{
		$roles = Model_Permission::get_all();
		
		$_roles = array();
		foreach($roles as $role)
		{
			$_roles[$role] = $role;
		}
		return array(
			'roles' => $_roles
		);
	}
	
	public function set_values(array $data)
	{
		$data['remember'] = empty($data['remember']) ? FALSE : (bool) $data['remember'];
		
		if(empty($data['roles_redirect']) OR !is_array($data['roles_redirect']))
		{
			$data['roles_redirect'] =  $this->get_default_roles();
		}
		else
		{
			$roles = array();
			foreach($data['roles_redirect'] as $data)
			{
				if(empty($data['roles']) OR empty($data['next_url'])) continue;
				
				$roles[] = array(
					'roles' => $data['roles'],
					'next_url' => $data['next_url']
				);
			}
			
			if(empty($roles))
				$roles = $this->get_default_roles();
			$data['roles_redirect'] = $roles;
		}

		return parent::set_values($data);
	}
	
	public function get_default_roles()
	{
		return array(
			array(
				'roles' => array(),
				'next_url' => '/'
			)
		);
	}

	public function fetch_data() {}
	
	public function render( array $params = array() ) {}

	public function on_page_load()
	{
		if(Request::current()->method() !== Request::POST) return;

		$data = Request::current()->post();
		
		$login_fieldname = Valid::email( Arr::get($data, $this->get( 'login_field' )) ) 
			? AuthUser::EMAIL 
			: AuthUser::USERNAME;
		
		$data = Validation::factory( $data )
			->label( $this->get( 'login_field' ), 'Username' )
			->label( $this->get( 'password_field' ), 'Password' )
			->rules( 'username', array(
				array( 'not_empty' )
			) )
			->rules( 'password', array(
				array( 'not_empty' ),
			) );
		
		// Get the remember login option
		$remember = isset( $data[$this->get( 'remember_field' )] ) AND $this->get( 'remember' ) === TRUE;
		
		return Request::current()->is_ajax() ? $this->_ajax_login($data, $login_fieldname, $remember) : $this->_login($data, $login_fieldname, $remember);
	}
	
	protected function _ajax_login(Validation $validation, $login_fieldname, $remember = FALSE)
	{
		$json = array('status' => FALSE);
		
		if ( $validation->check() )
		{
			if ( AuthUser::login( $login_fieldname, $validation[$this->get( 'login_field' )], $validation[$this->get( 'password_field' )], $remember ) )
			{
				$json['status'] = TRUE;
				$json['redirect'] = $this->get_next_url();
			}
			else
			{
				$json['message'] = __('Login failed. Please check your login data and try again.');
			}			
		}
		else
		{
			$json['message'] = $validation->errors( 'validation' );
		}
		
		Request::current()->headers( 'Content-type', 'application/json' );		
		$this->_ctx->response()->body(json_encode($json));
	}

	protected function _login(Validation $validation, $remember)
	{
		if ( $validation->check() )
		{
			if ( AuthUser::login( $login_fieldname, $validation[$this->get( 'login_field' )], $validation[$this->get( 'password_field' )], $remember ) )
			{
				HTTP::redirect($this->get_next_url());
			}
			else
			{
				Messages::errors( __('Login failed. Please check your login data and try again.') );
			}
		}
		

		HTTP::redirect( Request::current()->referrer() );
	}

	public function get_next_url()
	{
		$next_url = $this->get('next_url', Request::current()->referrer());
		
		$roles_redirect = $this->get('roles_redirect', array());
		if( empty($roles_redirect) OR !is_array($roles_redirect))
		{
			return $next_url;
		}

		foreach($roles_redirect as $data)
		{
			if( AuthUser::hasPermission( $data['roles'] ))
			{
				return $data['next_url'];
			}
		}
		
		return $next_url;
	}
}