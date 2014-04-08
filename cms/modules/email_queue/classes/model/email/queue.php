<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/EmailQueue
 * @category	Model
 * @author		ButscHSter
 */
class Model_Email_Queue extends ORM
{
	const STATUS_PENDING	= 'pending';
	const STATUS_SENT		= 'sent';
	const STATUS_FAILED		= 'failed';
	
	protected $_created_column = array(
		'column' => 'created_on',
		'format' => 'Y-m-d H:i:s'
	);
	
	protected $_updated_column = array(
		'column' => 'updated_on',
		'format' => 'Y-m-d H:i:s'
	);
	
	protected $_has_one = array(
		'body'	=> array(
			'model'			=> 'Email_Queue_Body',
			'foreign_key'	=> 'queue_id',
		),
	);

	public function rules()
	{
		return array(
			'recipient_email' => array(
				array('not_empty'),
				array('email'),
			),
			'sender_email' => array(
				array('not_empty'),
				array('email'),
			),
		);
	}
	
	
	/**
	 * Adds an email to the Queue
	 *
	 * @param 	string|array 	Recipient. Either email, or array(email, name)
	 * @param 	string|array 	Sender. Either email or array(email, name)
	 * @param 	string			Subject
	 * @param 	string 			Body
	 * @param 	int 			Priority (1 is low, 1,000 is high etc)
	 * @return 	Model_MailQueue
	 */
	public function add_to_queue($recipient, $sender, $subject, $body, $priority = 1)
	{
		if(is_array($recipient) AND count($recipient) == 2)
		{
			$this->recipient_email	= $recipient[0];
			$this->recipient_name 	= $recipient[1];
		}
		else
		{
			$this->recipient_email = $recipient;
		}
		
		if(is_array($sender) AND count($sender) == 2)
		{
			$this->sender_email	= $sender[0];
			$this->sender_name 	= $sender[1];
		}
		else
		{
			$this->sender_email = $sender;
		}
		
		$this->subject 	= $subject;
		$this->priority	= $priority;

		try
		{
			if($this->create())
			{
				$this->body->values(array(
					'queue_id' => $this->id,
					'body' => $body
				))
				->create();
			}
		}
		catch(ORM_Validation_Exception $e)
		{
			if( $this->loaded() )
				$this->delete();
		}
		
		return $this;
	}
	
	
	/**
	 * Gets a batch of emails ready for sending
	 *
	 * @param 	int 	Number of emails to send (batch size)
	 * @return 	ORM		Collection of objects
	 */
	public function find_batch( $size = NULL )
	{
		$this->where('state', '=', self::STATUS_PENDING)
			->order_by('priority', 'desc')
			->order_by('created_on', 'asc');

		if( ! empty($size) )
		{
			$this->limit( (int) $size );
		}

		return $this->find_all();
	}
	
	
	/**
	 * Called when an email has been sent
	 *
	 * @return 	this
	 */
	public function sent()
	{
		$this->state = self::STATUS_SENT;
		$this->attempts++;

		return $this->update();
	}
	
	
	/**
	 * Called when an email fails. If it's hit the limit of
	 *
	 * @return 	this
	 */
	public function failed()
	{
		$this->attempts++;
		$max_attempts = Config::get('email_queue', 'max_attempts');

		if($max_attempts <= $this->attempts)
		{
			$this->state = self::STATUS_FAILED;
		}

		return $this->update();
	}
	
	public function clean_old()
	{
		return DB::delete($this->table_name())
			->where(DB::expr('DATE(created_on)'), '<', array(DB::expr('CURDATE() - INTERVAL 10 DAY')))
			->where('state', '!=', self::STATUS_PENDING)
			->execute($this->_db);
	}
}