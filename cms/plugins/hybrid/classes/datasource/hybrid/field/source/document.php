<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Hybrid
 * @category	Field
 * @author		butschster <butschster@gmail.com>
 * @link		http://kodicms.ru
 * @copyright	(c) 2012-2014 butschster
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
class DataSource_Hybrid_Field_Source_Document extends DataSource_Hybrid_Field_Source_OneToOne {

	protected $_is_searchable = FALSE;
	protected $default = 0;
	protected $_props = array(
		'isreq' => TRUE,
		'ds_type' => NULL,
		'one_to_one' => FALSE
	);
	
	/**
	 * return string
	 */
	public function default_value()
	{
		return $this->default;
	}
	
	public function booleans()
	{
		return array('one_to_one');
	}
	
	public function set(array $data)
	{
		if (!empty($data['from_ds']))
		{
			$section = Datasource_Data_Manager::load($data['from_ds']);

			if ($section !== NULL)
			{
				$data['ds_type'] = $section->type();
			}
		}

		return parent::set($data);
	}

	public function onUpdateDocument(DataSource_Hybrid_Document $old = NULL, DataSource_Hybrid_Document $new)
	{
		if ($new->get($this->name) == -1)
		{
			if ($this->one_to_one)
			{
				DataSource_Hybrid_Factory::remove_documents($old->get($this->name));
			}

			$new->set($this->name, NULL);
			return;
		}
	}

	public function onRemoveDocument(DataSource_Hybrid_Document $doc)
	{
		if ($this->one_to_one)
		{
			DataSource_Hybrid_Factory::remove_documents($doc->get($this->name));
		}
	}

	public function convert_value($value)
	{
		$header = DataSource_Hybrid_Field_Utils::get_document_header($this->from_ds, $value);

		return array(
			'id' => $header ? $value : NULL,
			'header' => $header
		);
	}

	public function get_type()
	{
		return 'INT(11) UNSIGNED';
	}
	
	/**
	 * @param Model_Widget_Hybrid
	 * @param array $field
	 * @param array $row
	 * @param string $fid
	 * @return mixed
	 */
	public static function fetch_widget_field($widget, $field, $row, $fid, $recurse)
	{
		$related_widget = NULL;

		if ($recurse > 0 AND isset($widget->doc_fetched_widgets[$fid]))
		{
			$related_widget = self::_fetch_related_widget($widget, $row, $fid, $recurse);
		}

		return ($related_widget !== NULL) 
			? $related_widget 
			: (int) $row[$fid];
	}

	public function fetch_headline_value($value, $document_id)
	{
		if (empty($value))
		{
			return parent::fetch_headline_value($value, $document_id);
		}

		$header = DataSource_Hybrid_Field_Utils::get_document_header($this->from_ds, $value);

		if (!empty($header))
		{
			return HTML::anchor(Route::get('datasources')->uri(array(
					'directory' => 'hybrid',
					'controller' => 'document',
					'action' => 'view'
				)) . URL::query(array(
					'ds_id' => $this->from_ds, 
					'id' => $value
				), FALSE), $header, array(
					'class' => ' popup fancybox.iframe'
				));
		}

		return parent::fetch_headline_value($value, $document_id);
	}

	public function get_query_props(\Database_Query $query, DataSource_Hybrid_Agent $agent)
	{
		parent::get_query_props($query, $agent);

		$query->join(array('ds' . $this->ds_type, 'dss' . $this->id), 'left')
			->on(DataSource_Hybrid_Field::PREFFIX . $this->key, '=', 'dss' . $this->id . '.id')
			->on('dss' . $this->id . '.published', '=', DB::expr( 1 ))
			->select(array('dss'.$this->id.'.header', $this->id . 'header'));
	}
}
