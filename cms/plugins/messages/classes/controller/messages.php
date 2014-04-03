<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

class Controller_Messages extends Controller_System_Backend {
	
	public $allowed_actions = array('index', 'add', 'view');
	
	public function before()
	{
		parent::before();
		$this->breadcrumbs
			->add(__('Messages'), strtolower($this->request->controller()));
		
		Assets::package('redactor');
	}
	
	public function action_index()
	{
		$this->template->title = __('Messages');

		$this->template->content = View::factory('messages/index', array(
			'messages' => Api::get('user-messages.get', array('uid' => AuthUser::getId(), 'fields' => 'author,title,is_read,created_on'))
				->as_object()->get('response')
		));
	}

	public function action_add()
	{
		if($this->request->method() === Request::POST)
		{
			$id = (int) $this->request->post('to');
			$user = ORM::factory('user', $id);

			if( ! $user->loaded() ) 
			{
				throw new HTTP_Exception_404('User not found');
			}

			$post = $this->request->post();
			$post['from_user_id'] = AuthUser::getId();
			$post['to_user_id'] = $user->id;
			return $this->_send(Api::put('user-messages', $post));
		}
		
		$to = $this->request->query('to');
		$to = ORM::factory('user', $to)->id;

		$this->template->content = View::factory('messages/add', array(
			'user_id' => AuthUser::getId(),
			'to' => $to
		));
		
		$this->template->title = __('Send message');
		
		$this->breadcrumbs
			->add($this->template->title);
	}
	
	public function action_view()
	{
		$id = (int) $this->request->param('id');
		$user_id = AuthUser::getId();

		$message = Api::get('user-messages.get_by_id', array(
			'id' => $id, 
			'uid' =>  $user_id,
			'fields' => 'author,title,is_read,created_on,text'
		))->as_object();
		
		if( ! $message->response )
		{
			throw new HTTP_Exception_404('Message not found');
		}
		
		if($this->request->method() === Request::POST)
		{
			$this->auto_render = FALSE;
			$post = $this->request->post();
			$post['from_user_id'] = $user_id;
			$post['parent_id'] = $id;
			
			return $this->_send(Api::put('user-messages', $post), $id);
		}
		
		$read = Api::post('user-messages.mark_read', array(
			'id' => $id, 'uid' => $user_id
		));
		
		$new = Api::get('user-messages.count_new', array(
			'uid' => $user_id
		))->as_object();
		
		Model_Navigation::update(URL::backend('messages'), array(
			'counter' => $new->response
		));
		
		$messages = Api::get('user-messages.get', array(
			'uid' => $user_id, 
			'fields' => 'author,from_user_id,title,is_read,created_on,text',
			'pid' => $id
		))->as_object();
		
		$this->template->content = View::factory('messages/view', array(
			'tpl' => View::factory('messages/item'),
			'message' => $message->response,
			'messages' => $messages->response
		));
		
		$this->template->title = $message->response->title;
		
		$this->breadcrumbs
			->add($message->response->title);
	}
	
	private function _send($send, $parent_id = 0)
	{
		$status = $send->response;
		$id = $parent_id > 0 ? $parent_id : $status;
	
		if((int) $id > 0)
		{
			$this->go(Route::get('backend')->uri(array('controller' => 'messages', 'action' => 'view', 'id' => (int) $id)));
		}
		
		$this->go_back();
	}
}