<?php
namespace DataStore\Loader;

use DataStore\Query,
	DataStore\Query\Relation,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection,
	DataStore\Resource\Registry;

abstract class AbstractLoader implements LoaderInterface 
{
	/**
	 * Collection of Transform instances to run on the resulting data
	 * 
	 * @var type 
	 */
	protected $transforms = [];
	
	/**
	 * Add a transform callback used to process the resulting data
	 * 
	 * @param callable $callback
	 */
	public function transform($callback)
	{
		if (!is_callable($callback)) {
			throw new \InvalidArgumentException('$callback must be callable');
		}
		
		$this->transforms[] = $callback;
	}
	
	/**
	 * Run the loader's transformations on a collection of results
	 * 
	 * @param \DataStore\Model\Collection $results
	 */
	public function transformResults(ModelCollection $results)
	{
		if (count($this->transforms) === 0) {
			return $results;
		}
		
		$transformed = clone $results;
		$transformed->clearItems();
		
		foreach ($this->transforms as $transform) {
			$results->each(function(Model $model) use ($transform, $transformed) {
				$newModel = call_user_func($transform, $model);
				$transformed->add($newModel);
			});
		}
		
		return $transformed;
	}
	
	/**
	 * Load a single result from a query
	 * Returns NULL if a result could not be found
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return \DataStore\Result\Result|null
	 */
	public function load(Query\Query $query) 
	{
		$query->setMaxResults(1);
		$all = $this->loadAll($query);
		
		return $all->isEmpty() ? null : $all->getFirstItem();
	}

	/**
	 * Load all results found from a query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return \DataStore\Result\Collection
	 */
	public function loadAll(Query\Query $query) 
	{
		$storage = $query->getStorage();
		$rawResults = ModelCollection::generate(
			$storage->findAll($query),
			$query->getModelClass(),
			$storage->getCollectionClass()
		);
		
		$query->setTotalResults(count($rawResults));
		$query->setTotalCalcResults($storage->getFoundRows());
		
		$results = $this->transformResults($rawResults);
		$this->attachRelationships($results, $query);
		$this->attachResources($results);
		
		$query->parseResults($results);
		
		$results->each(function($result) use ($query) {
			$result->setQuery($query);
		});
		
		return $results;
	}
	
	/**
	 * Attach all relationships to a model collection
	 * 
	 * @param \DataStore\Model\Collection $models
	 * @param \DataStore\Query\Query $query
	 */
	public function attachRelationships(ModelCollection $models, Query\Query $query)
	{
		$relationships = $query->getRelationships();
		
		foreach ($relationships as $relation) {
			if ($relation->isEnabled()) {
				$this->_attachRelation($models, $relation);
			}
		}
	}
	
	/**
	 * Attach a resource to each result that has the resourceName and resourceId properties
	 * 
	 * @param \DataStore\Model\Collection $models
	 */
	public function attachResources(ModelCollection $models)
	{
		$resourceNames = $models->collectProperty("resourceName");
		$registry = Registry::instance();
		
		foreach ($resourceNames as $resourceName) {
			$registry->assignResource($resourceName, $models);
		}
	}
	
	/**
	 * Attach a single relation to a model collection
	 * 
	 * @param \DataStore\Model\Collection $models
	 * @param \DataStore\Query\Relation\RelationInterface $relation
	 */
	private function _attachRelation(ModelCollection $models, Relation\RelationInterface $relation)
	{
		$localKey = $relation->getLocalKey();
		$foreignKey = $relation->getForeignKey();
		$query = $relation->getQuery();
		$propertyName = $relation->getName();
		$relationsFound = 0;
		
		foreach ($localKey as $i => $lk) {
			$localValues = $models->collectProperty($lk);
			
			if (count($localValues) === 0) {
				continue;
			}
			
			$fk = isset($foreignKey[$i]) ? $foreignKey[$i] : $foreignKey[0];
			$query->fields($fk)->compareTo($localValues);
			$relationsFound += 1;
		}
		
		if ($relationsFound === 0) {
			return;
		}
		
		$relationResults = $query->loadAll();
		
		if ($relation instanceof Relation\OneToOne) {
			$models->assignProperty($propertyName, $relationResults, $foreignKey, $localKey);
		} else if ($relation instanceof Relation\OneToMany) {
			$models->assignAsCollection($propertyName, $relationResults, $foreignKey, $localKey);
		}
	}
}