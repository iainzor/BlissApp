<?php
namespace DataStore\Tests\Factory;

use DataStore\Query\DbQuery,
	DataStore\Factory\MySqlStringFactory,
	DataStore\Tests\Storage\MockDbStorage,
	DataStore\Model\Model;

class MySqlStringFactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \DataStore\Tests\Storage\MockDbStorage
	 */
	private $storage;
	
	/**
	 * @var \PDO
	 */
	private $db;
	
	/**
	 * @var \DataStore\Factory\MySqlStringFactory
	 */
	private $factory;
	
	public function setUp() 
	{
		$this->db = new \PDO("sqlite:test.db");
		$this->storage = new MockDbStorage($this->db);
		$this->factory = new MySqlStringFactory($this->db);
	}
	
	public function testBasic()
	{
		$query = new DbQuery($this->storage);
		$sql = $this->factory->generateSelectStatement($query);
		
		$this->assertEquals("SELECT `test_table`.* FROM `test_table`", $sql);
	}
	
	public function testSingleCondition()
	{
		$query = new DbQuery($this->storage);
		$query->fields("testField")->compareTo("testValue");
		$sql = $this->factory->generateSelectStatement($query);
		
		$this->assertEquals("SELECT `test_table`.* FROM `test_table` WHERE `test_table`.`testField` = 'testValue'", $sql);
	}
	
	public function testMutliValueCondition()
	{
		$query = new DbQuery($this->storage);
		$query->fields("testField")->compareTo(["valueA", "valueB"]);
		$sql = $this->factory->generateWhereClause($query);
		
		$this->assertEquals("WHERE `test_table`.`testField` IN ('valueA','valueB')", $sql);
	}
	
	public function testJoin()
	{
		$query = new DbQuery($this->storage);
		$storage = clone $this->storage;
		$storage->setAlias("storageB");
		$query->join($storage, "parentId", "id");
		$sql = $this->factory->generateJoinClause($query);
		
		$this->assertEquals("JOIN `test_table` AS `storageB` ON `test_table`.`id` = `storageB`.`parentId`", $sql);
	}
	
	public function testGenerateLimitClause()
	{
		$query = new DbQuery($this->storage);
		$query->setMaxResults(1);
		
		$sql = $this->factory->generateLimitClause($query);
		
		$this->assertEquals("LIMIT 1 OFFSET 0", $sql);
	}
	
	public function testGenerateInsertStatement()
	{
		$model = new Model();
		$model->setName("test");
		
		$sql = $this->factory->generateInsertStatement($this->storage, $model);
		
		$this->assertEquals("INSERT INTO `test_table` (`name`) VALUES ('test')", $sql);
	}
	
	public function testGenerateUpdateStatement()
	{
		$model = new Model();
		$model->setId(1);
		$model->setName("Foo");
		
		$query = new DbQuery($this->storage);
		$query->fields("id")->compareTo(1);
		
		$sql = $this->factory->generateUpdateStatement($model, $query);
		
		$this->assertEquals("UPDATE `test_table` SET `name` = 'Foo' WHERE `test_table`.`id` = '1'", $sql);
	}
	
	public function testGenerateDeleteStatement()
	{
		$query = new DbQuery($this->storage);
		$query->fields("id")->compareTo(1);
		
		$sql = $this->factory->generateDeleteStatement($query);
		
		$this->assertEquals("DELETE FROM `test_table` WHERE `test_table`.`id` = '1'", $sql);
	}
	
	public function testGenerateWithSubQuery()
	{
		$query = new DbQuery($this->storage);
		$subQuery = new DbQuery($this->storage);
		//$subQuery->setGroupType(DbQuery::GROUP_COUNT);
		//$subQuery->fields("foo")->compareTo([$query, "bar"]);
		
		$total = $query->fields("total");
		$total->setQuery($subQuery);
		
		$sql = $this->factory->generateSelectStatement($query);
		
		$this->assertEquals("SELECT `test_table`.*, (SELECT `test_table`.* FROM `test_table`) AS `total` FROM `test_table`", $sql);
	}
}