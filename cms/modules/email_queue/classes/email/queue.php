<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package		KodiCMS/EmailQueue
 * @author		ButscHSter
 */
class Email_Queue
{
	/**
	 * Adds an email to the Queue
	 *
	 * @param 	string|array 	Recipient. Either email, or array(email, name)
	 * @param 	string|array 	Sender. Either email or array(email, name)
	 * @param 	string			Subject
	 * @param 	string 			Body
	 * @param 	int 			Priority (1 is low, 1,000 is high etc)
	 * @return 	Model_EMail_Queue
	 */
	public static function add_to_queue($recipient, $sender, $subject, $body, $priority = 1)
	{
		return ORM::factory('email_queue')
			->add_to_queue($recipient, $sender, $subject, $body, $priority);
	}
	
	
	/**
	 * Send out a batch of emails. The number sent is dependant on config('email_queue.batch_size')
	 *
	 * @return 	array 	The number of emails sent and failed.
	 */
	public static function batch_send( $size = NULL )
	{
		set_time_limit(0);

		$stats = array(
			'sent' => 0, 
			'failed' => 0
		);
		
		$size = Config::get('email_queue', 'batch_size');
		
		$emails = ORM::factory('email_queue')->find_batch( $size );
		
		Kohana::$log->add(Log::INFO, 'Send emails')->write();

		foreach($emails as $email)
		{
			if(Email::factory($email->subject)
				->from($email->sender_email, $email->sender_name)
				->to($email->recipient_email, $email->recipient_name)
				->message($email->body->body, 'text/html')
				->send())
			{
				$email->sent();
				$stats['sent'] ++;
			}
			else
			{
				$email->failed();
				$stats['failed'] ++;
			}
		}
		
		Kohana::$log->add(Log::INFO, 'Send emails. Sent: :sent, failed: :failed', array(
			':sent' => $stats['sent'], ':failed' => $stats['failed']
		))->write();
		
		return $stats;
	}
	
	public static function batch_send_with_sleep()
	{
		set_time_limit(0);

		$stats = array(
			'sent' => 0, 
			'failed' => 0
		);

		$size = Config::get('email_queue', 'batch_size');
		$interval = Config::get('email_queue', 'interval');
		
		$emails = ORM::factory('email_queue')->find_batch();
		
		Kohana::$log->add(Log::INFO, 'Send emails with sleep, interval: :interval, size: :size.', array(
			':interval' => $interval, ':size' => $size
		))->write();

		$i = 0;
		ob_end_flush();
		foreach($emails as $email)
		{
			if( $i >= $size )
			{
				$i = 0;
				@ob_flush();
				sleep($interval);
			}

			if(Email::factory($email->subject)
				->from($email->sender_email, $email->sender_name)
				->to($email->recipient_email, $email->recipient_name)
				->message($email->body->body, 'text/html')
				->send())
			{
				$email->sent();
				$stats['sent'] ++;
			}
			else
			{
				$email->failed();
				$stats['failed'] ++;
			}
			
			$i++;
		}
		
		Kohana::$log->add(Log::INFO, 'Send emails with sleep. Sent: :sent, failed: :failed', array(
			':sent' => $stats['sent'], ':failed' => $stats['failed']
		))->write();

		return $stats;
	}
}