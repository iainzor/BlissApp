<?php
namespace DataStore\Tests\Filter;

use DataStore\Filter,
	DataStore\Query\Query,
	DataStore\Query\Condition\Condition,
	DataStore\Tests\Storage\MockStorageA;

class GenericFilterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param string $name
	 * @param string $orperator
	 * @return \DataStore\Filter\Field\Field
	 */
	private function _createField($name, $operator = Condition::EQUAL_TO)
	{
		$field = new Filter\Field\Field();
		$field->setName($name);
		$field->setOperator($operator);
		
		return $field;
	}
	
	public function testAddField()
	{
		$filter = new Filter\GenericFilter();
		$field = $this->_createField("myField");
		$filter->addField($field);
		
		$this->assertNotEmpty($filter->getFields());
	}
	
	public function testSetValues()
	{
		$field = $this->_createField("myField");
		$filter = new Filter\GenericFilter();
		$filter->addField($field);
		$filter->setValues([
			"myField" => "myValue"
		]);
		
		$this->assertEquals("myValue", $field->getValue());
	}
	
	public function testApplyToQuery()
	{
		$query = new Query(new MockStorageA());
		$filter = new Filter\GenericFilter();
		$field = $this->_createField("myField", Condition::NOT_EQUAL_TO);
		$field->setValue("myValue");
		$filter->addField($field);
		$filter->applyToQuery($query);
		
		$this->assertEquals(
			Condition::NOT_EQUAL_TO, 
			$query->fields("myField")
				  ->getConditions()
				  ->getFirstItem()
				  ->getOperator()
		);
	}
}