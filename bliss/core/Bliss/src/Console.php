<?php
namespace Bliss;

use Bliss\Console\MessageHandlerInterface,
	Bliss\Console\Message;

class Console
{
	const LOG = "LOG";
	const ERROR = "ERROR";
	const WARNING = "WARNING";
	const DEBUG = "DEBUG";
	const INFO = "INFO";

	/**
	 * @var array
	 */
	private static $messages = array();

	/**
	 * @var \Bliss\Console\MessageHandlerInterface[]
	 */
	private static $messageHandlers = array();

	/**
	 * Add a message handler to the console
	 *
	 * @param \Bliss\Console\MessageHandlerInterface $handler
	 */
	public static function addMessageHandler(MessageHandlerInterface $handler)
	{
		self::$messageHandlers[] = $handler;
	}

	/**
	 * Add a log message to the console
	 *
	 * @param string $message
	 */
	public static function log($message)
	{
		self::addMessage(self::LOG, $message);
	}

	/**
	 * Add an information message to the console
	 *
	 * @param string $message
	 */
	public static function info($message)
	{
		self::addMessage(self::INFO, $message);
	}

	/**
	 * Add an error message to the console
	 *
	 * @param string $message
	 * @param \Exception $exception
	 */
	public static function error($message, \Exception $exception = null)
	{
		if (!empty($exception)) {
			$message .= "\nTRACE:\n". $exception->getTraceAsString();
		}
		
		self::addMessage(self::ERROR, $message);
	}

	/**
	 * Add a debug message to the console
	 *
	 * @param string $message
	 */
	public static function debug($message)
	{
		self::addMessage(self::DEBUG, $message);
	}

	/**
	 * Add a warning message to the console
	 *
	 * @param string $message
	 */
	public static function warning($message)
	{
		self::addMessage(self::WARNING, $message);
	}

	/**
	 * Create a console message
	 *
	 * @param string $message
	 * @param string $type
	 */
	public static function message($message, $type = self::LOG)
	{
		self::addMessage($type, $message);
	}

	/**
	 * Add a message to the console
	 *
	 * @param string $type
	 * @param string $message
	 */
	private static function addMessage($type, $message)
	{
		$message = new Message($type, $message, new \DateTime("now"));

		self::$messages[] = $message;

		foreach (self::$messageHandlers as $messageHandler)
		{
			$messageHandler->notify($message);
		}
	}

	/**
	 * Convert all stored messages to a string
	 *
	 * @param string $messageType
	 * @return stirng
	 */
	public static function toString($messageType = null)
	{
		self::info("Memory used: ". number_format(memory_get_usage() / 1024) ."kb");
		self::info("Peak memory usage: ". number_format(memory_get_peak_usage(true) / 1024) ."kb");

		$messages = isset($messageType) ? self::getMessagesOfType($messageType) : self::$messages;

		$string = array();
		foreach ($messages as $message)
		{
			$string[]	= $message->toString();
		}
		return implode(PHP_EOL, $string);
	}

	/**
	 * Get the console message as an array
	 *
	 * @param string $messageType
	 * @return array
	 */
	public static function toArray($messageType = null)
	{
		$messages = isset($messageType) ? self::getMessagesOfType($messageType) : self::$messages;

		$array = array();
		foreach ($messages as $message)
			$array[] = $message->toArray();

		return $array;
	}

	/**
	 * Get all messages of a given type
	 *
	 * @param string $type
	 * @return array
	 */
	public static function getMessagesOfType($type)
	{
		$messages = array();
		foreach (self::$messages as $message)
		{
			if ($message->getMessageType() == $type) {
				$messages[] = $message;
			}
		}

		return $messages;
	}

	/**
	 * Save the console messages to a file
	 *
	 * @param string $filename
	 */
	public static function save($filename)
	{
		file_put_contents($filename, self::toString());
	}
	
	/**
	 * Clear all messages in the console
	 */
	public static function clear()
	{
		self::$messages = [];
	}
}