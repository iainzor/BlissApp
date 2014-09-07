<?php
namespace DataStore\Storage;

class GenericDbStorage extends AbstractDbStorage
{
	/**
	 * @var string
	 */
	private $tableName = null;
	
	/**
	 * @var string
	 */
	protected $modelClass;
	
	/**
	 * @return null
	 */
	public function getFields() { return null; }
	
	/**
	 * Set the table's name
	 * 
	 * @param string $tableName
	 */
	public function setTableName($tableName)
	{
		$this->tableName = $tableName;
	}
	
	/**
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	public function getTableName() 
	{
		if ($this->tableName === null) {
			throw new \UnexpectedValueException("No table name has been provided!");
		}
		
		return $this->tableName;
	}
	
	/**
	 * Get the class name of the model to put data into
	 * 
	 * @return string
	 */
	public function getModelClass() 
	{
		return $this->modelClass;
	}

	/**
	 * Set the class name of the model to put data into
	 * 
	 * @param string $className
	 */
	public function setModelClass($className) 
	{
		$this->modelClass = $className;
	}
	
	/**
	 * Generate a new generic DB storage instance for a table
	 * 
	 * @param \PDO $db
	 * @param string $tableName
	 * @return \DataStore\Storage\GenericDbStorage
	 */
	public static function factory(\PDO $db, $tableName)
	{
		$storage = new self($db);
		$storage->setTableName($tableName);
		
		return $storage;
	}
}