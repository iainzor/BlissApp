<?php
namespace DataStore\Query\Relation;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a relationship to the collection
	 * 
	 * @param \DataStore\Query\Relation\RelationInterface $relation
	 */
	public function add(RelationInterface $relation)
	{
		$this->addItem($relation);
	}
	
	/**
	 * Attempt to get a relation by its name
	 * 
	 * @param string $name
	 * @return \DataStore\Query\Relation\RelationInterface
	 * @throws \Exception
	 */
	public function get($name)
	{
		foreach ($this->getItems() as $relation) {
			if (strtolower($relation->getName()) == strtolower($name)) {
				return $relation;
			}
		}
		
		throw new \Exception("A relation by the name '{$name}' could not be found");
	}
	
	/**
	 * Check if a relationship exists
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function exists($name)
	{
		try {
			$this->get($name);
		} catch (\Exception $e) {
			return false;
		}
		
		return true;
	}
}