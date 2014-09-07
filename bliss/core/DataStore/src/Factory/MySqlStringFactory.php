<?php
namespace DataStore\Factory;

use DataStore\Query\DbQuery,
	DataStore\Query\Query,
	DataStore\Query\Condition\DbCondition,
	DataStore\Query\Join\Join,
	DataStore\Query\Join\DbJoin,
	DataStore\Query\Field\Field,
	DataStore\Query\Field\Collection as FieldCollection,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection,
	DataStore\Storage\AbstractDbStorage;

class MySqlStringFactory
{
	/**
	 * @var \PDO
	 */
	private $db;
	
	/**
	 * Constructor
	 * 
	 * @param \PDO $db
	 */
	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}
	
	/**
	 * Generate a SELECT statement for  a query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return string
	 */
	public function generateSelectStatement(Query $query)
	{
		$selectClause = $query->calculateFoundRows() ? "SELECT SQL_CALC_FOUND_ROWS" : "SELECT";
		$fieldClause = $this->generateFieldClause($query);
		$fromClause = $this->generateFromClause($query);
		$joinClause = $this->generateJoinClause($query);
		$whereClause = $this->generateWhereClause($query);
		$limitClause = $this->generateLimitClause($query);
		
		$statement	= $selectClause
					." {$fieldClause}"
					." {$fromClause}";
		
		if (strlen($joinClause)) {
			$statement .= " {$joinClause}";
		}
		
		if (strlen($whereClause)) {
			$statement .= " {$whereClause}";
		}
		
		if (strlen($limitClause)) {
			$statement .= " {$limitClause}";
		}
		
		return $statement;
	}
	
	/**
	 * Generate an INSERT statement for one or more records
	 * 
	 * @param \DataStore\Storage\AbstractDbStorage $storage
	 * @param \DataStore\Model\Collection|\DataStore\Model\Model $records
	 */
	public function generateInsertStatement(AbstractDbStorage $storage, $records, $replace = false)
	{
		if ($records instanceof Model) {
			$collection = new ModelCollection();
			$collection->add($records);
			$records = $collection;
		}
		
		if (!($records instanceof ModelCollection)) {
			throw new \UnexpectedValueException("Expecting instance of \\DataStore\\Model\\Model or \\DataStore\\Model\\Collection");
		}
		
		if (empty($records)) {
			throw new \Exception("No records were provided");
		}
		
		$tableName = $storage->getName();
		$fieldList = $this->generateFieldList($records, $storage->getFields(), ["id"]);
		$valuesList = $this->generateValuesList($records, $storage->getFields());
		
		array_walk($fieldList, function(&$fieldName) { $fieldName = "`{$fieldName}`"; });
		array_walk($valuesList, function(array &$values) use ($storage) {
			array_walk($values, function(&$value) use ($storage) {
				if ($value === null) {
					$value = "NULL";
				} else {
					$value = $storage->getDb()->quote($value);
				}
			});
			
			$values = "(". implode(", ", $values) .")";
		});
		
		$insert = $replace === true ? "REPLACE" : "INSERT";
		$statement = "{$insert} INTO `{$tableName}`"
				   . " (". implode(", ", $fieldList) .")"
				   . " VALUES ". implode(", ", $valuesList);
		
		return $statement;
	}
	
	/**
	 * Generate an UPDATE statement for a single model
	 * 
	 * @param \DataStore\Model\Model $record
	 * @param \DataStore\Query\Query $query
	 * @return string
	 */
	public function generateUpdateStatement(Model $record, Query $query)
	{
		$storage = $query->getStorage();
		$db = $this->db;
		$tableName = $storage->getName();
		$valuePairs = $this->generateValuePairs($record, $storage->getFields(), ["id"]);
		$whereClause = $this->generateWhereClause($query);
		
		array_walk($valuePairs, function(&$value, $fieldName) use ($db) {
			if ($value === null) {
				$value = "NULL";
			} else {
				$value = $db->quote($value);
			}	
			
			$value = "`{$fieldName}` = {$value}";
		});
		
		$statement = "UPDATE `{$tableName}`"
				   . " SET ". implode(", ", $valuePairs);
		
		if (strlen($whereClause)) {
			$statement .= " {$whereClause}";
		}
		
		return $statement;
	}
	
	/**
	 * Generate a REPLACE statment
	 * 
	 * @param \DataStore\Storage\AbstractDbStorage $storage
	 * @param \DataStore\Model\Model $record
	 * @return string
	 */
	public function generateReplaceStatement(AbstractDbStorage $storage, Model $record)
	{
		return $this->generateInsertStatement($storage, $record, true);
	}
	
	/**
	 * Generate a DELETE statement for query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return string
	 */
	public function generateDeleteStatement(Query $query)
	{
		$fromClause = $this->generateFromClause($query);
		$whereClause = $this->generateWhereClause($query);
		$limitClause = $query->getMaxResults() > 0 ? "LIMIT {$query->getMaxResults()}": null;
		
		$statement = "DELETE {$fromClause}";
		
		if (strlen($whereClause)) {
			$statement .= " {$whereClause}";
		}
		
		if (strlen($limitClause)) {
			$statement .= " {$limitClause}";
		}
		
		return $statement;
	}
	
	/**
	 * Generate the list of fields for a SELECT statement
	 * 
	 * @param \DataStore\Query\DbQuery $query
	 * @return string
	 */
	public function generateFieldClause(Query $query)
	{
		$fields = $query->getFields();
		$storage = $query->getStorage();
		$mainSource = $storage->getAlias();
		$pairs = ["`{$mainSource}`.*"];
		
		foreach ($fields as $field) {
			$subQuery = $field->getQuery();
			$sourceName = $field->getSourceName();
			$name = $field->getName();
			$alias = $field->getAlias();
			
			if ($subQuery) {
				$pairs[] = "(". $this->generateSelectStatement($subQuery) .") AS `{$alias}`";
				continue;
			}
			
			if ($sourceName === $mainSource && $name === $alias) {
				continue;
			}
			
			switch ($name) {
				case Field::ALL:
					$pairs[] = "`{$sourceName}`.*";
					break;
				default:
					$pairs[] = "`{$sourceName}`.`{$name}` AS `{$alias}`";
					break;
			}
		}
		
		return implode(", ", $pairs);
	}
	
	/**
	 * Generate a list of fields for a collection of records
	 * 
	 * @param \DataStore\Model\Collection $records
	 * @param \DataStore\Query\Field\Collection $fields
	 * @return array
	 * @throws \Exception
	 */
	public function generateFieldList(ModelCollection $records, FieldCollection $fields = null, array $remove = [])
	{
		$record = $records->getFirstItem();
		
		if (isset($fields)) {
			return $fields->collectProperty("name");
		}
		
		$keys = array_keys($record->toArray());
		$fieldNames = [];
		$singleRecord = count($records) === 1;
		
		foreach ($keys as $fieldName) {
			$isValid = $singleRecord ? $record->get($fieldName) !== null : true;
			
			if (!in_array($fieldName, $remove) && $isValid) {
				$fieldNames[] = $fieldName;
			}
		}
		
		return $fieldNames;
	}
	
	/**
	 * Generate a list of values for all models in a collection
	 * 
	 * @param \DataStore\Model\Collection $records
	 * @param \DataStore\Query\Field\Collection $fields
	 * @return array
	 */
	public function generateValuesList(ModelCollection $records, FieldCollection $fields = null)
	{
		$fieldNames = $this->generateFieldList($records, $fields);
		$valuesList = [];
		
		foreach ($records as $record) {
			$values = [];
			foreach ($fieldNames as $fieldName) {
				$values[] = $record->get($fieldName, null);
			}
			$valuesList[] = $values;
		}
		
		return $valuesList;
	}
	
	/**
	 * Generate a list of key => value pairs
	 * 
	 * @param \DataStore\Model\Model $record
	 * @param \DataStore\Query\Field\Collection $fields
	 * @return array
	 */
	public function generateValuePairs(Model $record, FieldCollection $fields = null, array $remove = [])
	{
		$records = new ModelCollection();
		$records->add($record);
		
		$fieldNames = $this->generateFieldList($records, $fields, $remove);
		$pairs = [];
		
		foreach ($fieldNames as $fieldName) {
			$pairs[$fieldName] = $record->get($fieldName, null);
		}
		
		return $pairs;
	}
	
	/**
	 * Generate a FROM clause for a query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return string
	 */
	public function generateFromClause(Query $query)
	{
		$storage = $query->getStorage();
		$name = $storage->getName();
		$alias = $storage->getAlias();
		
		return $alias !== $name
			? "FROM `{$name}` AS `{$alias}`"
			: "FROM `{$name}`";
	}
	
	/**
	 * Generate the JOIN clauses for the query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return string
	 */
	public function generateJoinClause(Query $query)
	{
		$joins = [];
		foreach ($query->getJoins() as $join) {
			$statement = $this->generateJoinStatement($query, $join);
			$joins[] = $statement;
		}
		return implode(" ", $joins);
	}
	
	/**
	 * Generate a JOIN statement for a single join instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @param \DataStore\Query\Join\Join $join
	 * @return string
	 */
	public function generateJoinStatement(Query $query, Join $join)
	{
		$mainSource = $query->getStorage()->getAlias();
		$storage = $join->getStorage();
		$alias = $storage->getAlias();
		$name = $storage->getName();
		$source = "`{$name}`";
		$localKey = $join->getLocalKey();
		$foreignKey = $join->getForeignKey();
		$joinType = $this->generateJoinType($join);
		
		if (strlen($alias) && $alias !== $name) {
			$name = $alias;
			$source .= " AS `{$alias}`"; 
		}
		
		$joinQuery = new DbQuery($join->getStorage());
		$joinQuery->setConditions($join->getConditions());
		$joinQuery->setJoins($join->getJoins());
		
		$conditions = $this->generateWhereClause($joinQuery, "AND");
		$subJoins = $this->generateJoinClause($joinQuery);
		$statement = "{$joinType} {$source} ON `{$mainSource}`.`{$localKey}` = `{$name}`.`{$foreignKey}`";
		
		if ($conditions) {
			$statement .= " {$conditions}";
		}
		
		if (strlen($subJoins)) {
			$statement .= " {$subJoins}";
		}
		
		return $statement;
	}
	
	/**
	 * Generate the JOIN type 
	 * 
	 * @param \DataStore\Query\Join\Join $join
	 * @return string
	 */
	public function generateJoinType(Join $join)
	{
		switch ($join->getType()) {
			case DbJoin::LEFT:
				return "LEFT JOIN";
			case Join::TYPE_DEFAULT:
			default:
				return "JOIN";
		}
	}
	
	/**
	 * Generate a WHERE clause for a query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @param string $prepend
	 * @return string
	 */
	public function generateWhereClause(Query $query, $prepend = "WHERE")
	{
		$conditions = $query->getConditions();
		$pairs = [];
		
		foreach ($conditions as $condition) {
			/* @var $condition \DataStore\Query\Condition\Condition */
			
			$field = $condition->getField();
			$value = $condition->getValue();
			
			if (!$this->_isValidField($field, $query)) {
				echo "INVALID FIELD: ". $field->getAlias() ."\n";
				continue;
			}
			if (is_array($value) && empty($value)) {
				continue;
			}
			
			switch ($condition->getOperator()) {
				case DbCondition::EQUAL_TO:
					$pairs[] = $this->_isEqualTo($field, $value);
					break;
				case DbCondition::NOT_EQUAL_TO:
					$pairs[] = $this->_isEqualTo($field, $value, true);
					break;
				case DbCondition::CONTAINS:
					$pairs[] = $this->_contains($field, $value, "%%%s%%");
					break;
				case DbCondition::STARTS_WITH:
					$pairs[] = $this->_contains($field, $value, "%s%%");
					break;
				case DbCondition::ENDS_WITH:
					$pairs[] = $this->_contains($field, $value, "%%%s");
					break;
				default:
					throw new \InvalidArgumentException("Invalid condition operator: ". $condition->getOperator());
			}
		}
		
		$clean = [];
		foreach ($pairs as $pair) {
			if ($pair !== null) {
				$clean[] = $pair;
			}
		}
		
		return count($clean)
			? $prepend ." ". implode(" AND ", $clean)
			: null;
	}
	
	/**
	 * Generate a LIMIT clause for a query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 * @return string
	 */
	public function generateLimitClause(Query $query)
	{
		$max = $query->getMaxResults();
		$offset = $query->getResultOffset();
		
		if ($max > 0) {
			return "LIMIT {$max} OFFSET {$offset}";
		} else {
			return null;
		}
	}
	
	/**
	 * Generate an equal to comparison
	 * 
	 * @param \DataStore\Query\Field\Field $field
	 * @param mixed $value
	 * @param boolean $notEqual
	 * @return string
	 */
	private function _isEqualTo(Field $field, $value, $notEqual = false)
	{
		$db = $this->db;
		$fieldName = $field->getAlias();
		$sourceName = $field->getSourceName();
		
		if ($value === true) { $value = 1; }
		if ($value === false) { $value = 0; }
		
		if (is_array($value)) {
			$values = array_map([$db, "quote"], $value);
			$in = $notEqual ? "NOT IN" : "IN";
			return "`{$sourceName}`.`{$fieldName}` {$in} (". implode(",", $values) .")";
		} else if ($value === null) {
			$is = $notEqual ? "IS NOT" : "IS";
			return "`{$sourceName}`.`{$fieldName}` {$is} NULL";
		} else {
			$op = $notEqual ? "!=" : "=";
			return "`{$sourceName}`.`{$fieldName}` {$op} ". $db->quote($value);
		}
	}
	
	/**
	 * Generate a LIKE comparison
	 * 
	 * @param \DataStore\Query\Field\Field $field
	 * @param string $value
	 * @return string
	 */
	private function _contains(Field $field, $value, $format = "%%%s%%")
	{
		$fieldName = $field->getName();
		$sourceName = $field->getSourceName();
		$compare = $this->db->quote(sprintf($format, $value));
		
		if (preg_match("/^'%{1,}?'$/", $compare)) {
			return null;
		}
		
		return "`{$sourceName}`.`{$fieldName}` LIKE {$compare}";
	}
	
	/**
	 * Check if a field is valid for a query instance
	 * 
	 * @param \DataStore\Query\Field\Field $field
	 * @param \DataStore\Query\Query $query
	 * @param boolean $includeQueryFields Whether to allow the base query'sfields
	 * @return boolean
	 */
	private function _isValidField(Field $field, Query $query, $includeQueryFields = true)
	{
		$storage = $query->getStorage();
		$mainSource = $storage->getAlias();
		$sourceName = $field->getSourceName();
		$alias = $field->getAlias();
		$name = $field->getName();
		$fields = $storage->getFields();
		
		if ($includeQueryFields === false && $sourceName === $mainSource) {
			return false;
		} else if ($includeQueryFields === true && $sourceName !== $mainSource) {
			return true;
		}
		
		if (!$fields) {
			return true;
		} else if ($query->hasField($alias)) {
			return true;
		} else {
			return $fields->isField($name);
		}
	}
}