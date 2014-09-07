<?php
namespace Bliss\Router\Route;

interface RouteInterface
{
	/**
	 * @return int
	 */
	public function priority();
	
	/**
	 * @param mixed $value
	 * @return boolean
	 */
	public function matches($value);
	
	/**
	 * @return array
	 */
	public function parameters();
}