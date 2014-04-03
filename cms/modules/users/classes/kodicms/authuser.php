<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Users
 * @author		ButscHSter
 */
class KodiCMS_AuthUser {
	
	const EMAIL = 'email';
	const USERNAME = 'username';

	/**
	 * 
	 * @return boolean
	 */
	public static function isLoggedIn($role = NULL)
	{
		return Auth::instance()->logged_in($role);
	}

	/**
	 * 
	 * @return Model_User
	 */
	public static function getRecord( $default = FALSE )
	{
		return Auth::instance()->get_user($default);
	}

	/**
	 * 
	 * @return integer
	 */
	public static function getId()
	{
		return self::getRecord() ? self::getRecord()->id : FALSE;
	}

	/**
	 * 
	 * @return string
	 */
	public static function getUserName()
	{
		return self::getRecord() ? self::getRecord()->username : FALSE;
	}

	/**
	 * 
	 * @return array
	 */
	public static function getPermissions()
	{
		$roles = self::getRecord() ? self::getRecord()->roles->find_all() : FALSE;
		
		$array = array();
		if($roles)
		{
			foreach ( $roles as $role )
			{
				$array[$role->id] = $role->name;
			}
		}
		return $array;
	}

	/**
	 * Checks if user has (one of) the required permissions.
	 *
	 * @param string $permission Can contain a single permission or comma seperated list of permissions.
	 * @return boolean
	 */
	public static function hasPermission( $permissions, $all_required = FALSE )
	{
		if(empty($permissions))
		{
			return TRUE;
		}
		
		if(!is_array( $permissions ))
		{
			$permissions = explode(',', $permissions);
		}

		return self::getRecord() ? self::getRecord()->has_role($permissions, $all_required) : FALSE;
	}

	/**
	 * 
	 * @param string $username
	 * @param string $password
	 * @param boolean $remember
	 * @return boolean
	 */
	public static function login( $field, $username, $password, $remember = FALSE )
	{
		$user = ORM::factory( 'user' );
		
		// Attempt to load the user
		$user
			->where( $field, '=', $username )
			->find();
		
		if(
			$user->loaded()
			AND
			Auth::instance()->login($user, $password, $remember))
		{
			return TRUE;
		}

		return FALSE;
	}

	static public function logout()
	{
		Auth::instance()->logout();
		Session::instance()->destroy();
	}

}

// end AuthUser class