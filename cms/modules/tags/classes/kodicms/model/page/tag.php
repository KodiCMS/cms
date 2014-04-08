<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS
 * @category	Model
 * @author		ButscHSter
 */
class KodiCMS_Model_Page_Tag extends Record
{
	const TABLE_NAME = 'page_tags';

	/**
	 * 
	 * @param integer $page_id
	 * @return array
	 */
	public static function find_by_page($page_id)
	{
		return DB::select(array('tags.id', 'id'), array('tags.name', 'tag'))
			->from(array(self::tableName(), 'page_tags'))
			->join(array(self::tableName('Model_Tag'), 'tags'), 'left')
				->on('page_tags.tag_id', '=', 'tags.id')
			->where('page_tags.page_id', '=', (int) $page_id)
			->cache_tags( array('page_tags') )
			->cached( (int) Config::get('cache', 'tags') )
			->execute()
			->as_array('id', 'tag');
	}

	/**
	 * 
	 * @param integer $page_id
	 * @param array $tags
	 */
	public static function save_by_page($page_id, $tags)
	{
		if( is_string($tags) )
		{
			$tags = explode(Model_Tag::SEPARATOR, $tags);
		}

		$tags = array_unique(array_map('trim', $tags));

		$current_tags = Model_Page_Tag::find_by_page($page_id);

		// no tag before! no tag now! ... nothing to do!
		if( empty($tags) AND empty($current_tags) )
		{
			return NULL;
		}

		// delete all tags
		if( empty($tags) )
		{
			// update count (-1) of those tags
			foreach( $current_tags as $tag )
			{
				DB::update(Model_Tag::tableName())
					->set(array('count' => DB::expr('count - 1')))
					->where('name', '=', $tag)
					->execute();
			}

			Record::deleteWhere( self::tableName(), array(
				'where' => array(array('page_id', '=', (int) $page_id))));
			
			Cache::instance()->delete_tag('page_tags');
		}
		else
		{
			$old_tags = array_diff($current_tags, $tags);
			$new_tags = array_diff($tags, $current_tags);

			// insert all tags in the tag table and then populate the page_tag table
			foreach( $new_tags as $index => $tag_name )
			{
				if ( empty($tag_name) )	continue;

				$tag = Record::findOneFrom('Model_Tag', array(
					'where' => array(
						array('name', '=', $tag_name)
					)
				));

				// try to get it from tag list, if not we add it to the list
				if ( !($tag instanceof Model_Tag))
				{
					$tag = new Model_Tag(array('name' => trim($tag_name)));
				}

				$tag->count++;
				$tag->save();

				// create the relation between the page and the tag
				$page_tag = new Model_Page_Tag( array('page_id' => (int) $page_id, 'tag_id' => $tag->id) );
				$page_tag->save();

			}

			// remove all old tag
			foreach( $old_tags as $index => $tag_name )
			{
				// get the id of the tag
				$tag = Record::findOneFrom('Model_Tag',
						array('where' => array(array('name', '=', $tag_name))));

				Record::deleteWhere( self::tableName(), array(
					'where' => array(
						array('page_id', '=', (int) $page_id ),
						array('tag_id', '=', $tag->id)
					)));

				$tag->count--;
				$tag->save();
			}

			Cache::instance()->delete_tag('page_tags');
		}
	}
} // end class KodiCMS_Model_Page_Tag