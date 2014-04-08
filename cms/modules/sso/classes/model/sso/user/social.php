<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @package		KodiCMS/SSO
 * @category	Model
 * @author		ButscHSter
 */
class Model_SSO_User_Social extends ORM {

	protected $_table_name = 'user_social';
	
	protected $_belongs_to = array(
		'user' => array()
	);

	public function provider()
	{
		return strtolower(substr($this->service_type, strpos($this->service_type, '.') + 1));
	}

	public function link()
	{
		$provider = Kohana::$config->load('social.' . $this->provider());

		$name = Arr::get($provider, 'name');
		if( ! Arr::get($provider, 'account_link') ) return $name;
		$link = Arr::get($provider, 'account_link');
		$link = strtr($link, array(':id' => $this->service_name));

		return HTML::anchor($link, $name, array(
			'target' => 'blank'
		));
	}

	/**
	 * Get avatar URL
	 *
	 * @param  int $size  used for gravatar images only
	 *
	 * @return mixed|null|string
	 */
	public function avatar($size = NULL)
	{
		$avatar = $this->avatar;
		if (empty($avatar) AND ! empty($this->email) )
		{
			// use email as Gravatar ID
			$avatar = md5($this->email);
		}

		if (empty($avatar))
		{
			return NULL;
		}

		if (strpos($avatar, '://') == FALSE)
		{
			// its a Gravatar ID
			$avatar = 'http://gravatar.com/avatar/' . $avatar;
			$params = array();
			if (empty($avatar))
			{
				// use default Gravatar
				$params['f'] = 'y';
			}

			if ($size)
			{
				$params['s'] = intval($size);
			}

			if ( ! empty($params) )
			{
				$avatar .= http_build_query($params);
			}
		}

		return $avatar;
	}

}