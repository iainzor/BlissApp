<?php
namespace DataStore\Query\Group;

class Group extends \Bliss\Component
{
	/**
	 * @var string
	 */
	protected $fieldName = null;
	
	/**
	 * Get the name of the field to group by
	 * 
	 * @return string
	 */
	public function getFieldName() 
	{
		return $this->fieldName;
	}

	/**
	 * Set the name of the field to group by
	 * 
	 * @param string $field
	 */
	public function setFieldName($field)
	{
		$this->fieldName = $fieldName;
	}
	
	/**
	 * Generate a new group instance from an array of properties
	 * 
	 * @param array $properties
	 * @return \DataStore\Query\Group\Group
	 */
	public static function factory(array $properties)
	{
		$group = new self();
		$group->setProperties($properties);
		
		return $group;
	}
}