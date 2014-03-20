<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class KodiCMS_Search_MySQL extends Search {

	/**
	 * 
	 * @param string $keyword
	 * @return string
	 */
	public function stem_query($keyword)
	{
		$result = '';

		$text = UTF8::strtolower($keyword);
		$text = trim( $text );
		$text = strip_tags( $text );

		$stop_words = Model_Search_Stopwords::get();

		// Parse original text and stem all words that are not tags
		$tkn = new Model_Search_Tokenizer();
		$tkn->set_text( $text );
		$tkn->stopwords = $stop_words;
		
		$stemmer = new Model_Search_Stemmer($tkn);

		while ( $cur = $tkn->next() )
		{
			$result .= $stemmer->stem($cur);
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param string $keyword
	 * @param boolean $only_title
	 * @param string $modules
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function find_by_keyword( $keyword, $only_title = FALSE, $modules = NULL, $limit = 50, $offset = 0)
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('Search', __FUNCTION__);
		}

		$query = DB::select('id', 'module', 'title', 'annotation', 'params')
			->from('search_index');

		$result = $this->_get_query( $query, $keyword, $only_title, $modules, $limit, $offset )->execute();
		
		$ids = array();

		foreach($result as $row)
		{
			$ids[$row['module']][] = $row;
		}

		if (isset($benchmark)) 
		{
			Profiler::stop($benchmark);
		}
		
		return $ids;
	}
	
	/**
	 * 
	 * @param string $keyword
	 * @param boolean $only_title
	 * @param string $modules
	 * @return integer
	 */
	public function count_by_keyword( $keyword, $only_title = FALSE, $modules = NULL )
	{
		$query = DB::select(array(DB::expr('COUNT(*)'), 'total'))
			->from('search_index');
		
		return $this->_get_query( $query, $keyword, $only_title, $modules, NULL )
			->execute()
			->get('total');
	}
	
	/**
	 * 
	 * @param string $keyword
	 * @return string
	 */
	public function match_against_query($keyword)
	{
		$keyword = trim(strtolower($keyword));
		$result = '>"' . $keyword . '"<';
		
		$words = explode(' ', $keyword);

		if(count($words) > 1 )
		{
			$result .= '(';
			foreach ($words as $word )
			{
				$result .= '+' . $word;
			}
			
			$result .= ') ';
		}
			
		return $result;
	}
	
	/**
	 * 
	 * @param Database_Query $query
	 * @param string $keyword
	 * @param string $module
	 * @param bool $only_title
	 */
	public function get_module_query( Database_Query $query, $keyword, $module, $only_title = FALSE )
	{
		return $this->_get_query($query, $keyword, $only_title, $module, NULL)
			->join('search_index', 'left')
				->on('search_index.id', '=', 'd.id');
	}

	/**
	 * 
	 * @param string $keyword
	 * @param boolean $only_title
	 * @param string $modules
	 * @param integer $limit
	 * @param integer $offset
	 * @param boolean $fulltextsearch
	 * @return Database_Query
	 */
	protected function _get_query(Database_Query $query, $keyword, $only_title = FALSE, $modules = NULL, $limit = 50, $offset = 0 )
	{
		$keyword = $this->stem_query($keyword);
		
		if($limit !== NULL)
		{
			$query
				->limit((int) $limit)
				->offset($offset);
		}
		
		if(is_array($modules))
		{
			$query->where('search_index.module', 'in', $modules);
		}
		elseif ($modules !== NULL) 
		{
			$query->where('search_index.module', '=', $modules);
		}
		
		if($this->config('full_text_search') === TRUE)
		{
			$query->where(DB::expr('MATCH(`search_index`.`header`, `search_index`.`content`)'), 'AGAINST', DB::expr("('".self::match_against_query($keyword)."' IN BOOLEAN MODE)"));
		}
		else
		{
			$words = explode(' ', $keyword);

			$query->where_open();
				$query->where_open();

				foreach ($words as $word )
				{
					if(!empty($word))
						$query->where('search_index.header', 'like', '%'.$word.'%');
				}

				$query->where_close();

				if($only_title === FALSE)
				{
					$query->or_where_open();
					foreach ($words as $word )
					{
						if(!empty($word))
							$query->where('search_index.content', 'like', '%'.$word.'%');
					}
					$query->where_close();
				}
			$query->where_close();
		}
		
		return $query;
	}

	/**
	 * 
	 * @param string $module
	 * @param integer $id
	 * @param string $title
	 * @param string $content
	 * @param array $params
	 * @return bool
	 */
	public function add_to_index( $module, $id, $title, $content = '', $annotation, $params = array() ) 
	{
		$indexer = new Model_Search_Indexer;
		return $indexer->add($module, $id, $title, $content, $annotation, $params);
	}
	
	/**
	 * 
	 * @param string $module
	 * @param integer $id
	 * @return bool
	 */
	public function remove_from_index( $module, $id = NULL ) 
	{
		$indexer = new Model_Search_Indexer;
		return $indexer->remove( $module, $id );
	}
}