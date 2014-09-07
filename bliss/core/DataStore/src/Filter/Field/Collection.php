<?php
namespace DataStore\Filter\Field;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a field to the collection
	 * 
	 * @param \DataStore\Filter\Field\Field $field
	 */
	public function add(Field $field)
	{
		$this->addItem($field);
	}
	
	/**
	 * Attempt to get a field from the collection
	 * 
	 * @param string $name
	 * @return \DataStore\Filter\Field\Field
	 * @throws \InvalidArgumentException
	 */
	public function get($name)
	{
		foreach ($this->getItems() as $field) {
			if ($field->getName() === $name) {
				return $field;
			}
		}
		
		throw new \InvalidArgumentException("Could not find the field '{$name}'");
	}
	
	/**
	 * Generate a new collection of fields using an array of data
	 * 
	 * @param array $filters
	 * @return \DataStore\Filter\Field\Collection
	 */
	public static function factory(array $fields)
	{
		$collection = new self();
		foreach ($fields as $fieldData) {
			$field = new Field();
			$field->setProperties($fieldData);
			$collection->add($field);
		}
		return $collection;
	}
}