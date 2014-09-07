<?php
namespace DataStore\Query\Relation;

use DataStore\Query\Query;

abstract class AbstractRelation implements RelationInterface
{
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var \DataStore\Query\Query
	 */
	protected $query;
	
	/**
	 * @var mixed
	 */
	protected $foreignKey;
	
	/**
	 * @var mixed
	 */
	protected $localKey;
	
	/**
	 * @var boolean
	 */
	protected $isEnabled = true;
	
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
	 * Get the name of the relation
	 * 
	 * @return string
	 */
	public function getName() 
	{
		return $this->name;
	}
	
	/**
	 * Set the name of the relation
	 * 
	 * @param string $propertyName
	 */
	public function setName($name) 
	{
		$this->name = $name;
	}

	/**
	 * Get the relation's query
	 * 
	 * @return \DataStore\Query\Query
	 */
	public function getQuery()
	{
		return $this->query;
	}
	
	/**
	 * Get the foreign key used to link the relation
	 * 
	 * @return array
	 */
	public function getForeignKey() 
	{
		return $this->foreignKey;
	}
	
	/**
	 * Set the foreign key used to link the relation
	 * 
	 * @param string|array $foreignKey
	 */
	public function setForeignKey($foreignKey) 
	{
		if (!is_array($foreignKey)) {
			$foreignKey = [$foreignKey];
		}
		
		$this->foreignKey = $foreignKey;
	}

	/**
	 * Get the local key used to link the relation
	 * 
	 * @return array
	 */
	public function getLocalKey() 
	{
		return $this->localKey;
	}

	/**
	 * Set the local key used to link the relation
	 * 
	 * @param string|array $localKey
	 */
	public function setLocalKey($localKey) 
	{
		if (!is_array($localKey)) {
			$localKey = [$localKey];
		}
		
		$this->localKey = $localKey;
	}
	
	public function disable() { $this->isEnabled = false; }

	public function enable() { $this->isEnabled = true; }

	public function isEnabled() { return $this->isEnabled; }

}