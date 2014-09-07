<?php
namespace Bliss\ErrorHandler;

interface ErrorHandlerInterface
{
	public function handleException(\Exception $e);
	
	public function handleError($num, $message, $file, $line, array $context = null);
}