<?php
namespace DataStore\Tests\Storage;

use DataStore\Query\Query,
	DataStore\Query\Condition;

class ArrayStorageTest extends \PHPUnit_Framework_TestCase
{
	public function testFindAllWithoutQuery()
	{
		$storage = new MockStorageA();
		$results = $storage->findAll();
		
		$this->assertNotEmpty($results);
	}
	
	public function testFindAllWithQuery()
	{
		$storage = new MockStorageA();
		$query = new Query($storage);
		$query->fields("foo")->compareTo("ba", Condition\Condition::CONTAINS);
		
		$results = $storage->findAll($query);
		
		$this->assertCount(2, $results);
	}
}