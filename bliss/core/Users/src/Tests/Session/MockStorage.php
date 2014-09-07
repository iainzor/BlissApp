<?php
namespace Users\Tests\Session;

class MockStorage implements \Users\Session\StorageInterface
{
	private static $_records = array(
		array(
			"id" => "session_me_timbers",
			"email" => "iainedminster@gmail.com",
			"userId" => 1,
			"created" => 0,
			"isValid" => true
		)
	);
	
	public function count(array $config) {
		
	}

	public function delete(array $config) {
		
	}

	public function deleteAll(array $config) {
		
	}

	public function load(array $config) {
		return self::$_records[0];
	}

	public function loadAll(array $config) {
		return self::$_records;
	}

	public function replace(array $record, array $fieldsToUpdate) {
		
	}

	public function save(array $record, array $params = null) {
		
	}

	public function saveAll(array $configs) {
		
	}

}