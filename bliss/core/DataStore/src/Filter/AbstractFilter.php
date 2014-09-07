<?php
namespace DataStore\Filter;

use DataStore\Query\Query;

abstract class AbstractFilter extends \Bliss\Component implements FilterInterface
{
	/**
	 * @var \DataStore\Filter\Field\Collection
	 */
	protected $fields;
	
	/**
	 * Apply the filter to a query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 */
	public function applyToQuery(Query $query) 
	{
		foreach ($this->fields() as $field) {
			$this->applyFieldToQuery($field, $query);
		}
	}
	
	/**
	 * Apply a field configuration to a query instance
	 * 
	 * @param \DataStore\Filter\Field\Field $field
	 * @param \DataStore\Query\Query $query
	 */
	private function applyFieldToQuery(Field\Field $field, Query $query)
	{
		$queryField = $query->fields($field->getAlias());
		$queryField->compareTo($field->getValue(), $field->getOperator(), false);
	}

	/**
	 * Set the values of any fields within the filter
	 * 
	 * @param array $valuePairs
	 */
	public function setValues(array $valuePairs) 
	{
		foreach ($valuePairs as $name => $value) {
			try {
				$this->fields()->get($name)->setValue($value);
			} catch (\Exception $ex) {}
		}
	}
	
	/**
	 * Get the fields available to the filter
	 * 
	 * @return \DataStore\Filter\Field\Collection
	 */
	public function fields()
	{
		if (!isset($this->fields)) {
			$this->fields = new Field\Collection();
		}
		
		return $this->fields;
	}
	
	/**
	 * @return \DataStore\Filter\Field\Collection
	 */
	public function getFields()
	{
		return $this->fields();
	}
	
	/**
	 * Set the fields for the filter to use
	 * 
	 * @param \DataStore\Filter\Field\Collection $fields
	 */
	public function setFields(Field\Collection $fields)
	{
		$this->fields = $fields;
	}
	
	/**
	 * Add a field to the filter
	 * 
	 * @param \DataStore\Filter\Field\Field $field
	 */
	public function addField(Field\Field $field)
	{
		$this->fields()->add($field);
	}

}