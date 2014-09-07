<?php
namespace DataStore;

interface DataStoreInterface
{
	/**
	 * @param string $resourceName
	 * @return \DataStore\Query\Query
	 */
	public function createQuery($resourceName);
	
	/**
	 * @param string $resourceName
	 * @param callable $callback 
	 */
	public function registerResource($resourceName, $callback);
}