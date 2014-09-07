<?php
namespace DataStore;

class Module extends \Bliss\Module\AbstractModule implements DataStoreInterface
{
	const NAME = "data-store";
	
	/**
	 * @var \DataStore\Resource\Collection
	 */
	private $resources;
	
	public function getName() { return self::NAME; }
	
	public function init()
	{
		$this->resources = new Resource\Collection();
	}
	
	/**
	 * @param string $resourceName
	 * @return \DataStore\Query\Query
	 */
	public function createQuery($resourceName)
	{
		return $this->resources->createQuery($resourceName);
	}
	
	/**
	 * Register a resource with the DataStore
	 * 
	 * @param string $resourceName
	 * @param callable $callback Callable that must return an instance of \DataStore\Resource\QueryInterface
	 */
	public function registerResource($resourceName, $callback)
	{
		$this->resources->register($resourceName, $callback);
	}
}