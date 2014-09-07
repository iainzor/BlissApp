<?php
namespace DataStore\Query\Condition;

use DataStore\Query\Field\Field;

class Condition extends \Bliss\Component
{
	const EQUAL_TO = "equalTo";
	const NOT_EQUAL_TO = "notEqualTo";
	const GREATER_THAN = "greaterThan";
	const LESS_THAN = "lessThan";
	const GREATER_OR_EQUAL_TO = "greaterOrEqualTo";
	const LESS_OR_EQUAL_TO = "lessOrEqualTo";
	const CONTAINS = "contains";
	const STARTS_WITH = "startsWith";
	const ENDS_WITH = "endsWith";
	
	/**
	 * @var \DataStore\Query\Field\Field
	 */
	protected $field;
	
	/**
	 * @var mixed
	 */
	protected $value = false;
	
	/**
	 * @var string
	 */
	protected $format;
	
	/**
	 * @var string
	 */
	protected $operator = self::EQUAL_TO;
	
	/**
	 * Set the name of the field to compare the value to
	 * 
	 * @param \DataStore\Query\Field\Field $field
	 */
	public function setField(Field $field)
	{
		$this->field = $field;
	}
	
	/**
	 * Get the name of the field to compare the value to
	 * 
	 * @return \DataStore\Query\Field\Field
	 */
	public function getField()
	{
		return $this->field;
	}
	
	/**
	 * Set the value used to compare against 
	 * 
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * Get the value used to compare against
	 * 
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Set the string used to format the value using sprintf
	 * 
	 * @see sprintf
	 * @param string $format
	 */
	public function setFormat($format) 
	{
		$this->format = $format;
	}
	
	/**
	 * Get the condition's formatted value
	 * 
	 * @return string
	 */
	public function getFormattedValue()
	{
		return isset($this->format)
			? sprintf($this->format, $this->value)
			: $this->value;
	}

	/**
	 * Get the condition's operator
	 * 
	 * @return string
	 */
	public function getOperator() 
	{
		return $this->operator;
	}

	/**
	 * Set the condition's operator
	 * 
	 * @param string $operator
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
	}
		
	/**
	 * Create a new condition instance from an array of properties
	 * 
	 * @param array $properties
	 * @return \DataStore\Query\Condition\Condition
	 */
	public static function factory(array $properties)
	{
		$condition = new self();
		$condition->setProperties($properties);
		
		return $condition;
	}
}