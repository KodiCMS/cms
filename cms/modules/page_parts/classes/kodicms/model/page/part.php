<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Page_Part extends Record
{
	const TABLE_NAME = 'page_parts';

	const PART_NOT_PROTECTED = 0;
	const PART_PROTECTED = 1;

	/**
	 *
	 * @var array 
	 */
	protected static $_parts = NULL;

	/**
	 * 
	 * @param Model_Page_Front $page
	 * @param string $part
	 * @param boolean $inherit
	 * @return boolean
	 */
	public static function has_content( Model_Page_Front $page, $part, $inherit = FALSE)
	{
		if(self::$_parts === NULL)
		{
			self::$_parts[$page->id()] = self::_load_parts($page->id());
		}

		if(isset(self::$_parts[$page->id()][$part]))
		{
			return TRUE;
		}
		else if($inherit !== FALSE 
				AND $page->parent() instanceof Model_Page_Front )
		{
			return self::has_content( $this->parent(), $part, TRUE);
		}

		return FALSE;
	}

	/**
	 * 
	 * @param Model_Page_Front $page
	 * @param string $part
	 * @param boolean $inherit
	 * @param integer $cache_lifetime
	 * @return void
	 */
	public static function content( Model_Page_Front $page, $part = 'body', $inherit = FALSE, $cache_lifetime = NULL, array $tags = array())
	{		
		if (self::has_content( $page, $part ))
		{
			$view = self::get( $page->id(), $part);

			if( $cache_lifetime !== NULL 
				AND ! Fragment::load( $page->id() . $part . Request::current()->uri(), (int) $cache_lifetime ))
			{
				echo $view;				

				Fragment::save_with_tags((int) $cache_lifetime, array('page_parts'));
			}
			else if($cache_lifetime === NULL)
			{
				echo $view;
			}

		}
		else if ($inherit !== FALSE
				AND $page->parent() instanceof Model_Page_Front )
		{
			self::content( $page->parent(), $part, TRUE, $cache_lifetime);
		}
	}

	/**
	 * 
	 * @param integer $page_id
	 * @param string $part
	 * @return View
	 */
	public static function get( $page, $part )
	{
		$html = NULL;

		$page_id = ($page instanceof Model_Page_Front) ? $page->id() : (int) $page;

		if( empty(self::$_parts[$page_id][$part]) ) return NULL;

		if( self::$_parts[$page_id][$part] instanceof Model_Page_Part )
		{
			$html = View_Front::factory()
				->bind('page', $page)
				->render_html(self::$_parts[$page_id][$part]->content_html);
		}
		else if( self::$_parts[$page_id][$part] instanceof Kohana_View )
		{
			$html = self::$_parts[$page_id][$part]->render();
		}

		return $html;
	}

	public function defaults()
	{
		return array(
			'name' => 'part',
			'page_id' => 0,
			'is_protected' => self::PART_NOT_PROTECTED
		);
	}

	public function beforeSave()
	{
		if (!empty($this->permissions))
		{
			$this->savePermissions($this->permissions);
		}

		unset($this->permissions);

		// apply filter to save is generated result in the database
		if ( ! empty($this->filter_id))
		{
			if (WYSIWYG::get($this->filter_id))
			{
				$filter_class = WYSIWYG::get($this->filter_id);

				if($filter_class !== FALSE)
				{
					$this->content_html = $filter_class->apply($this->content);
					return TRUE;
				}
			}
		}

		$this->content_html = $this->content;

		return TRUE;
	}

	public static function findByPageId($id)
	{
		return self::findAllFrom('Model_Page_Part', array(
			'where' => array(array('page_id', '=', (int) $id)),
			'order_by' => array(array('id', 'asc'))
		));
	}

	public static function deleteByPageId($page_id)
	{
		$parts = self::findAllFrom('Model_Page_Part', array(
			'where' => array(array('page_id', '=', (int) $page_id))));

		$result = true;

		foreach ($parts as $part)
		{
			if ( !$part->delete())
			{
				$result = FALSE;
			}
		}

		return $result;
	}

	public function is_protected($roles = array( 'administrator', 'developer' ))
	{
		if(
			$this->is_protected == Model_Page_Part::PART_PROTECTED
		AND
			!AuthUser::hasPermission( $roles )
		)
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return array
	 */
	final private static function _load_parts($page_id)
	{
		return DB::select('name', 'content', 'content_html')
			->from(self::tableName())
			->where('page_id', '=', $page_id)
			->cache_tags( array('page_parts') )
			->as_object('Model_Page_Part')
			->cached( (int) Config::get('cache', 'page_parts'))
			->execute()
			->as_array('name');	
	}

} // end Model_Page_Part class