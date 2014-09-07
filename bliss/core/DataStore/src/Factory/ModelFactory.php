<?php
namespace DataStore\Factory;

use DataStore\Query\Query,
	DataStore\Query\Relation,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection;

class ModelFactory
{
	/**
	 * @var \DataStore\Query\Query
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param \DataStore\Query\Query $query
	 */
	public function __construct(Query $query)
	{
		$this->query = $query;
	}
	
	/**
	 * Generate a full model tree for an array of data
	 * 
	 * @param array $data
	 * @return \DataStore\Model\Model
	 */
	public function generate(array $data)
	{
		$model = $this->createModel();
		$relationships = $this->query->getRelationships();
		
		foreach ($data as $name => $value) {
			if (is_array($value) && empty($value)) {
				continue;
			} 
			
			if ($relationships->exists($name) && $value !== null) {
				$value = $this->generateRelationModel(
					$relationships->get($name),
					$value
				);
			}
			
			$model->set($name, $value);
		}
		
		return $model;
	}
	
	/**
	 * Generate a new model based on a relation to the query
	 * 
	 * @param \DataStore\Query\Relation\RelationInterface $relation
	 * @param array $data
	 * @return \DataStore\Model\Model|\DataStore\Model\Collection
	 */
	public function generateRelationModel(Relation\RelationInterface $relation, array $data)
	{
		$query = $relation->getQuery();
		$storage = $query->getStorage();
		$collectionClass = $storage->getCollectionClass() != null ? $storage->getCollectionClass() : "\\DataStore\\Model\\Collection";
		$factory = new self($query);
		
		if ($relation instanceof Relation\OneToOne) {
			$model = $factory->generate($data);
			return $model;
		} else if ($relation instanceof Relation\OneToMany) {
			$collection = new $collectionClass();
			foreach ($data as $item) {
				$model = $factory->generate($item);
				$collection->add($model);
			}
			return $collection;
		}
		
		throw new \Exception("Invalid relation type: ". get_class($relation));
	}
	
	/**
	 * Create an empty model
	 * 
	 * @return \DataStore\Model\Model
	 */
	public function createModel()
	{
		$modelClass = $this->query->getModelClass();
		
		if ($modelClass === null) {
			$model = new Model();
		} else {
			$model = new $modelClass();
		}
		
		return $model;
	}
}