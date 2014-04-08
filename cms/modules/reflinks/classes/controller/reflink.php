<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Reflink
 * @category	Controller
 * @author		ButscHSter
 */
class Controller_Reflink extends Controller_System_Controller {

	public function action_index()
	{
		$code = $this->request->param( 'code' );
		if ( $code === NULL )
		{
			Model_Page_Front::not_found();
		}

		$reflink = ORM::factory( 'user_reflink', $code );

		try
		{
			Database::instance()->begin();
			
			$status = $reflink->confirm();

			switch ( $reflink->type )
			{
				case Model_User_Reflink::FORGOT_PASSWORD:
					$this->_forgot($reflink, $status);
					break;
			}
			
			Database::instance()->commit();
		}
		catch ( Kohana_Exception $e )
		{
			Database::instance()->rollback();
			Messages::errors( $e->getMessage() );
		}
		
		$this->go( Route::get('user')->uri(array( 'action' => 'login' ) ) );
	}
	
	protected function _forgot($reflink, $new_password)
	{
		try
		{
			Email_Type::get('user_new_password')->send(array(
				'username' => $reflink->user->username,
				'email' => $reflink->user->email,
				'password' => $new_password
			));
			
			Messages::success(__('An email has been send with your new password!'));
			return $reflink->delete();
		}
		catch ( Kohana_Exception $e )
		{
			throw new Reflink_Exception('Email :email not send', array(
				':email' => $reflink->user->email));
		}
	}
}