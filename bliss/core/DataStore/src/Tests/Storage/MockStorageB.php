<?php
namespace DataStore\Tests\Storage;

use DataStore\Storage\AbstractArrayStorage;

class MockStorageB extends AbstractArrayStorage
{
	public function __construct(array $data = array()) 
	{
		parent::__construct([
			[
				"id" => 1,
				"parentId" => 1,
				"foo" => "bar"
			], [
				"id" => 2,
				"parentId" => 1,
				"foo" => "baz"
			], [
				"id" => 3,
				"parentId" => 2,
				"bar" => "baz"
			]
		]);
	}
	
	public function getName() { return "DataStoreTestMockStorageB"; }
	public function getModelClass() { return null; }
	public function getFields() { return null; }
	public function getFoundRows() { return 0; }
}