<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Widgets
 * @category	SSO
 * @author		ButscHSter
 */
class Model_Widget_Social_Registration extends Model_Widget_Decorator {
	
	public $providers = array();

	public function fetch_data()
	{
		return array(
			'providers' => $this->get_checked_providers()
		);
	}
	
	public function get_providers()
	{
		return Kohana::$config->load('oauth')->as_array();
	}
	
	public function get_provider_param($name, $key = NULL)
	{
		if( $key !== NULL ) $key = '.' . $key;
		$params = Kohana::$config->load('social')->as_array();
		return Arr::path($params, $name.$key);
	}

	public function get_registered_providers()
	{
		$providers = array();
		foreach ($this->get_providers() as $provider => $data)
		{
			 if( (isset($data['id']) AND empty($data['id']))
				OR
					(isset($data['key']) AND empty($data['key']))		
				OR 
					empty($data['secret'])
				)
				continue;

			 $providers[$provider] = $data;
		}
		
		return $providers;
	}
	
	public function get_url($provider)
	{
		return Route::get('accounts-auth')->uri(array(
			'directory' => 'oauth', 
			'controller' => $provider, 
			'action' => 'register')
		);
	}

	public function get_checked_providers()
	{
		$providers = $this->get_registered_providers();
	
		$checked = array();
		
		if(!empty($this->providers))
		{
			foreach($this->providers as $provider => $value)
			{
				if(isset($providers[$provider]))
				{
					$checked[$provider] = array(
						'name' => $this->get_provider_param($provider, 'name'),
						'uri' => $this->get_url( $provider )
					);
				}
			}
		}
		
		return $checked;
	}
}