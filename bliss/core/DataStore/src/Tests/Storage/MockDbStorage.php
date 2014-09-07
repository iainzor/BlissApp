<?php
namespace DataStore\Tests\Storage;

class MockDbStorage extends \DataStore\Storage\GenericDbStorage
{
	public function getTableName() 
	{
		return "test_table";
	}

}