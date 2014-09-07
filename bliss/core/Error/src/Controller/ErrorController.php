<?php
namespace Error\Controller;

use Bliss\Controller\MultiActionController;

class ErrorController extends MultiActionController
{
	public function init()
	{}

	public function errorAction()
	{
		$response = $this->app()->response();
		$exception = $this->getParam("exception", new \Exception("Unknown error"));
		$response->setCode($exception->getCode());
		
		return [
			"exception" => $exception
		];
	}
}