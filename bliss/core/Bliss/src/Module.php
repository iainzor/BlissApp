<?php
namespace Bliss;

class Module extends Module\AbstractModule
{
	const NAME = "bliss";
	
	/**
	 * @return string
	 */
	public function getName() { return self::NAME; }
	
	public function init()
	{}
}