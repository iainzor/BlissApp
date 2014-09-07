<?php
namespace Users\Tests;

use Users\User;

class MockStorage implements \Users\StorageInterface
{
	const TEST_PASSWORD = "abc123";
	
	private static $_records = array(
		array(
			"id" => 1,
			"username" => "iainzor",
			"email" => "iainedminster@gmail.com",
			"password" => null
		)
	);
	
	public function __construct()
	{
		$hasher = User::hasher();
		
		self::$_records[0]["password"] = $hasher->hash(self::TEST_PASSWORD);
	}
	
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
	
	public function loadByUsernameOrEmail($username) {
		foreach (self::$_records as $record) {
			if ($record["username"] == $username || $record["email"] == $username) {
				return $record;
			}
		}
		return null;
	}
	
	public function emailExists($email) {
		foreach (self::$_records as $record) {
			if ($record["email"] == $email) {
				return true;
			}
		}
		return false;
	}
	
	public function usernameExists($username) {
		foreach (self::$_records as $record) {
			if ($record["username"] == $username) {
				return true;
			}
		}
		return false;
	}

}