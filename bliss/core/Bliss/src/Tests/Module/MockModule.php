<?php
namespace Bliss\Tests\Module;

class MockModule extends \Bliss\Module\AbstractModule
{
	public function getName() { return "mock-module"; }

	public function init() {}
}