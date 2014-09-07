<?php
namespace Tests;

class Module extends \Bliss\Module\AbstractModule
{
	const NAME = "tests";
	
	public function getName() { return self::NAME; }
	
	public function init()
	{}
}