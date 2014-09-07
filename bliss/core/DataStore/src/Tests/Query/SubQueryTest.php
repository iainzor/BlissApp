<?php
namespace DataStore\Tests\Query;

use DataStore\Query\Query,
	DataStore\Tests\Storage\MockStorageA;

class SubQueryTest extends \PHPUnit_Framework_TestCase
{
	public function testSubQuery()
	{
		$query = new Query(new MockStorageA());
		$subQuery = new Query(new MockStorageA());
		
		$query->fields("total")->setQuery($subQuery);
		
		$this->assertNotNull($query->fields("total")->getQuery());
	}
}