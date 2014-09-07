<?php
namespace DataStore\Query;

use DataStore\Storage\StorageInterface,
	DataStore\Loader\GenericLoader,
	DataStore\Saver\GenericSaver,
	DataStore\Factory\ModelFactory,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection;

class Query extends \Bliss\Component
{
	/**
	 * @var \DataStore\Storage\StorageInterface
	 */
	protected $storage;
	
	/**
	 * @var int
	 */
	protected $maxResults = 0;
	
	/**
	 * @var int
	 */
	protected $resultOffset = 0;
	
	/**
	 * @var int
	 */
	protected $totalResults = 0;
	
	/**
	 * @var int
	 */
	protected $totalCalcResults = 0;
	
	/**
	 * @var \DataStore\Query\Field\Collection
	 */
	private $fields;
	
	/**
	 * @var \DataStore\Query\Join\Collection
	 */
	private $joins;
	
	/**
	 * @var \DataStore\Query\Condition\Collection
	 */
	private $conditions;
	
	/**
	 * @var \DataStore\Query\Group\Collection
	 */
	private $groups;
	
	/**
	 * @var \DataStore\Query\Order\Collection
	 */
	private $orders;
	
	/**
	 * @var \DataStore\Query\Relation\Collection
	 */
	private $relationships;
	
	/**
	 * @var string
	 */
	private $modelClass;
	
	/**
	 * @var boolean
	 */
	private $calculateFoundRows = false;
	
	/**
	 * Constructor
	 * 
	 * @param \DataStore\Storage\StorageInterface $storage
	 */
	public function __construct(StorageInterface $storage) 
	{
		$this->storage = $storage;
		$this->fields = new Field\Collection();
		$this->joins = new Join\Collection();
		$this->conditions = new Condition\Collection();
		$this->groups = new Group\Collection();
		$this->orders = new Order\Collection();
		$this->relationships = new Relation\Collection();
		$this->modelClass = $storage->getModelClass();
	}
	
	/**
	 * Set the query's storage instance
	 * 
	 * @param \DataStore\Storage\StorageInterface $storage
	 */
	public function setStorage(StorageInterface $storage)
	{
		$this->storage = $storage;
	}
	
	/**
	 * Get the instance of the storage for the join
	 * 
	 * @return \DataStore\Storage\StorageInterface
	 */
	public function getStorage()
	{
		return $this->storage;
	}
	
	/**
	 * Get the maximum number of results the query should return
	 * 
	 * @return int
	 */
	public function getMaxResults() 
	{
		return $this->maxResults;
	}
	
	/**
	 * Set the maximum number of results the query should return
	 * 
	 * @param int $maxResults
	 */
	public function setMaxResults($maxResults) 
	{
		$this->maxResults = (int) $maxResults;
	}

	/**
	 * Get the number of results to offset the query by
	 * 
	 * @return int
	 */
	public function getResultOffset() 
	{
		return $this->resultOffset;
	}

	/**
	 * Set the number of results to offset the query by
	 * 
	 * @param iny $resultOffset
	 */
	public function setResultOffset($resultOffset) 
	{
		$this->resultOffset = (int) $resultOffset;
	}
	
	/**
	 * Set the total number of results
	 * 
	 * @param int $totalResults
	 */
	public function setTotalResults($totalResults)
	{
		$this->totalResults = (int) $totalResults;
	}
	
	/**
	 * Get the total number of results
	 * 
	 * @return int
	 */
	public function getTotalResults()
	{
		return $this->totalResults;
	}
	
	/**
	 * Get the total calculated number of results beyond what has loaded
	 * 
	 * @return int
	 */
	public function getTotalCalcResults() 
	{
		return $this->totalCalcResults;
	}
	
	/**
	 * Set the total calculated number of results
	 * 
	 * @param int $totalCalcResults
	 */
	public function setTotalCalcResults($totalCalcResults) 
	{
		$this->totalCalcResults = (int) $totalCalcResults;
	}

		
	/**
	 * Get the fields to fetch from the query
	 * 
	 * @return \DataStore\Query\Field\Collection
	 */
	public function getFields()
	{
		return $this->fields;
	}
	
	/**
	 * Get a single field from the query
	 * 
	 * @param string|array $field
	 */
	public function getField($field)
	{
		if (is_array($field)) {
			list($storage, $fieldName) = $field;
		} else {
			$storage = $this->storage;
			$fieldName = $field;
		}
		
		if ($this->fields->isField($fieldName)) {
			return $this->fields->findByName($fieldName);
		}
		
		foreach ($this->joins as $join) {
			if ($join->hasField($fieldName)) {
				$field = $join->getField($fieldName);
				$this->fields->add($field);
				
				return $field;
			}
		}
		
		$field = new Field\Field();
		$field->setName($fieldName);
		$field->setSourceName($storage->getAlias());
			
		$this->fields->add($field);
		
		return $field;
	}
	
