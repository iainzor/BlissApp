<?php
namespace DataStore\Storage;

use DataStore\Factory\MySqlStringFactory,
	DataStore\Query\Query,
	DataStore\Query\DbQuery,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection,
	Bliss\Console;

abstract class AbstractDbStorage extends AbstractStorage
{
	/**
	 * @var \PDO
	 */
	protected $db;
	
	/**
	 * Get the database table's name
	 * 
	 * @return string
	 */
	abstract public function getTableName();
	
	/**
	 * Constructor
	 * 
	 * @param \PDO $db
	 */
	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}
	
	/**
	 * Get the PDO instance for the storage
	 * 
	 * @return \PDO
	 */
	public function getDb()
	{
		return $this->db;
	}
	
	/**
	 * Get the last ID inserted into the database
	 * 
	 * @return mixed
	 */
	public function getLastInsertedId() 
	{
		return $this->db->lastInsertId();
	}
	
	/**
	 * @return string
	 */
	public function getName() 
	{
		return $this->getTableName();
	}
	
	/**
	 * Get the number of rows found from the last SELECT statement
	 * 
	 * @return int
	 */
	public function getFoundRows() 
	{
		$statement = $this->createStatement("SELECT FOUND_ROWS()");
		return (int) $statement->fetchColumn();
	}
	
	public function count(Query $query = null) 
	{}

	/**
	 * Insert a single record into the database
	 * 
	 * @param \DataStore\Model\Model $record
	 */
	public function insert(Model $record) 
	{
		$records = new ModelCollection();
		$records->add($record);
		
		return $this->insertAll($records);
	}

	/**
	 * Insert multiple records into the database
	 * 
	 * @param \DataStore\Model\Collection $records
	 * @return int
	 */
	public function insertAll(ModelCollection $records) 
	{
		$factory = new MySqlStringFactory($this->db);
		$sql = $factory->generateInsertStatement($this, $records);
		
		return $this->exec($sql);
	}
	
	/**
	 * Replace a single record in the database
	 * 
	 * @param \DataStore\Model\Model $record
	 * @return int
	 */
	public function replace(Model $record) 
	{
		$factory = new MySqlStringFactory($this->db);
		$sql = $factory->generateReplaceStatement($this, $record);
		
		return $this->exec($sql);
	}

	/**
	 * Delete the first record found using the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return boolean
	 */
	public function delete(Query $query) 
	{
		$query->setMaxResults(1);
		$result = $this->deleteAll($query);
		
		if ($result == false) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Delete all records matching the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return int The number of rows affected
	 */
	public function deleteAll(Query $query = null) 
	{
		$factory = new MySqlStringFactory($this->db);
		$sql = $factory->generateDeleteStatement($query);
		$result = $this->exec($sql);
		
		return $result;
	}

	/**
	 * Find the first result for a query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return array|null
	 */
	public function find(Query $query = null) {
		if ($query === null) {
			$query = new Query($this);
		}
		
		$query->setMaxResults(1);
		$all = $this->findAll($query);
		
		return count($all) ? array_shift($all) : null;
	}

	/**
	 * Find all results for a query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return array
	 */
	public function findAll(Query $query = null) 
	{
		$factory = new MySqlStringFactory($this->db);
		$sql = $factory->generateSelectStatement($query);
		$statement = $this->createStatement($sql);
		
		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * Update a record in the database
	 * 
	 * @param \DataStore\Model\Model $record
	 */
	public function update(Model $record) 
	{
		if (!$record->getId()) {
			throw new \Exception("The record to update does not have an ID");
		}
		
		$query = new DbQuery($this);
		$query->fields("id")->compareTo($record->getId());
		
		$factory = new MySqlStringFactory($this->db);
		$sql = $factory->generateUpdateStatement($record, $query);
		
		return $this->exec($sql);
	}
	
	/**
	 * Execute a SQL statement and return the number of rows affected
	 * 
	 * @param string $sql
	 * @return int
	 * @throws \PDOException
	 */
	public function exec($sql)
	{
		Console::message($sql, "SQL");
		
		$rowsAffected = $this->db->exec($sql);
		
		if ($rowsAffected === false) {
			throw new \PDOException(
				$this->db->errorInfo()[2]
			);
		}
		
		return $rowsAffected;
	}

	/**
	 * Create a new PDOStatement from a SQL string
	 * 
	 * @param string $sql
	 * @param array $params
	 * @return \PDOStatement
	 * @throws \PDOException
	 */
	protected function createStatement($sql, array $params = [])
	{
		Console::message($sql, "SQL");
		
		$statement = $this->db->prepare($sql);
		if ($statement === false) {
			throw new \PDOException(
				$this->db->errorInfo()[2]
			);
		}
		
		$result = $statement->execute($params);
		if ($result === false) {
			throw new \PDOException(
				$statement->errorInfo()[2]
			);
		}
		
		return $statement;
	}
}