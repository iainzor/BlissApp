<?php
namespace Bliss\Console;

use Bliss\Console;

class Message
{
	private $dateTime;
	private $messageType;
	private $message;
	private $microtime;

	/**
	 * Constructor
	 *
	 * @param string $messageType
	 * @param string $message
	 * @param \DateTime $dateTime
	 */
	public function __construct($messageType, $message, \DateTime $dateTime = null)
	{
		$this->messageType = $messageType;
		$this->message = $message;
		$this->dateTime = isset($dateTime) ? $dateTime : new DateTime("now");
		$this->microtime = microtime(true); //substr((string)microtime(), 2, 7);
	}

	/**
	 * Get the date and time of the message
	 *
	 * @return DateTime
	 */
	public function getDateTime()
	{
		return $this->dateTime;
	}

	/**
	 * Get the message type
	 *
	 * @return string
	 */
	public function getMessageType()
	{
		return $this->messageType;
	}

	/**
	 * Get the message string
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Convert the message to a string
	 *
	 * @return string
	 */
	public function toString()
	{
		$message = $this->message;
		if ($this->messageType != Console::ERROR) {
			$message = trim(preg_replace("/\s+/", " ", $this->message));
		}

		$string	= $this->dateTime->format("Y-m-d H:i:s") .".". $this->microtime ."\t"
				. $this->messageType ."\t"
				. $message;

		return $string;
	}

	/**
	 * Convert the message to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			"timestamp" => $this->dateTime->format("Y-m-d H:i:s") .".". $this->microtime,
			"type" => $this->messageType,
			"message" => $this->message
		);
	}
}