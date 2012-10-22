<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package    Kodi/Datasource
 */
class DataSource_Hybrid_Field_Datasource extends DataSource_Hybrid_Field {

	public $from_ds = NULL;
	
	protected $_props = array(
		'isreq' => TRUE
	);
	
	public function __construct( $data )
	{
		parent::__construct( $data );
		
		$this->family = self::TYPE_DATASOURCE;
		$this->from_ds = (int) $this->from_ds;
	}
	
	public function create() 
	{
		if(parent::create())
		{
			$this->update();
		}

		$ds = DataSource_Hybrid_Field_Utils::load_ds($this->from_ds);
		$ds->increase_lock();
		
		return $this->id;
	}
	
	public function update() 
	{
		return DB::update($this->table)
			->set(array(
				'header' => $this->header,
				'props' => serialize($this->_props),
				'from_ds' => $this->from_ds
			))
			->where('id', '=', $this->id)
			->execute();
	}
	
	public function remove() 
	{
		$ds = DataSource_Hybrid_Field_Utils::load_ds($this->from_ds);
		$ds->decrease_lock();

		parent::remove();
	}
	
	public function convert_to_plain($doc) 
	{
		$doc->fields[$this->name] = NULL;
	}
	
	public function get_type()
	{
		return 'INT(11) UNSIGNED';
	}
}