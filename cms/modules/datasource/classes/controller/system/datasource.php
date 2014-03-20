<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

/**
 * @package		KodiCMS
 * @category	Datasource
 */
class Controller_System_Datasource extends Controller_System_Backend
{
	/**
	 *
	 * @var DataSource_Section 
	 */
	protected $_section = NULL;

	public function before()
	{
		parent::before();

		Assets::js('datasource', ADMIN_RESOURCES . 'js/datasource.js', 'global');
		Assets::css('datasource', ADMIN_RESOURCES . 'css/datasource.css', 'global');
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return DataSource_Section
	 * @throws HTTP_Exception_404
	 */
	public function section( $id = NULL )
	{
		if( $this->_section instanceof DataSource_Section) return $this->_section;
		
		if($id === NULL)
		{
			Messages::errors(__('Datasource section not loaded'));
			$this->go_home();
		}
	
		$this->_section = Datasource_Data_Manager::load((int) $id);

		if(empty($this->_section))
		{
			Messages::errors(__('Datasource section :id not found', 
					array(':id' => $id)));

			$this->go_home();
		}
		
		return $this->_section;
	}
}