<?php
namespace UI\Tests\View;

use UI\View\Container,
	UI\View\Format;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialConditions()
	{
		$view = new Container();
		
		$this->assertNull($view->render());
	}
	
	public function testJsonRender()
	{
		$params = [
			"hello" => "world"
		];
		
		$view = new Container();
		$view->registerFormat(new Format\JsonFormat());
		$view->setFormat("json");
		$view->setParams($params);
		
		$this->assertEquals(json_encode($params), $view->render());
	}
}