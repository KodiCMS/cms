<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @package		KodiCMS/Archive
 * @category	Behavior
 * @author		ButscHSter
 */
class Behavior_Archive extends Behavior_Abstract
{
	public function routes()
	{
		return array(
			'/<year>/<month>/<day>/<slug>' => array(
				'regex' => array(
					'year' => '[0-9]{4}',
					'month' => '[0-9]{2}',
					'day' => '[0-9]{2}'
				),
				'method' => '_display_page'
			),
			'/<year>/<month>/<day>' => array(
				'regex' => array(
					'year' => '[0-9]{4}',
					'month' => '[0-9]{2}',
					'day' => '[0-9]{2}'
				)
			),
			'/<year>/<month>' => array(
				'regex' => array(
					'year' => '[0-9]{4}',
					'month' => '[0-9]{2}'
				)
			),
			'/<year>' => array(
				'regex' => array(
					'year' => '[0-9]{4}'
				)
			),
			'/<slug>' => array(
				'method' => '_display_page'
			),
			'' => array(
				'method' => '_display_page'
			),
		);
	}

	public function execute()
	{
		if( isset($this->router()->day) )
		{
			return $this->_archive_by('day');
		}
		else if( isset($this->router()->month) )
		{
			return $this->_archive_by('month');
		}
		else if( isset($this->router()->year) )
		{
			return $this->_archive_by('year');
		}
		
		Model_Page_Front::not_found();
	}

	/**
	 * 
	 * @param string $interval
	 */
	protected function _archive_by($interval)
    {
        $this->interval = $interval;

        $page = $this->page()->children(array(
            'where' => array(
				array('behavior_id', '=', 'archive_' . $interval . '_index')
			),
            'limit' => 1
        ), array(), TRUE);

        if (isset($page[0]))
        {
            $this->_page = $page[0];
            $this->page()->time = mktime(0, 0, 0, $this->router()->param('month', 1), $this->router()->param('day', 1), $this->router()->param('year'));
        }
        else
        {
            Model_Page_Front::not_found();
        }
    }
    
    protected function _display_page()
    {
		$slug = $this->router()->param('slug');

		if(empty($slug))
		{
			return;
		}

        if(($this->_page = Model_Page_Front::findBySlug($slug, $this->page())) === FALSE )
		{
            Model_Page_Front::not_found();
		}
    }

	/**
	 * 
	 * @param array $clause
	 * @return array
	 */
	public function get($clause = array())
	{
		$date = implode('-', $this->router()->params());

		if( ! isset($clause['where']) )
		{
			$clause['where'] = array(array('page.created_on', 'like', $date . '%'));
		}

		if( ! isset($clause['order_by']) )
		{
			$clause['order_by'] = array(array('page.created_on', 'desc'));
		}

		$pages = $this->page()->parent->children($clause);

		return $pages;
	}
}