	/**
	 * Set the fields to fetch from the query
	 * 
	 * @param \DataStore\Query\Field\Collection $fields
	 */
	public function setFields(Field\Collection $fields)
	{
		$this->fields = $fields;
	}
	
	/**
	 * Get one or all fields from the query
	 * 
	 * @param string|array $fieldName
	 * @return \DataStore\Query\Field\Field|\DataStore\Query\Field\Collection
	 */
	public function fields($fieldName = null)
	{
		if ($fieldName !== null) {
			return $this->getField($fieldName);
		} else {
			return $this->fields;
		}
	}
	
	/**
	 * Check if the query has a field set
	 * 
	 * @param string $fieldName
	 * @return boolean
	 */
	public function hasField($fieldName)
	{
		$isField = $this->fields->isField($fieldName);
		
		if (!$isField) {
			foreach ($this->joins as $join) {
				if ($join->hasField($fieldName)) {
					$isField = true;
				}
			}
		}
		
		return $isField;
	}
	
	/**
	 * Clear all fields in the query
	 */
	public function clearFields()
	{
		$this->fields->clearItems();
	}
	
	/**
	 * Get the storages to join onto the query
	 * 
	 * @return \DataStore\Query\Join\Collection
	 */
	public function getJoins()
	{
		return $this->joins;
	}
	
	/**
	 * Set the storages to join onto the query
	 * 
	 * @param \DataStore\Query\Join\Collection $joins
	 */
	public function setJoins(Join\Collection $joins)
	{
		$this->joins = $joins;
	}
	
	/**
	 * Join another storage to the query
	 * 
	 * @param \DataStore\Storage\StorageInterface $storage
	 * @param string $foreignKey
	 * @param string $localKey
	 * @param string $joinType
	 * @return \DataStore\Query\Join\Join
	 */
	public function join(StorageInterface $storage, $foreignKey, $localKey, $joinType = Join\Join::TYPE_DEFAULT)
	{
		$join = new Join\Join($storage);
		$join->setForeignKey($foreignKey);
		$join->setLocalKey($localKey);
		$join->setType($joinType);
		
		$this->joins->add($join);
		
		return $join;
	}
	
	/**
	 * Get the conditions used to filter the results of the query
	 * 
	 * @return \DataStore\Query\Condition\Collection
	 */
	public function getConditions()
	{
		$conditions = clone $this->conditions;
		foreach ($this->fields as $field) {
			$conditions->merge($field->getConditions());
		}
		
		return $conditions;
	}
	
	/**
	 * Set the conditions used to filter the results of the query
	 * 
	 * @param \DataStore\Query\Condition\Collection $conditions
	 */
	public function setConditions(Condition\Collection $conditions)
	{
		$this->conditions = $conditions;
	}
	
	/**
	 * Clear all conditions for the query
	 */
	public function clearConditions()
	{
		$this->conditions->clearItems();
	}
	
	/**
	 * Get the fields the query should group the results by
	 * 
	 * @return \DataStore\Query\Group\Collection
	 */
	public function getGroups()
	{
		return $this->groups;
	}
	
	/**
	 * Set the fields the query should group the results by
	 * 
	 * @param \DataStore\Query\Group\Collection $groups
	 */
	public function setGroups(Group\Collection $groups)
	{
		$this->groups = $groups;
	}
	
	/**
	 * Get the fields the query should order the results by
	 * 
	 * @return \DataStore\Query\Order\Collection
	 */
	public function getOrders()
	{
		return $this->orders;
	}
	
	/**
	 * Set the fields the query should order the results by
	 * 
	 * @param \DataStore\Query\Order\Collection $orders
	 */
	public function setOrders(Order\Collection $orders)
	{
		$this->orders = $orders;
	}
	
	/**
	 * Create a new one-to-one relationship with this query and another one
	 * 
	 * $foreignKey and $localKey can either be a string or an array
	 * 
	 * If no $localKey is provided, it will be the same as $foreignKey
	 * 
	 * @param string $propertyName
	 * @param \DataStore\Query\Query $query
	 * @param string|array $foreignKey
	 * @param string|array $localKey
	 * @return \DataStore\Query\Relation\OneToOne
	 */
	public function hasOne($propertyName, Query $query, $foreignKey, $localKey = null)
	{
		if ($localKey === null) {
			$localKey = $foreignKey;
		}
		
		$relation = new Relation\OneToOne($query);
		$relation->setName($propertyName);
		$relation->setForeignKey($foreignKey);
		$relation->setLocalKey($localKey);
		
		$this->relationships->add($relation);
		
		return $relation;
	}
	
