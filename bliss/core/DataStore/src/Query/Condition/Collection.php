<?php
namespace DataStore\Query\Condition;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a condition to the collection
	 * 
	 * @param \DataStore\Query\Condition\Condition $condition
	 */
	public function add(Condition $condition)
	{
		$this->addItem($condition);
	}
	
	/**
	 * Create a new collection instance from an array of items
	 * 
	 * @param array $conditions
	 * @return \DataStore\Query\Condition\Collection
	 */
	public static function factory(array $conditions) 
	{
		$collection = new self();
		foreach ($conditions as $data) {
			$condition = Condition::factory($data);
			$collection->add($condition);
		}
		return $collection;
	}
}