<?php
namespace Bliss\Router\Route;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a route to the collection
	 * 
	 * @param \Bliss\Router\Route\RouteInterface $route
	 */
	public function add(RouteInterface $route)
	{
		$this->addItem($route);
	}
	
	/**
	 * 
	 * @param type $value
	 * @return type
	 */
	public function find($value)
	{
		$route = new UnknownRoute();
		
		foreach ($this->getAll() as $candidate) {
			if ($candidate->matches($value) && $candidate->priority() > $route->priority()) {
				$route = $candidate;
			}
		}
		
		if ($route instanceof UnknownRoute) {
			throw new RouteNotFoundException("Could not find a route matching '{$value}'");
		}
		
		return $route;
	}
}