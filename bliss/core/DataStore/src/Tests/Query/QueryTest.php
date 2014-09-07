<?php
namespace DataStore\Tests\Query;

use DataStore\Query,
	DataStore\Tests\Storage\MockStorageA,
	DataStore\Tests\Storage\MockStorageB;

/**
 * SELECT	*
 * FROM		my_table
 * JOIN		my_other_table
 *			ON my_table.id = my_other_table.tableId
 * WHERE	my_table.id > '100'
 * GROUP BY	my_table.categoryId
 * ORDER BY	my_table.id DESC
 * LIMIT	0,100
 */

class QueryTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialConditions()
	{
		$storage = new MockStorageA();
		$query = new Query\Query($storage);
		
		$this->assertEquals(0, $query->getMaxResults());
		$this->assertEquals(0, $query->getResultOffset());
		
		$this->assertInstanceOf("\\DataStore\\Query\\Field\\Collection", $query->getFields());
		$this->assertCount(0, $query->getFields());
		
		$this->assertInstanceOf("\\DataStore\\Query\\Join\\Collection", $query->getJoins());
		$this->assertCount(0, $query->getJoins());
		
		$this->assertInstanceOf("\\DataStore\\Query\\Condition\\Collection", $query->getConditions());
		$this->assertCount(0, $query->getConditions());
		
		$this->assertInstanceOf("\\DataStore\\Query\\Group\\Collection", $query->getGroups());
		$this->assertCount(0, $query->getGroups());
		
		$this->assertInstanceOf("\\DataStore\\Query\\Order\\Collection", $query->getOrders());
		$this->assertCount(0, $query->getOrders());
	}
}