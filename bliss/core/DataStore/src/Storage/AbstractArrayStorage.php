<?php
namespace DataStore\Storage;

use DataStore\Query\Query,
	DataStore\Query\Condition\Condition,
	DataStore\Query\Field\Field,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection;

abstract class AbstractArrayStorage extends AbstractStorage
{
	const ID = "__index__";
	
	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var int
	 */
	protected $lastInsertedId = 0;
	
	/**
	 * Constructor
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = [])
	{
		$this->setData($data);
	}
	
	/**
	 * Get the ID of the last record to be inserted
	 * 
	 * @return int
	 */
	public function getLastInsertedId() 
	{
		return $this->lastInsertedId;
	}
	
	/**
	 * Set the data available to the storage
	 * Each item will be assigned a property designating its unique index
	 * 
	 * @param array $data
	 */
	public function setData(array $data)
	{
		foreach ($data as $i => $datum) {
			$data[$i][self::ID] = $i;
		}
		
		$this->data = $data;
	}
	
	/**
	 * Count the number of results found with the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return int
	 */
	public function count(Query $query = null) 
	{
		$results = $this->findAll($query);
		return count($results);
	}

	/**
	 * Add a new record to the data array
	 * 
	 * @param \DataStore\Model\Model $record
	 * @return int The index of the record
	 */
	public function insert(Model $record) 
	{
		$i = count($this->data);
		$record[self::ID] = $i;
		
		$this->data[$i] = $record->toArray();
		
		return $i;
	}

	/**
	 * Add multiple records to the data array
	 * 
	 * @param \DataStore\Model\Collection $records
	 * @return array A collection of indexes of the records created
	 */
	public function insertAll(ModelCollection $records) 
	{
		$indexes = [];
		foreach ($records as $record) {
			$indexes[] = $this->insert($record);
		}
		return $indexes;
	}
	
	/**
	 * Delete the first record matching the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return boolean TRUE on success or FALSE on failure
	 */
	public function delete(Query $query) 
	{
		$query->setMaxResults(1);
		$count = $this->deleteAll($query);
		
		return $count === 1;
	}
	
	/**
	 * Delete all records found using the query
	 * If no query is provided, all records will be deleted
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return int The number of records deleted
	 */
	public function deleteAll(Query $query = null) 
	{
		
	}

	/**
	 * Find the first record matching the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return null|array Either returns the record or null if a record could not be found
	 */
	public function find(Query $query = null) 
	{
		if ($query === null) {
			$query = new Query();
		}
		
		$query->setMaxResults(1);
		$results = $this->findAll($query);
		
		return count($results) > 0 ? array_shift($results) : null;
	}

	/**
	 * Find all records matching the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return array A collection of records found
	 */
	public function findAll(Query $query = null) 
	{
		if ($query === null) {
			return $this->data;
		} else {
			return $this->_filter($query);
		}
	}

	/**
	 * Update a record in the data array
	 * 
	 * @param array $record
	 * @param array $params
	 * @return boolean Returns TRUE if the record was updated or FALSE otherwise
	 */
	public function update(Model $record) 
	{}
	
	
	public function replace(Model $record) 
	{}
	
	
	/**
	 * Filter all items in the array that match the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return array
	 */
	private function _filter(Query $query)
	{
		$results = [];
		
		foreach ($this->data as $item) {
			if ($this->_matchesItem($query, $item)) {
				$results[] = $item;
			}
		}
		
		return $results;
	}
	
	/**
	 * Check if an item matches the query
	 * 
	 * @param \DataStore\Query\Query $query
	 * @param array $item
	 * @return boolean
	 */
	private function _matchesItem(Query $query, array $item)
	{
		foreach ($query->getConditions() as $condition) {
			$matchesMax = 0;
			$matchesFound = 0;
			
			foreach ($item as $fieldName => $value) {
				if ($condition->getField()->getAlias() !== $fieldName) {
					continue;
				}
				
				$matchesMax += 1;
				$field = $query->getField($fieldName);
				$field->setValue($value);
				
				if ($this->_matchesField($condition, $field)) {
					$matchesFound += 1;
				}
			}
		}
		
		return $matchesMax > 0 && $matchesFound === $matchesMax;
	}
	
	/**
	 * Check if a field's value matches a condition
	 *  
	 * @param \DataStore\Query\Condition\Condition $condition
	 * @param \DataStore\Query\Field\Field $field
	 * @return boolean
	 */
	private function _matchesField(Condition $condition, Field $field)
	{
		$fieldValue = $field->getValue();
		$compareValue = $condition->getValue();
		
		switch ($condition->getOperator()) {
			case Condition::EQUAL_TO: 
				return $compareValue === $fieldValue;
			case Condition::NOT_EQUAL_TO:
				return $compareValue !==  $fieldValue;
			case Condition::GREATER_THAN:
				return $compareValue > $fieldValue;
			case Condition::GREATER_OR_EQUAL_TO:
				return $compareValue >= $fieldValue;
			case Condition::LESS_THAN:
				return $compareValue < $fieldValue;
			case Condition::LESS_OR_EQUAL_TO:
				return $compareValue <= $fieldValue;
			case Condition::CONTAINS:
				return stristr($fieldValue, $compareValue) !== false;
			default:
				return false;
		}
	}
}