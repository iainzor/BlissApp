<?php
namespace DataStore\Tests\Query;

use DataStore\Query\Join\Join,
	DataStore\Tests\Storage\MockStorageA;

class JoinTest extends \PHPUnit_Framework_TestCase
{
	public function testAddFieldConditions()
	{
		$storage = new MockStorageA();
		$join = new Join($storage);
		$join->fields("foo")->compareTo("ba");
		
		$this->assertNotEmpty($join->getConditions());
	}
}