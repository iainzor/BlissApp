<?php
namespace Bliss\Router;

class UriRouter extends AbstractRouter
{
	/**
	 * Get the parameters of a URI string
	 * 
	 * @param string $value
	 * @return array
	 */
	public function getParams($value) 
	{
		$route = $this->routes->find($value);
		
		return $route->parameters();
	}
}