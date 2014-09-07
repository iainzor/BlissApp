<?php
namespace Users\Session;

use Bliss\Storage\AbstractDbStorage;

class DbStorage extends AbstractDbStorage implements StorageInterface 
{
	protected $tableName = "user_sessions";
	
	protected $fieldList = array(
		"id",
		"userId",
		"created",
		"isValid"
	);
	
	/**
	 * Generate a new database session loader instance
	 * 
	 * @param \Bliss\Db\Connection $db
	 * @return \Users\Session\Saver
	 */
	public static function generateLoader(\Bliss\Db\Connection $db)
	{
		return new Loader(new self($db));
	}
}