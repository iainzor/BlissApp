<?php
namespace DataStore\Tests\Query;

use DataStore\Query\Query,
	DataStore\Tests\Storage\MockStorageA,
	DataStore\Tests\Storage\MockStorageB,
	DataStore\Factory\ModelFactory;

class ModelFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testBasicBuild()
	{
		$storage = new MockStorageA();
		$query = new Query($storage);
		$factory = new ModelFactory($query);
		$model = $factory->generate([
			"foo" => "bar"
		]);
		
		$this->assertInstanceOf("\\DataStore\\Model\\Model", $model);
		$this->assertEquals("bar", $model->getFoo());
	}
	
	public function testBuildRelationships()
	{
		$storageA = new MockStorageA();
		$storageB = new MockStorageB();
		
		$queryA = new Query($storageA);
		$queryB = new Query($storageB);
		
		$queryA->hasOne("parent", $queryA, "id", "parentId");
		$queryA->hasMany("children", $queryB, "parentId", "id");
		
		$factory = new ModelFactory($queryA);
		$model = $factory->generate([
			"id" => 2,
			"foo" => "bar",
			"parent" => [
				"id" => 1,
				"foo" => "baz"
			],
			"children" => [
				[
					"id" => 3,
					"foo" => "blah"
				], [
					"id" => 4,
					"foo" => "halb"
				]
			]
		]);
		
		$this->assertInstanceOf("\\DataStore\\Model\\Model", $model->getParent());
		$this->assertInstanceOf("\\DataStore\\Model\\Collection", $model->getChildren());
		$this->assertEquals(1, $model->getParent()->getId());
	}
}