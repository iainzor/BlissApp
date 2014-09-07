<?php
namespace Bliss\Router\Route;

class UnknownRoute extends AbstractRoute
{
	protected $priority = -1000;
	
	public function matches($value) { return false; }
	
	public function parameters() { return []; }
}