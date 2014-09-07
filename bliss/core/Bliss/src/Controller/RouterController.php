<?php
namespace Bliss\Controller;

use Bliss\Router\Route\RegexRoute,
	Bliss\Request\HttpRequest;

class RouterController extends MultiActionController
{
	public function init()
	{}
	
	public function routeAction()
	{
		$path = $this->getParam("routePath");
		$route = RegexRoute::factory("/^([a-z0-9-]+)\/?([a-z0-9-]+)?\/?([a-z0-9-]+)?\.?([a-z]+)?$/i", [
			"matches" => [
				1 => "module",
				2 => "controller",
				3 => "action",
				4 => "format"
			]
		]);
		
		$parameters = [
			"module" => "bliss",
			"controller" => "default"
		];
		if ($route->matches($path)) {
			$getParams = filter_input_array(INPUT_GET);
			$parameters = array_merge($route->parameters(), isset($getParams) ? $getParams : []);
		}
		
		$request = new HttpRequest();
		$request->setParams($parameters);
		
		$this->app()->exec($request);
		$this->app()->close();
	}
}