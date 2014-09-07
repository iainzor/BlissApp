<?php
namespace DataStore\Resource;

use DataStore\Model\Collection as ModelCollection;

class Collection
{
	/**
	 * @var array
	 */
	private $resources = [];
	
	/**
	 * Register a resource
	 * 
	 * @param string $resourceName
	 * @param callable $callback A callback function that must return an instance of \DataStore\Resource\QueryInterface
	 * @see \DataStore\Resource\ResourceInterface
	 * @throws \InvalidArgumentException
	 */
	public function register($resourceName, $callback)
	{
		if (!is_callable($callback)) {
			throw new \InvalidArgumentException("Second paramter must be a callable, ". gettype($callback) ." provided");
		}
		
		$this->resources[$resourceName] = $callback;
	}
	
	/**
	 * Attempt to get a query for a resource 
	 * 
	 * @param string $resourceName
	 * @return \DataStore\Query\Query
	 * @throws \Exception
	 */
	public function createQuery($resourceName)
	{
		if (!isset($this->resources[$resourceName])) {
			throw new \Exception("Resource has not been registered: {$resourceName}");
		}
		
		$callback = $this->resources[$resourceName];
		$resource = call_user_func($callback);
		
		if (!($resource instanceof ResourceInterface)) {
			throw new \Exception("Resource must be an instance of \\DataStore\\Resource\\ResourceInterface, ". get_class($resource) ." provided");
		}
		
		return $resource->createQuery();
	}
	
	/**
	 * Load and assign any resources belonging to $models
	 * 
	 * @param string $resourceName
	 * @param \DataStore\Model\Collection $models
	 * @return void
	 */
	public function assignResource($resourceName, ModelCollection $models)
	{
		$ids = [];
		
		try {
			$query = $this->getQuery($resourceName);
		} catch (\Exception $e) {
			return;
		}
		
		foreach ($models as $model) {
			if ($model->getResourceName() === $resourceName) {
				$ids[] = $model->getResourceId();
			}
		}

		if (count($ids)) {
			$query->fields("id")->compareTo($ids);
			$results = $query->loadAll();
			$resources = new ModelCollection();
			
			foreach ($results as $result) {
				$resource = $query->toResource($result);
				$resources->add($resource);
			}
			
			$models->assignProperty("resource", $resources, "id", "resourceId");
		}
	}
}