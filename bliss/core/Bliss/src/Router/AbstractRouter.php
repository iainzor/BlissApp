<?php
namespace Bliss\Router;

abstract class AbstractRouter implements RouterInterface
{
	/**
	 * @var \Bliss\Router\Route\Collection
	 */
	protected $routes;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->routes = new Route\Collection();
	}
	
	/**
	 * Add a route to the router
	 * 
	 * @param \Bliss\Router\Route\RouteInterface $route
	 */
	public function addRoute(Route\RouteInterface $route)
	{
		$this->routes->add($route);
	}
}