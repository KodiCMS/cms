<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package Datasource
 * @category Hybrid
 */
class Datasource_Section_Hybrid_Headline extends Datasource_Section_Headline {

	public function fields()
	{
		$this->fields = array(
			'id' => array(
				'name' => 'ID',
				'width' => 50
			),
			'header' => array(
				'name' => 'Header',
				'width' => NULL,
				'type' => 'link'
			),
		);
		
		$fields = DataSource_Hybrid_Field_Factory::get_section_fields($this->_section->id());
		
		foreach($fields as $key => $field)
		{
			if( ! $field->in_headline ) continue;

			$this->fields[$field->name] = array(
				'name' =>  $field->header
			);
		}
		
		$this->fields['date'] = array(
			'name' => 'Date of creation',
			'width' => 150
		);

		return $this->fields;
	}

	public function get( array $ids = NULL )
	{
		$agent = $this->_section->agent();

		$fids = array();

		$documents = array();		

		$results = array(
			'total' => 0,
			'documents' => array()
		);
		
		$pagination = $this->pagination($ids);

		$section_fields = DataSource_Hybrid_Field_Factory::get_section_fields($this->_section->id());

		foreach($section_fields as $key => $field)
		{
			if( ! array_key_exists( $field->name, $this->fields())) continue;
			
			$fids[] = $field->id;
		}

		$query = $agent
			->get_query_props($fids, (array) $this->_sorting)
			->select(array('d.created_on', 'date'))
			->select('dss.name')
			->join(array('datasources', 'dss'))
				->on('d.ds_id', '=', 'dss.id');

		if( ! empty($ids) ) 
		{
			$query->where('d.id', 'in', $ids);
		}

		$query = $this->search_by_keyword($query);

		$result = $query
			->limit($this->limit())
			->offset($this->offset())
			->execute()
			->as_array('id');

		if(count($result) > 0)
		{
			$results['total'] = $pagination->total_items;
			
			foreach ( $result as $id => $row )
			{
				$documents[$id] = array(
					'id' => $id,
					'published' => (bool) $row['published'],
					'header' => $row['header'],
					'date' => Date::format($row['date'])
				);
				
				foreach($section_fields as $field)
				{
					if(isset($row[$field->id]))
					{
						$documents[$id][$field->name] = $field->fetch_headline_value($row[$field->id]);
					}
				}
			}
			
			$results['documents'] = $documents;
		}
		
		return $results;
	}
	
	public function count_total( array $ids = NULL )
	{
		$agent = DataSource_Hybrid_Agent::instance($this->_section->id());

		$query = $this
			->_section
			->agent()
			->get_query_props(array(), $this->_sorting);

		$query = $this->search_by_keyword($query);
		
		if( ! empty($ids) ) 
		{
			$query->where('d.id', 'in', $ids);
		}
		
		return $query->select(array(DB::expr('COUNT(*)'),'total_docs'))
			->execute()
			->get('total_docs');
	}
}