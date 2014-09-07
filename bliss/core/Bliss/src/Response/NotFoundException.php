<?php
namespace Bliss\Response;

class NotFoundException extends \Exception
{
	public function __construct($message = null, $code = 404, $previous = null) {
		$code = 404;
		
		parent::__construct($message, $code, $previous);
	}
}