<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */
abstract class Datasource_Object {
	
	const BLOCK_TYPE_PRE	= 'PRE';
	const BLOCK_TYPE_POST	= 'POST';
	
	/**
	 *
	 * @var strimg
	 */
	public $template = NULL;
	
	/**
	 *
	 * @var array
	 */
	public $template_params = array();

	/**
	 *
	 * @var string
	 */
	public $block = NULL;

	/**
	 * 
	 * @param array $params
	 */
	public function render($params = array())
	{
		if(Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('Object render', __CLASS__);
		}

		if($this->template === NULL) 
		{
			$this->template = 'datasource/object/default';
		}
		
		$allow_omments = (bool) Arr::get($params, 'comments');
		
		if(!($this->block == self::BLOCK_TYPE_PRE OR $this->block == self::BLOCK_TYPE_POST)) 
		{
			if($allow_omments) 
			{
				echo "<!--\n\n{Object: {$this->name}}\n\n-->";
			}
		}
		
		$params = Arr::merge($params, $this->template_params);
		
		echo View::factory($this->template, array(
			'args' => $params
		));
		
		if(!($this->block == self::BLOCK_TYPE_PRE OR $this->block == self::BLOCK_TYPE_POST)) 
		{
			if($allow_omments) 
			{
				echo "<!--\n\n{/Object: {$this->name}}\n\n-->";
			}
		}
		
		if(isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
	}
	
	abstract public function init();
	abstract public function on_page_load();

	/**
	 * 
	 * @param array $params
	 */
	public function run($params = array()) 
	{
		return $this->render($params);
	}
	
	public function __toString()
	{
		return $this->render($params);;
	}
}