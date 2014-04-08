<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Widget_Hybrid_Document extends Model_Widget_Hybrid {
	
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
	 * @var string 
	 */
	public $doc_id_field = 'id';

	/**
	 *
	 * @var bool 
	 */
	public $crumbs = FALSE;
		
	/**
	 *
	 * @var array
	 */
	public $document = array();
	
	/**
	 *
	 * @var integer 
	 */
	protected $_id = NULL;
	
	/**
	 *
	 * @var string 
	 */
	public $doc_id_ctx = 'item';

	/**
	 * 
	 * @param array $data
	 */
	public function set_values(array $data) 
	{
		$this->doc_fields = $this->doc_fetched_widgets = array();
		
		parent::set_values($data);
		
		$this->docs_uri = Arr::get($data, 'docs_uri', $this->docs_uri);
		$this->doc_id_field = Arr::get($data, 'doc_id_field', $this->doc_id_field);

		$doc_id_ctx = Arr::get($data, 'doc_id_ctx');
		$this->doc_id_ctx = empty($doc_id_ctx) ? $this->doc_id_ctx : $doc_id_ctx;
		
		$this->throw_404 = (bool) Arr::get($data, 'throw_404');
		$this->crumbs = (bool) Arr::get($data, 'crumbs');
		$this->seo_information = (bool) Arr::get($data, 'seo_information');
		
		return $this;
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
		
		return $this;
	}
	
	public function get_doc_ids()
	{
		$data = array('ID');
		
		$fields = DataSource_Hybrid_Field_Factory::get_section_fields($this->ds_id);
		foreach ($fields as $field)
		{
			if($field->use_as_document_id())
			{
				$data[$field->id] = $field->header;
			}
		}
		return $data;
	}

	public function on_page_load()
	{
		parent::on_page_load();
		
		$doc = $this->get_document();
		
		$page = $this->_ctx->get_page();

		if($this->seo_information === TRUE)
		{
			$page->title = $doc['header'];
			$page->meta_title = $doc['meta_title'];
			$page->meta_keywords = $doc['meta_keywords'];
			$page->meta_description = $doc['meta_description'];
		}
	}
	
	public function change_crumbs( Breadcrumbs &$crumbs )
	{
		parent::change_crumbs( $crumbs );
		$page = $this->_ctx->get_page();
		$doc = $this->get_document();
		
		$crumb = $crumbs->get_by('url', $page->url);
		
		if($crumb !== NULL)
		{
			$crumb->name = $doc['header'];
		}
	}

	public function fetch_data()
	{
		$result = array();
		
		if(!$this->ds_id) return $result;
		
		$result = $this->get_document();
		
		return array(
			'doc' => $result
		);
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return array
	 */
	public function get_document($id = NULL, $recurse = 3)
	{
		$result = array();
		
		if($id === NULL)
		{
			$id = $this->get_doc_id();
		}
		
		if(empty($id)) 
		{
			return $result;
		}
		
		if(isset($this->document[$id]))
		{
			return $this->document[$id];
		}
		
		$agent = $this->get_agent();
		$query = $agent->get_query_props( $this->doc_fields );
		$fields = $agent->get_fields();
		
		if(isset($fields[$this->doc_id_field]))
		{
			$id_field = $fields[$this->doc_id_field]->name;
		}
		else
		{
			$id_field = 'ds.id';
		}
		
		$result = $query->where($id_field, '=', $id)
			->where('d.published', '=', 1)
			->group_by('d.id')
			->limit(1)
			->execute()
			->current();
		
		if(empty($result) )
		{	
			if($this->throw_404)
			{
				$this->_ctx->throw_404();
			}
			
			return $result;
		}

		foreach ($result as $key => $value)
		{
			if( ! isset($fields[$key])) continue;

			$field = & $fields[$key];
			$related_widget = NULL;
				
			$field_class = 'DataSource_Hybrid_Field_' . $field->type;
			$field_class_method = 'fetch_widget_field';

			if( class_exists($field_class) AND method_exists( $field_class, $field_class_method ))
			{
				$result['_' . $field->key] = $result[$key];
				$result[$field->key] = call_user_func_array($field_class.'::'.$field_class_method, array( $this, $field, $result, $key, $recurse));
			}
			
			unset($result[$key]);
		}
		
		$this->document[$id] = $result;
		
		return $result;
	}
	
	/**
	 * 
	 * @param integer $id
	 */
	public function set_doc_id($id)
	{
		$this->_id = (int) $id;
	}

	public function get_doc_id()
	{
		if( Valid::numeric($this->_id) )
		{
			return $this->_id;
		}

		return $this->_ctx->get($this->doc_id_ctx);
	}
	
	public function get_cache_id()
	{
		if(IS_BACKEND) return;

		return 'Widget::' 
			. $this->type . '::' 
			. $this->id . '::' 
			. $this->get_doc_id();
	}
	
	public function count_total()
	{
		return 1;
	}
	
	public function fetch_backend_content()
	{
		try
		{
			$content = View::factory( 'widgets/backend/' . $this->backend_template(), array(
					'widget' => $this
				))->set($this->backend_data());
		}
		catch( Kohana_Exception $e)
		{
			$content = NULL;
		}
		
		return $content;
	}
	
	public function __sleep()
	{
		$vars = get_object_vars($this);
		unset($vars['_id'], $vars['document'], $vars['_ctx']);

		return array_keys($vars);
	}
}