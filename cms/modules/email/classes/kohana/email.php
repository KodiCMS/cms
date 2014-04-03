<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Email message building and sending.
 *
 * @package    Kohana
 * @category   Email
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Email {

	// Current module version
	const VERSION = '1.0.0';

	/**
	 * @var  object  Swiftmailer instance
	 */
	public static $_mailer;

	/**
	 * Creates a SwiftMailer instance.
	 *
	 * @return  object  Swift object
	 */
	public static function mailer()
	{
		if ( ! Email::$_mailer)
		{
			// Load email configuration, make sure minimum defaults are set
			$config = Kohana::$config->load('email')->as_array() + array(
				'driver'  => 'native',
				'options' => array(),
			);

			// Extract configured options
			extract($config, EXTR_SKIP);

			if ($driver === 'smtp')
			{
				// Create SMTP transport
				$transport = Swift_SmtpTransport::newInstance($options['hostname']);

				if (isset($options['port']))
				{
					// Set custom port number
					$transport->setPort($options['port']);
				}

				if (isset($options['encryption']))
				{
					// Set encryption
					$transport->setEncryption($options['encryption']);
				}

				if (isset($options['username']))
				{
					// Require authentication, username
					$transport->setUsername($options['username']);
				}

				if (isset($options['password']))
				{
					// Require authentication, password
					$transport->setPassword($options['password']);
				}

				if (isset($options['timeout']))
				{
					// Use custom timeout setting
					$transport->setTimeout($options['timeout']);
				}
			}
			elseif ($driver === 'sendmail')
			{
				// Create sendmail transport
				$transport = Swift_SendmailTransport::newInstance();

				if (isset($options['command']))
				{
					// Use custom sendmail command
					$transport->setCommand($options['command']);
				}
			}
			else
			{
				// Create native transport
				$transport = Swift_MailTransport::newInstance();

				if (isset($options['params']))
				{
					// Set extra parameters for mail()
					$transport->setExtraParams($options['params']);
				}
			}

			// Create the SwiftMailer instance
			Email::$_mailer = Swift_Mailer::newInstance($transport);
		}

		return Email::$_mailer;
	}

	/**
	 * Create a new email message.
	 *
	 * @param   string  message subject
	 * @param   string  message body
	 * @param   string  body mime type
	 * @return  Email
	 */
	public static function factory($subject = NULL, $message = NULL, $type = NULL)
	{
		return new Email($subject, $message, $type);
	}

	/**
	 * @var  object  message instance
	 */
	protected $_message;

	/**
	 * Initialize a new Swift_Message, set the subject and body.
	 *
	 * @param   string  message subject
	 * @param   string  message body
	 * @param   string  body mime type
	 * @return  void
	 */
	public function __construct($subject = NULL, $message = NULL, $type = NULL)
	{
		// Create a new message, match internal character set
		$this->_message = Swift_Message::newInstance();

		if ($subject)
		{
			// Apply subject
			$this->subject($subject);
		}

		if ($message)
		{
			// Apply message, with type
			$this->message($message, $type);
		}
	}

	/**
	 * Set the message subject.
	 *
	 * @param   string  new subject
	 * @return  Email
	 */
	public function subject($subject)
	{
		// Change the subject
		$this->_message->setSubject($subject);

		return $this;
	}

	/**
	 * Set the message body. Multiple bodies with different types can be added
	 * by calling this method multiple times. Every email is required to have
	 * a "text/plain" message body.
	 *
	 * @param   string  new message body
	 * @param   string  mime type: text/html, etc
	 * @return  Email
	 */
	public function message($body, $type = NULL)
	{
		if ( ! $type OR $type === 'text/plain')
		{
			// Set the main text/plain body
			$this->_message->setBody($body);
		}
		else
		{
			// Add a custom mime type
			$this->_message->addPart($body, $type);
		}

		return $this;
	}

	/**
	 * Add one or more email recipients..
	 *
	 *     // A single recipient
	 *     $email->to('john.doe@domain.com', 'John Doe');
	 *
	 *     // Multiple entries
	 *     $email->to(array(
	 *         'frank.doe@domain.com',
	 *         'jane.doe@domain.com' => 'Jane Doe',
	 *     ));
	 *
	 * @param   mixed    single email address or an array of addresses
	 * @param   string   full name
	 * @param   string   recipient type: to, cc, bcc
	 * @return  Email
	 */
	public function to($email, $name = NULL, $type = 'to')
	{
		if (is_array($email))
		{
			foreach ($email as $key => $value)
			{
				if (ctype_digit((string) $key))
				{
					// Only an email address, no name
					$this->to($value, NULL, $type);
				}
				else
				{
					// Email address and name
					$this->to($key, $value, $type);
				}
			}
		}
		else
		{
			// Call $this->_message->{add$Type}($email, $name)
			call_user_func(array($this->_message, 'add'.ucfirst($type)), $email, $name);
		}

		return $this;
	}

	/**
	 * Add a "carbon copy" email recipient.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function cc($email, $name = NULL)
	{
		return $this->to($email, $name, 'cc');
	}

	/**
	 * Add a "blind carbon copy" email recipient.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function bcc($email, $name = NULL)
	{
		return $this->to($email, $name, 'bcc');
	}

	/**
	 * Add one or more email senders.
	 *
	 *     // A single sender
	 *     $email->from('john.doe@domain.com', 'John Doe');
	 *
	 *     // Multiple entries
	 *     $email->from(array(
	 *         'frank.doe@domain.com',
	 *         'jane.doe@domain.com' => 'Jane Doe',
	 *     ));
	 *
	 * @param   mixed    single email address or an array of addresses
	 * @param   string   full name
	 * @param   string   sender type: from, replyto
	 * @return  Email
	 */
	public function from($email, $name = NULL, $type = 'from')
	{
		if (is_array($email))
		{
			foreach ($email as $key => $value)
			{
				if (ctype_digit((string) $key))
				{
					// Only an email address, no name
					$this->from($value, NULL, $type);
				}
				else
				{
					// Email address and name
					$this->from($key, $value, $type);
				}
			}
		}
		else
		{
			// Call $this->_message->{add$Type}($email, $name)
			call_user_func(array($this->_message, 'add'.ucfirst($type)), $email, $name);
		}

		return $this;
	}

	/**
	 * Add "reply to" email sender.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function reply_to($email, $name = NULL)
	{
		return $this->from($email, $name, 'replyto');
	}

	/**
	 * Add actual email sender.
	 *
	 * [!!] This must be set when defining multiple "from" addresses!
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function sender($email, $name = NULL)
	{
		$this->_message->setSender($email, $name);

		return $this;
	}

	/**
	 * Set the return path for bounce messages.
	 *
	 * @param   string  email address
	 * @return  Email
	 */
	public function return_path($email)
	{
		$this->_message->setReturnPath($email);

		return $this;
	}

	/**
	 * Access the raw [Swiftmailer message](http://swiftmailer.org/docs/messages).
	 *
	 * @return  Swift_Message
	 */
	public function raw_message()
	{
		return $this->_message;
	}

	/**
	 * Attach a file.
	 *
	 * @param   string  file path
	 * @return  Email
	 */
	public function attach_file($path)
	{
		$this->_message->attach(Swift_Attachment::fromPath($path));

		return $this;
	}

	/**
	 * Embed an image
	 *
	 * @param   string  image path
	 * @return  Embedded image
	 */
	public function embed($image_path)
	{
		return $this->_message->embed(Swift_Image::fromPath($image_path));
	}

	/**
	 * Attach content to be sent as a file.
	 *
	 * @param   binary  file contents
	 * @param   string  file name
	 * @param   string  mime type
	 * @return  Email
	 */
	public function attach_content($data, $file, $mime = NULL)
	{
		if ( ! $mime)
		{
			// Get the mime type from the filename
			$mime = File::mime_by_ext(pathinfo($file, PATHINFO_EXTENSION));
		}

		$this->_message->attach(Swift_Attachment::newInstance($data, $file, $mime));

		return $this;
	}

	/**
	 * Send the email.
	 *
	 * !! Failed recipients can be collected by using the second parameter.
	 *
	 * @param   array    failed recipient list, by reference
	 * @return  integer  number of emails sent
	 */
	public function send(array & $failed = NULL)
	{
		return Email::mailer()->send($this->_message, $failed);
	}

	/**
	 * Send the email to a batch of addresses.
	 *
	 * !! Failed recipients can be collected by using the second parameter.
	 *
	 * @param   array    failed recipient list, by reference
	 * @return  integer  number of emails sent
	 */
	public function batch(array $to, array & $failed = NULL)
	{
		// Get a copy of the current message
		$message = clone $this->_message;

		// Load the mailer instance
		$mailer = Email::mailer();

		// Count the total number of messages sent
		$total = 0;

		foreach ($to as $email => $name)
		{
			if (ctype_digit((string) $email))
			{
				// Only an email address was provided
				$email = $name;
				$name  = NULL;
			}

			// Set the To addre
			$message->setTo($email, $name);

			// Send this email
			$total += $mailer->send($message, $failed);
		}

		return $total;
	}

} // End email

// Load Swiftmailer
require Kohana::find_file('vendor/swiftmailer', 'lib/swift_required');

if(method_exists('Swift_Preferences', 'getInstance'))
{
	// Set the default character set for everything
	Swift_Preferences::getInstance()->setCharset(Kohana::$charset);
}