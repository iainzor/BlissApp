<?php
namespace UI\Tests;

use UI\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialConditions()
	{
		$container = new Container();
		$view = $container->view();
		$layout = $container->layout();
		
		$this->assertInstanceOf("\\UI\\View\\Container", $view);
		$this->assertInstanceOf("\\UI\\Layout\\Container", $layout);
		$this->assertNull($container->render());
	}
}