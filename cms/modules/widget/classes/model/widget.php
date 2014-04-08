<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS/Widgets
 * @category	Model
 * @author		ButscHSter
 */
class Model_Widget extends ORM {

	protected $_reload_on_wakeup = FALSE;
	
	protected $_created_column = array(
		'format' => 'Y-m-d H:i:s',
		'column' => 'created_on'
	);

	/**
	 *
	 * @var Model_Widget_Decorator 
	 */
	protected $_code = FALSE;

	public function rules() 
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 100))
			)
		);
	}
	
	public function type()
	{
		$widget_types = Widget_Manager::map();
		
		$type = $this->type;

		foreach($widget_types as $group => $types)
		{
			if(isset($types[$type])) $type = $types[$type];
		}
		
		return $type;
	}
	
	public function code()
	{
		if($this->_code === FALSE)
		{
			try 
			{
				$this->_code = unserialize($this->code);	
			}
			catch (Exception $e) 
			{
				$this->_code = new Model_Widget_HTML();
			}
		}
		
		return $this->_code;
	}
}