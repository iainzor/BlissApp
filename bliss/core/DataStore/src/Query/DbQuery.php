<?php
namespace DataStore\Query;

class DbQuery extends Query
{
	/**
	 * @var \DataStore\Storage\AbstractDbStorage
	 */
	protected $storage;
	
	/**
	 * Get the storage instance's database instance
	 * 
	 * @return \PDO
	 */
	public function getDb()
	{
		return $this->storage->getDb();
	}
}