<?php
namespace DataStore\Filter\Field;

use DataStore\Query\Condition\Condition;

class Field extends \DataStore\Query\Field\Field
{
	/**
	 * @var string
	 */
	protected $operator = Condition::EQUAL_TO;
	
	/**
	 * Set the operator used to compare the field with the value
	 * 
	 * @param string $operator
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
	}
	
	/**
	 * Get the operator used to compare the field with the value
	 * 
	 * @return string
	 */
	public function getOperator()
	{
		return $this->operator;
	}
}