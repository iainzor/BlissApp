<?php
namespace Bliss\Router;

interface RouterInterface
{
	/**
	 * @param mixed $value
	 * @return array
	 */
	public function getParams($value);
	
	/**
	 * @param \Bliss\Router\Route\RouteInterface $route
	 */
	public function addRoute(Route\RouteInterface $route);
}