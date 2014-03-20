<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Widget_Hybrid_Headline extends Model_Widget_Hybrid {
	/**
	 *
	 * @var array 
	 */
	public $doc_fields = array();
	
	/**
	 *
	 * @var array 
	 */
	public $doc_fetched_widgets = array();
	
	/**
	 *
	 * @var array 
	 */
	public $doc_filter = array();
	
	/**
	 *
	 * @var array 
	 */
	public $doc_order = array();
	
	/**
	 *
	 * @var string 
	 */
	public $doc_uri = NULL;
	
	/**
	 *
	 * @var string 
	 */
	public $doc_id = 'id';
	
	/**
	 *
	 * @var bool 
	 */
	public $only_published = TRUE;
	
	/**
	 *
	 * @var array 
	 */
	public $ids = array();
	
	/**
	 *
	 * @var array 
	 */
	protected $arrays = array();
	
	/**
	 *
	 * @var array 
	 */
	public $docs = NULL;

	/**
	 * 
	 * @param array $data
	 */
	public function set_values(array $data) 
	{
		$this->doc_fields = $this->doc_fetched_widgets = array();
		$this->doc_filter =  array();
		
		parent::set_values($data);
		$this->doc_order = Arr::get($data, 'doc_order', array());
		
		$this->only_sub = (bool) Arr::get($data, 'only_sub');
		$this->only_published = (bool) Arr::get($data, 'only_published');
		
		$this->doc_uri = Arr::get($data, 'doc_uri', $this->doc_uri);
		$this->doc_id = preg_replace('/[^A-Za-z,]+/', '', Arr::get($data, 'doc_id', $this->doc_id));
		
		$this->throw_404 = (bool) Arr::get($data, 'throw_404');
		$this->sort_by_rand = (bool) Arr::get($data, 'sort_by_rand');
		
		return $this;
	}
	
	public function set_ds_id($ds_id)
	{
		return (int) $ds_id;
	}
	
	public function set_field($fields = array())
	{
		if(!is_array( $fields)) return;
		foreach($fields as $f)
		{
			if(isset($f['id']))
			{
				$this->doc_fields[] = (int) $f['id'];
			
				if(isset($f['fetcher']))
					$this->doc_fetched_widgets[(int) $f['id']] = (int) $f['fetcher'];
			}
		}
	}
	
	public function set_doc_filter(array $filters)
	{
		$data = array();
		foreach($filters as $key => $rows)
		{
			foreach ($rows as $i => $row)
			{
				$data[$i][$key] = $row;
			}
		}
		
		return $data;
	}
	
	public function count_total()
	{
		return $this->get_total_documents();
	}

	/**
	 * 
	 * @return array
	 */
	public function fetch_data()
	{
		if( ! $this->ds_id ) return array();
		
		$this->get_documents();
		
		if(empty($this->docs) AND $this->throw_404)
		{
			$this->_ctx->throw_404();
		}
		
		return array(
			'docs' => $this->docs,
			'count' => count($this->docs)
		);
	}
	
	public function get_total_documents()
	{
		$agent = $this->get_agent();

		$query = $agent->get_query_props(array(), array(), $this->doc_filter);
		$query = $this->_search_by_keyword($query);
		
		if(is_array($this->ids) AND count($this->ids) > 0)
		{
			$query->where('d.id', 'in',  $this->ids);
		}
		
		if($this->only_published === TRUE)
		{
			$query->where('d.published', '=',  1);
		}
		
		return $query->select(array(DB::expr('COUNT(*)'),'total_docs'))
			->execute()
			->get('total_docs');
	}
	
	/**
	 * 
	 * @param integer $recurse
	 * @return array
	 */
	public function get_documents( $recurse = 3 )
	{
		if( $this->docs !== NULL ) return $this->docs;

		$result = array();
		
		$agent = $this->get_agent();

		if( ! $agent )
		{
			return $result;
		}
		
		$query = $this
			->_get_query();
		
		$ds_fields = $agent->get_fields();
		$fields = array();
		foreach ($this->doc_fields as $fid)
		{
			if(isset($ds_fields[$fid]))
			{
				$fields[$fid] = $ds_fields[$fid];
			}
		}

		$href_params = $this->_parse_doc_id();
		
		foreach ($query->execute() as $row)
		{
			$result[$row['id']] = array();
			$doc = & $result[$row['id']];
			
			$doc['id'] = $row['id'];
			$doc['header'] = $row['header'];
			$doc['created_on'] = $row['created_on'];
			
			foreach ($fields as $fid => $field)
			{
				$related_widget = NULL;
				
				$field_class = 'DataSource_Hybrid_Field_' . $field->type;
				$field_class_method = 'fetch_widget_field';
				if( class_exists($field_class) AND method_exists( $field_class, $field_class_method ))
				{
					$doc[$field->key] = call_user_func_array($field_class.'::'.$field_class_method, array( $this, $field, $row, $fid, $recurse - 1));
					continue;
				}
			}
			
			$doc_params = array();
			foreach ($href_params as $field)
			{
				if(!isset($doc[$field]))
				{
					continue;
				}
				
				$doc_params[] = $doc[$field];
			}
			
			$doc['href'] = URL::site($this->doc_uri . implode( '/' , $doc_params ));
		}
		
		$this->docs = $result;
		return $result;
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _parse_doc_id()
	{
		return explode(',', $this->doc_id);
	}
	
	/**
	 * 
	 * @param Database_Query $query
	 */
	protected function _search_by_keyword( Database_Query $query )
	{
		if($this->search_key === NULL OR trim($this->search_key) == '') return $query;

		$keyword = $this->_ctx->get($this->search_key);
		
		if( empty($keyword) )return $query;
		
		return Search::instance()->get_module_query($query, $keyword, 'ds_' . $this->ds_id);
	}

	/**
	 * 
	 * @return Database_Query_Builder
	 */
	protected function _get_query()
	{
		$agent = $this->get_agent();

		$query = $agent->get_query_props($this->doc_fields, $this->doc_order, $this->doc_filter);
		
		$query = $this->_search_by_keyword($query);
		
		if(is_array($this->ids) AND count($this->ids) > 0)
		{
			$query->where('d.id', 'in',  $this->ids);
		}
		
		if($this->only_published === TRUE)
		{
			$query->where('d.published', '=',  1);
		}

		$query->limit($this->list_size);
		$query->offset($this->list_offset);
		
		return $query;
	}
	
	public function get_cache_id()
	{
		return 'Widget::' 
			. $this->type . '::' 
			. $this->id . '::' 
			. $this->list_offset . '::' 
			. $this->list_size;
	}
}