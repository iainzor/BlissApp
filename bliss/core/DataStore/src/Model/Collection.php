<?php
namespace DataStore\Model;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a result to the collection
	 * 
	 * @param \DataStore\Model\Model $model
	 */
	public function add(Model $model)
	{
		$model->setParentCollection($this);
		
		$this->addItem($model);
	}
	
	/**
	 * Set the property name to the value of all models in the collection
	 * 
	 * @param string $propertyName
	 * @param mixed $value
	 */
	public function set($propertyName, $value)
	{
		$this->each(function(Model $model) use ($propertyName, $value) {
			$model->set($propertyName, $value);
		});
	}
	
	/**
	 * Generate a new result collection from an array of result data
	 * 
	 * @param array $results
	 * @return \DataStore\Model\Collection
	 */
	public static function generate(array $results = null, $modelClass = null, $collectionClass = null)
	{
		$modelClass = isset($modelClass) ? $modelClass : "\\DataStore\\Model\\Model";
		$collectionClass = isset($collectionClass) ? $collectionClass : "\\DataStore\\Model\\Collection";
		$collection = new $collectionClass();
		
		if ($results !== null) {
			foreach ($results as $row) {
				$result = new $modelClass();
				$result->setProperties($row);

				$collection->add($result);
			}
		}
		
		return $collection;
	}
}