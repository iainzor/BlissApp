<?php
namespace DataStore\Query\Join;

use DataStore\Query\Query;

class Join extends Query
{
	const TYPE_DEFAULT = "default";
	
	/**
	 * @var string
	 */
	protected $type;
	
	/**
	 * @var string
	 */
	protected $foreignKey;
	
	/**
	 * @var string
	 */
	protected $localKey;
	
	/**
	 * Set the type of join to use
	 * 
	 * @param string $type
	 */
	public function setType($type = null)
	{
		$this->type = $type;
	}
	
	/**
	 * Get the type of join to use
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Get the join's foreign key name
	 * 
	 * @return string
	 */
	public function getForeignKey() 
	{
		return $this->foreignKey;
	}
	
	/**
	 * Set the join's foreign key name
	 * 
	 * @param string $foreignKey
	 */
	public function setForeignKey($foreignKey) 
	{
		$this->foreignKey = $foreignKey;
	}

	/**
	 * Get the join's local key name
	 * 
	 * @return string
	 */
	public function getLocalKey() 
	{
		return $this->localKey;
	}
	
	/**
	 * Set the join's local key name
	 * 
	 * @param string $localKey
	 */
	public function setLocalKey($localKey) 
	{
		$this->localKey = $localKey;
	}
		
	/**
	 * Create a new Join instance from an array of properties
	 * 
	 * @param array $properties
	 * @param \DataQuery\Query\Join\Join $instance
	 * @return \DataQuery\Query\Join\Join
	 */
	public static function factory(array $properties, Join $instance = null)
	{
		$join = isset($instance) ? $instance : new self();
		$join->setProperties($properties);
		
		return $join;
	}
}