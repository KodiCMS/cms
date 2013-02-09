<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_System_Plugin extends Controller_Plugins {
	
	public $plugin;
	
	public $plugin_id = NULL;

	public function before()
	{
		parent::before();
		
		if($this->plugin_id === NULL)
		{
			$this->plugin_id = strtolower($this->request->controller());
		}

		if ( !Plugins::is_enabled( $this->plugin_id ) )
		{
			throw new Kohana_Exception( 'Plugin not activated' );
		}
		
		$this->plugin = Plugins::get_registered( $this->plugin_id );
		
		$this->template->title = __('Plugins');
		$this->breadcrumbs
			->add($this->template->title, 'plugins')
			->add(__($this->plugin->title));
			
	}
	
	public function display($view, $vars = NULL)
	{
		return View::factory($this->plugin->id . '/' . $view, $vars);
	}

	public function action_settings()
	{
		if ( $this->request->method() == Request::POST )
		{
			return $this->_settings_save( $this->plugin );
		}

		$this->template->content = View::factory('plugins/settings', array(
			'content' => View::factory($this->plugin->id.'/settings', array(
				'plugin' => $this->plugin
			))
		));
		
		$this->template->title = __('Plugin settings');
		$this->breadcrumbs
			->add($this->template->title, $this->plugin->id.'/settings');
	}

	protected function _settings_save( $plugin )
	{
		$data = Arr::get( $_POST,  'setting' );
		Plugins_Settings::set_all_settings( $data, $plugin->id );
		
		// save and quit or save and continue editing?
		if ( $this->request->post('commit') !== NULL )
		{
			$this->go( 'plugins' );
		}
		else
		{
			$this->go_back();
		}
	}
	
} // end PluginController class