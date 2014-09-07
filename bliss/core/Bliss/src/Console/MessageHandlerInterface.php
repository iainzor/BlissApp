<?php
namespace Bliss\Console;

interface MessageHandlerInterface
{
	public function notify(Message $message);
}