<?php
namespace DataStore\Model;

use Bliss\String,
	DataStore\Query\Query;

class Model extends \Bliss\Component implements ModelInterface
{
	/**
	 * @var int
	 */
	protected $id;
	
	/**
	 * @var int
	 */
	protected $created;
	
	/**
	 * @var int
	 */
	protected $updated;
	
	/**
	 * @var array
	 */
	private $properties = [];
	
	/**
	 * @var \DataStore\Query\Query
	 */
	private $query;
	
	/**
	 * Get the ID of the model
	 * 
	 * @return int
	 */
	public function getId() 
	{
		return $this->id;
	}

	/**
	 * Set the ID of the model
	 * 
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = (int) $id;
	}
	
	/**
	 * Get the UNIX timestamp of when the model was created
	 * 
	 * @return int
	 */
	public function getCreated() 
	{
		return $this->created;
	}

	/**
	 * Set the UNIX timestamp of when the model was created
	 * 
	 * @param int $date
	 */
	public function setCreated($date) 
	{
		$this->created = $this->parseDate($date);
	}
	
	/**
	 * Get the UNIX timestamp of when the model was last updated
	 * 
	 * @return int
	 */
	public function getUpdated() 
	{
		return $this->updated;
	}

	/**
	 * Set the UNIX timestamp of when the model was last updated
	 * 
	 * @param int $date
	 */
	public function setUpdated($date) 
	{
		$this->updated = $this->parseDate($date);
	}
	
	/**
	 * Parse a date and convert it to an integer
	 * 
	 * @param mixed $date
	 * @return int
	 */
	protected function parseDate($date)
	{
		if (!is_numeric($date)) {
			$date = strtotime($date);
		}
		
		return (int) $date;
	}
		
	/**
	 * Set the model's properties
	 * 
	 * @param array $properties
	 */
	public function setProperties(array $properties) 
	{
		foreach ($properties as $name => $value) {
			$this->set($name, $value);
		}
	}
	
	/**
	 * Set the model's query instance
	 * 
	 * @param \DataStore\Query\Query $query
	 */
	public function setQuery(Query $query) 
	{
		$this->query = $query;
	}
	
	/**
	 * Delete the model from where it's stored
	 * 
	 * @return boolean
	 */
	public function delete() 
	{
		if ($this->id) {
			$this->query->fields("id")->compareTo($this->id);
			return $this->query->delete();
		}
		
		return false;
	}

	/**
	 * Save any changes made to the model
	 * 
	 * @param boolean $recursive
	 * @return int The model's unique ID
	 */
	public function save($recursive = false) 
	{
		$this->query->save($this, $recursive);
		
		return $this->id;
	}
	
	/**
	 * Replace the model 
	 */
	public function replace()
	{
		$this->query->replace($this);
	}
	
	/**
	 * Attempt to get a property's value
	 * 
	 * @param string $propertyName
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($propertyName, $defaultValue = null)
	{
		$propertyName = String::toCamelCase($propertyName, false);
		$method = "get{$propertyName}";
		
		if (method_exists($this, $method)) {
			return call_user_func([$this, $method]);
		} else {
			return isset($this->properties[$propertyName])
				? $this->properties[$propertyName]
				: $defaultValue;
		}
	}
	
	/**
	 * Set a property's value
	 * 
	 * @param string $propertyName
	 * @param mixed $value
	 */
	public function set($propertyName, $value)
	{
		$propertyName = String::toCamelCase($propertyName, false);
		$method = "set{$propertyName}";
		
		if (method_exists($this, $method)) {
			call_user_func([$this, $method], $value);
		} else {
			$this->properties[$propertyName] = $this->_parse($value);
		}
	}

	/**
	 * Direct property assignment
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) 
	{
		return $this->get($name);
	}
	
	/**
	 * Set a property's value
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$this->set($name, $value);
	}
	
	/**
	 * Catch calls for get and set methods
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @throws \InvalidArgumentException
	 */
	public function __call($method, array $arguments) 
	{
		$value = isset($arguments[0]) ? $arguments[0] : null;
		
		if (preg_match("/^set([a-z0-9]+)$/i", $method, $matches)) {
			$name = $matches[1];
			return $this->set($name, $value);
		}
		
		if (preg_match("/^get([a-z0-9]+)$/i", $method, $matches)) {
			$name = $matches[1];
			return $this->get($name);
		}
		
		throw new \InvalidArgumentException("Invalid method: {$method}");
	}
	
	/**
	 * Cast a value to its proper type
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	private function _parse($value)
	{
		if (is_object($value)) {
			return $value;
		}
		
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->_parse($v);
			}
			return $value;
		}
		
		if (preg_match("/^[0-9]+$/", $value)) {
			return (int) $value;
		}
		
		if (preg_match("/^[0-9]+\.[0-9]+$/", $value)) {
			return (float) $value;
		}
		
		return $value;
	}
	
	/**
	 * Override the default toArray to return the model's properties
	 * 
	 * @return array
	 */
	public function toArray() 
	{
		$properties = array_merge(parent::toArray(), $this->properties);
		foreach ($properties as $name => $rawValue) {
			$value = $this->get($name, $rawValue);
			
			if ($value instanceof \Bliss\Component || $value instanceof \Bliss\Collection) {
				$properties[$name] = $value->toArray();
			} else {
				$properties[$name] = $value;
			}
		}
		return $properties;
	}
	
	/**
	 * Generate a new model from an array of properties
	 * 
	 * @param array $properties
	 * @return \DataStore\Model\Model
	 */
	public static function generate(array $properties)
	{
		$result = new self();
		$result->setProperties($properties);
		
		return $result;
	}
}