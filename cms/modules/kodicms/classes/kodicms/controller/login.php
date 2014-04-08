<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Controller
 * @author		ButscHSter
 */
class KodiCMS_Controller_Login extends Controller_System_Frontend {

	public $template = 'system/frontend';

	public function before()
	{
		parent::before();
		if (
			$this->request->action() != 'logout'
			AND
			AuthUser::isLoggedIn()
		)
		{
			$this->go_home();
		}
	}

	/**
	 * Checks if a user is already logged in, otherwise it $this->gos the user
	 * to the login screen.
	 */
	public function action_login()
	{
		if ( $this->request->method() == Request::POST )
		{
			$this->auto_render = FALSE;
			return $this->_login();
		}

		$this->template->title = __('Login');
		$this->template->content = View::factory( 'system/login' );
		
		$this->template->content->install_data = Session::instance()->get_once('install_data');
	}

	private function _login()
	{
		$array = $this->request->post('login');

		$fieldname = Valid::email( Arr::get($array, 'username') ) 
			? AuthUser::EMAIL : AuthUser::USERNAME;

		$array = Validation::factory( $array )
			->label( 'username', 'Username' )
			->label( 'password', 'Password' )
			->label( 'email', 'Email' )
			->rules( 'username', array(
				array( 'not_empty' )
			) )
			->rules( 'password', array(
				array( 'not_empty' ),
			) );

		// Get the remember login option
		$remember = isset( $array['remember'] );

		if ( $array->check() )
		{
			Observer::notify( 'admin_login_before', $array );

			if ( AuthUser::login( $fieldname, $array['username'], $array['password'], $remember ) )
			{
				Observer::notify( 'admin_login_success', $array['username'] );

				Session::instance()->delete('install_data');
				
				Kohana::$log->add(Log::INFO, ':user login')->write();

				if( $next_url = Flash::get( 'redirect') )
				{
					$this->go($next_url);
				}

				// $this->go to defaut controller and action
				$this->go_backend();
			}
			else
			{
				Observer::notify( 'admin_login_failed', $array['username'] );
				Messages::errors( __('Login failed. Please check your login data and try again.') );
				$array->error( $fieldname, 'incorrect' );
				
				Kohana::$log->add(Log::ALERT, 'Try to login with :field: :value. Incorrect data', 
                    array(
                        ':field' => $fieldname,
                        ':value' => $array['username']
                    ))->write();
			}
		}
		else
		{
			Messages::errors( $array->errors( 'validation' ) );
		}

		$this->go( Route::get('user')->uri(array( 'action' => 'login' ) ) );
	}

	public function action_logout()
	{
		$this->auto_render = FALSE;
		AuthUser::logout();
		Observer::notify('admin_after_logout', AuthUser::getUserName());
		
		if( $next_url = Flash::get( 'redirect') )
		{
			$this->go($next_url);
		}
				
		$this->go_home();
	}

	public function action_forgot()
	{
		if ( $this->request->method() == Request::POST )
		{
			$this->auto_render = FALSE;
			$this->_forgot(Arr::path($_POST, 'forgot.email'));
		}

		$this->template->title = __('Forgot password');
		$this->template->content = View::factory( 'system/forgot' );
	}

	private function _forgot($email)
	{
		if(!Valid::email( $email ))
		{
			Messages::errors( __('Use a valid e-mail address.') );
			$this->go( Route::get('user')->uri(array( 'action' => 'forgot' ) ) );
		}
		
		$user = ORM::factory('user', array(
			'email' => $email
		));
		
		if( ! $user->loaded() )
		{
			Messages::errors( __('No user found!') );
			$this->go( Route::get('user')->uri(array( 'action' => 'forgot' ) ) );
		}
		
		$reflink = ORM::factory( 'user_reflink' )
			->generate($user, Model_User_Reflink::FORGOT_PASSWORD);

		if( ! $reflink )
		{
			Messages::errors(__('Reflink generate error'));
			$this->go_back();
		}
		
		Observer::notify('admin_login_forgot_before', $user);
		
		try
		{
			Email_Type::get('user_request_password')->send(array(
				'username' => $user->username,
				'email' => $user->email,
				'reflink' => ltrim( Route::get( 'reflink' )->uri(array('code' => $reflink) ), '/'),
				'code' => $reflink
			));

			Messages::success( __('Email with reflink send to address set in your profile' ));
		} 
		catch (Exception $e)
		{
			Messages::error( __('Something went wrong' ));
		}

		$this->go( Route::get( 'user')->uri(array('action' => 'login') ) );
	}
}