<?php
namespace DataStore\Storage;

abstract class AbstractStorage implements StorageInterface 
{
	/**
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * @var string
	 */
	protected $alias = null;
	
	/**
	 * @return string
	 */
	public function getCollectionClass() { return null; }
	
	/**
	 * Get the name of the storage
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Set the name of the storage
	 * 
	 * @param string $name
	 */
	public function setName($name) 
	{
		$this->name = $name;
	}

	/**
	 * Get the alias of the storage
	 * If no alias is set, the name will be returned
	 * 
	 * @return string
	 */
	public function getAlias() 
	{
		return isset($this->alias) ? $this->alias : $this->getName();
	}
	
	/**
	 * Set the alias of the storage
	 * 
	 * @param string $alias
	 */
	public function setAlias($alias) 
	{
		$this->alias = $alias;
	}
}