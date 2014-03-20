<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class KodiCMS_Controller_API_Tags extends Controller_System_API {

	public function rest_get()
	{
		$name = $this->param('term', NULL, TRUE);
		
		if(empty($name))
		{
			return NULL;
		}

		$tags = Model_Tag::findAllLike($name);
		
		$array = array();
		
		foreach ( $tags as $tag )
		{
			$array[] = array(
				'id' => $tag->name,
				'text' => $tag->name
			);
		}
		
		$this->response($array);
	}
}