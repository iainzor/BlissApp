<?php
namespace Bliss\Response;

class HttpResponse extends AbstractResponse
{
	private static $_codes = [
		200 => "OK",
 		404 => "Not Found",
		500 => "Internal Server Error"
	];
	
	/**
	 * Set the response code
	 * 
	 * @param int $code
	 */
	public function setCode($code)
	{
		$code = (int) $code;
		$message = isset(self::$_codes[$code]) ? self::$_codes[$code] : self::$_codes[200];
		
		header("HTTP/1.1 {$code} {$message}");
	}
	
	/**
	 * Set the response's content type
	 * 
	 * @param string $contentType
	 */
	public function setContentType($contentType)
	{
		header("Content-Type: {$contentType}");
	}
}