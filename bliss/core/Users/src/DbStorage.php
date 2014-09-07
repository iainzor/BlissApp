<?php
namespace Users;

use Bliss\Storage\AbstractDbStorage;

class DbStorage extends AbstractDbStorage implements StorageInterface 
{
	protected $tableName = "users";
	
	protected $fieldList = array(
		"id",
		"role",
		"email",
		"username",
		"nickname",
		"password",
		"firstName",
		"lastName",
		"created",
		"updated",
		"isActive"
	);
	
	/**
	 * Load a user's data by either their username or password
	 * 
	 * @param string $username
	 * @return array
	 */
	public function loadByUsernameOrEmail($username) 
	{
		return $this->db->fetchRow("
			SELECT	*
			FROM	". $this->tableName ." 
			WHERE	`username` = :username
					OR `email` = :username
			LIMIT	1
		", array(
			":username" => $username
		));
	}
	
	/**
	 * Check if an email address already exists
	 * 
	 * @param string $email
	 * @return boolean
	 */
	public function emailExists($email) 
	{
		return $this->count(array(
			"params" => array(
				"email" => $email
			)
		)) > 0;
	}
	
	/**
	 * Check if a username already exists
	 * 
	 * @param string $username
	 * @return boolean
	 */
	public function usernameExists($username) 
	{
		return $this->count(array(
			"params" => array(
				"username" => $username
			)
		)) > 0;
	}
	
	/**
	 * Generate a database user loader instance
	 * 
	 * @param \Bliss\Db\Connection $db
	 * @return \Users\Loader
	 */
	public static function generateLoader(\Bliss\Db\Connection $db)
	{
		$storage = new self($db);
		$loader = new Loader($storage);
		$loader->setParamContainer(\System\Module::params());
		
		return $loader;
	}
	
	/**
	 * Generate a database user saver instance
	 * 
	 * @param \Bliss\Db\Connection $db
	 * @return \Users\Saver
	 */
	public static function generateSaver(\Bliss\Db\Connection $db)
	{
		$storage = new self($db);
		$saver = new Saver($storage);
		$saver->setParamsContainer(\System\Module::params());
		
		return $saver;
	}
}