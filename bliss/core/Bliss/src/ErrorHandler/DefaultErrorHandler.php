<?php
namespace Bliss\ErrorHandler;

class DefaultErrorHandler implements ErrorHandlerInterface
{
	public function handleError($num, $message, $file, $line, array $context = []) 
	{
		$str = " 
			<pre>
				<h1>Error #{$num}</h1>
				<h2>{$message}</h2>
				<p>On line {$line} of {$file}</p>	
			</pre>
		";
		
		echo $str;
		exit;
	}

	public function handleException(\Exception $e) 
	{
		$message = $e->getMessage();
		$trace = $e->getTraceAsString();
		$str = "
			<pre>
				<h1>Error</h1>
				<h2>{$message}</h2>
					
				<h3>Error Trace</h3>
				<p>{$trace}</p>
			</pre>
		";
		
		echo $str;
		exit;
	}

}