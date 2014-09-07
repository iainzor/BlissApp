<?php
namespace DataStore\Tests\Storage;

use DataStore\Storage\AbstractArrayStorage;

class MockStorageA extends AbstractArrayStorage
{
	public function __construct(array $data = array()) 
	{
		parent::__construct([
			[
				"id" => 1,
				"foo" => "bar"
			], [
				"id" => 2,
				"foo" => "baz"
			], [
				"id" => 3,
				"bar" => "baz"
			]
		]);
	}
	
	public function getName() { return "DataStoreTestMockStorageA"; }
	public function getModelClass() { return null; }
	public function getFields() { return null; }
	public function getFoundRows() { return 0; }
}