<?php
namespace Assets;

use Bliss\Module\AbstractModule;

class Module extends AbstractModule
{
	const NAME = "assets";
	
	/**
	 * @return string
	 */
	public function getName() { return self::NAME; }
	
	/**
	 * @var \Assets\Source\Container
	 */
	private static $_container;
	
	public function init()
	{}
	
	/**
	 * Get the asset container instance
	 * 
	 * @return \Assets\Source\Container
	 */
	public static function container()
	{
		if (!isset(self::$_container)) {
			self::$_container = new Source\Container($this->app());
		}
		return self::$_container;
	}
}