<?php
namespace DataStore\Query\Field;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a field to the collection
	 * 
	 * @param \DataStore\Query\Field\Field $field
	 */
	public function add(Field $field)
	{
		$this->addItem($field);
	}
	
	/**
	 * Find a single field by it's name/alias
	 * 
	 * @param string $fieldName
	 * @return \DataStore\Query\Field\Field
	 * @throws \Exception
	 */
	public function findByName($fieldName)
	{
		foreach ($this->getItems() as $field) {
			if ($field->getAlias() === $fieldName) {
				return $field;
			}
		}
		
		throw new \Exception("Could not find the field '{$fieldName}'");
	}
	
	/**
	 * Check if a field exists
	 * 
	 * @param string $fieldName
	 * @return boolean
	 */
	public function isField($fieldName)
	{
		try {
			$this->findByName($fieldName);
		} catch (\Exception $ex) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Create a new collection from an array of field definitions
	 * 
	 * @param array $fields
	 * @return \DataStore\Query\Field\Collection
	 */
	public static function factory(array $fields)
	{
		$collection = new self();
		foreach ($fields as $data) {
			$field = Field::factory($data);
			$collection->add($field);
		}
		return $collection;
	}
	
	/**
	 * Generate a new collection of fields from an array of field names
	 * 
	 * @param array $fieldNames
	 * @return \DataStore\Query\Field\Collection
	 */
	public static function fromList(array $fieldNames)
	{
		$fields = [];
		foreach ($fieldNames as $name) {
			$fields[] = [
				"name" => $name
			];
		}
		return self::factory($fields);
	}
}