	/**
	 * Create a new one-to-many relationship with this query and another one
	 * 
	 * @param string $propertyName
	 * @param \DataStore\Query\Query $query
	 * @param string|array $foreignKey
	 * @param string|array $localKey
	 * @return \DataStore\Query\Relation\OneToMany
	 */
	public function hasMany($propertyName, Query $query, $foreignKey, $localKey)
	{
		$relation = new Relation\OneToMany($query);
		$relation->setName($propertyName);
		$relation->setForeignKey($foreignKey);
		$relation->setLocalKey($localKey);
		
		$this->relationships->add($relation);
		
		return $relation;
	}
	
	/**
	 * Get all available relationships for the query
	 * 
	 * @return \DataStore\Query\Relation\Collection
	 */
	public function getRelationships()
	{
		return $this->relationships;
	}
	
	/**
	 * Attempt to get a relation from the query
	 * 
	 * @param string $relationName
	 * @return \DataStore\Query\Relation\RelationInterface
	 */
	public function getRelation($relationName)
	{
		return $this->relationships->get($relationName);
	}
	
	/**
	 * Add a relation to the query
	 * 
	 * @param \DataStore\Query\Relation\RelationInterface $relation
	 */
	public function addRelation(Relation\RelationInterface $relation)
	{
		$this->relationships->add($relation);
	}
	
	/**
	 * Set the search parameters for the query
	 * 
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$fields = $this->storage->getFields();
		
		foreach ($params as $fieldName => $value) {
			if ($fields && !$fields->isField($fieldName)) {
				continue;
			}
			
			$this->fields($fieldName)->compareTo($value);
		}
	}
	
	/**
	 * Get the class name used to generate models for the query
	 * 
	 * @return string
	 */
	public function getModelClass() 
	{
		return $this->modelClass;
	}

	/**
	 * Set the model class used to generate models for the query
	 * 
	 * @param string $modelClass
	 */
	public function setModelClass($modelClass) 
	{
		$this->modelClass = $modelClass;
	}
		
	/**
	 * Generate a full model tree for an array of data
	 * 
	 * @param array $data
	 * @return \DataStore\Model\Model
	 */
	public function generateModel(array $data)
	{
		$factory = new ModelFactory($this);
		$model = $factory->generate($data);
		$model->setQuery($this);
		
		return $model;
	}
	
	/**
	 * Set whether the query should calculate the total number of rows found
	 * 
	 * @param boolean $flag
	 */
	public function setCalculateFoundRows($flag = true)
	{
		$this->calculateFoundRows = (boolean) $flag;
	}
	
	/**
	 * Check if the query should calculate the the total number of rows found
	 * 
	 * @return boolean
	 */
	public function calculateFoundRows()
	{
		return $this->calculateFoundRows;
	}
	
	/**
	 * Use a generic loader to load all results for the query
	 * 
	 * @return \DataStore\Result\Collection
	 */
	public function loadAll()
	{
		$loader = new GenericLoader();
		return $loader->loadAll($this);
	}
	
	/**
	 * Use a generic loader to loader a single result for the query
	 * 
	 * @return \DataStore\Result\Model
	 */
	public function load()
	{
		$loader = new GenericLoader();
		return $loader->load($this);
	}
	
	/**
	 * Save data using the query
	 * 
	 * @param \DataStore\Result\Model $model
	 * @param boolean $recursive Whether to recursively save all relationships
	 * @return \DataStore\Result\Model
	 */
	public function save(Model $model, $recursive = false)
	{
		$collection = new ModelCollection();
		$collection->add($model);
		$this->saveAll($collection);
		
		if ($recursive === true) {
			$saver = new GenericSaver();
			$saver->saveRelationships($model, $this->getRelationships());
		}
		
		return $model;
	}
	
	/**
	 * Save all models provided
	 * 
	 * @param \DataStore\Model\Collection $models
	 */
	public function saveAll(ModelCollection $models)
	{
		$saver = new GenericSaver();
		$saver->saveAll($this, $models);
	}
	
	/**
	 * Insert or replace a model
	 * 
	 * @param \DataStore\Model\Model $model
	 */
	public function replace(Model $model)
	{
		$saver = new GenericSaver();
		$saver->replace($this, $model);
	}
	
	/**
	 * Delete the next record matching the query
	 * 
	 * @return boolean
	 */
	public function delete()
	{
		return $this->storage->delete($this);
	}
	
	/**
	 * Delete all records matching the query
	 * 
	 * @return int The number of rows deleted
	 */
	public function deleteAll()
	{
		return $this->storage->deleteAll($this);
	}
	
	/**
	 * Placeholder method for subclasses used to parse any results found
	 * 
	 * @param \DataStore\Model\Collection $results
	 */
	public function parseResults(ModelCollection $results)
	{}
	
	public function toArray() {
		$data = parent::toArray();
		unset($data["storage"]);
		
		return $data;
	}
}