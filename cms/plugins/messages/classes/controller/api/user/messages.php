<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Controller_API_User_Messages extends Controller_System_Api {

	public function get_get()
	{		
		$user_id = $this->param('uid', NULL, TRUE);
		$parent_id = (int) $this->param('pid');
		$messages = Model_API::factory('api_message')
			->get_all($user_id, $parent_id, $this->fields);

		$this->response($messages);
	}
	
	public function get_list()
	{		
		$this->get_get();
		$messages = $this->json['response'];
		
		$response_messages = array();
		foreach($messages as $msg)
		{
			$msg = (object) $msg;
			
			if($msg->is_read == Model_API_Message::STATUS_NEW)
			{
				Api::post('user-messages.mark_read', array(
					'id' => $msg->id, 'uid' => AuthUser::getId()
				));
			}
	
			$response_messages[] = (string) View::factory('messages/item')
				->set('message', (object) $msg);
		}
		
		$this->response($response_messages);
	}
	
	public function get_get_by_id()
	{
		$id = $this->param('id', NULL, TRUE);
		$user_id = $this->param('uid', NULL, TRUE);
		
		$message = Model_API::factory('api_message')
			->get_by_id($id, $user_id, $this->fields);
		
		$this->response($message);
	}
	
	public function get_count_new()
	{
		$user_id = $this->param('uid', NULL, TRUE);
		
		$total = Model_API::factory('api_message')
			->count_new($user_id);
		
		$this->response($total);
	}

	public function post_mark_read()
	{
		$id = $this->param('id', NULL, TRUE);
		$user_id = $this->param('uid', NULL, TRUE);
		
		$message = Model_API::factory('api_message')
			->mark_read($id, $user_id);
		
		$this->response($message);
	}

	public function rest_put()
	{
		$from_user_id = (int) $this->param('from_user_id', NULL, TRUE);
		$parent_id = (int) $this->param('parent_id', 0);
		$content = $this->param('content', NULL, TRUE);
		
		if($parent_id > 0)
		{
			$title = NULL;
			$to_user_id = DB::select('user_id')
				->from('messages_users')
				->where('messages_users.message_id', '=', $parent_id)
				->execute()
				->as_array(NULL, 'user_id');
		}
		else
		{
			$title = $this->param('title', NULL, TRUE);
			$to_user_id = (int) $this->param('to_user_id', NULL, TRUE);
		}
		
		$message = Model_API::factory('api_message')
			->send($title, $content, $from_user_id, $to_user_id, $parent_id);
		
		$this->response($message);
	}
	
	public function post_delete()
	{
		$id = $this->param('id', NULL, TRUE);
		$user_id = $this->param('uid', NULL, TRUE);
		
		$message = Model_API::factory('api_message')
			->delete_by_user($id, $user_id);
		
		$this->response($message);
	}
}