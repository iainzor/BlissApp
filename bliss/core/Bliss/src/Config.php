<?php
namespace Bliss;

class Config implements \Iterator, \ArrayAccess
{
	/**
	 * @var array
	 */
	private $data = array();
	
	/**
	 * Attempt to create a new config instance from a value
	 * If it cannot be created, the original value will be returned
	 * 
	 * @param string $value
	 * @return \Bliss\Config|mixed
	 */
	public static function factory($value)
	{
		if (is_array($value))
		{
			if (ArrayUtilities::isAssociative($value)) {
				return new self($value);
			} else {
				return $value;
			}
		}
		
		return $value;
	}
	
	/**
	 * Constructor
	 * 
	 * @param array $data
	 */
	public function __construct(array $data = null)
	{
		if (isset($data)) {
			$this->setData($data);
		}
	}
	
	/**
	 * Set the data for the config instance
	 * 
	 * @param array $data
	 */
	public function setData(array $data)
	{
		$this->data = $this->_merge($this->data, $data);
	}
	
	/**
	 * Get the config's data array
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Get the config's data as an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		foreach ($this->data as $name => $value) {
			if (is_object($value) && !method_exists($value, "toArray")) {
				continue;
			} else if (is_object($value) && method_exists($value, "toArray")) {
				$data[$name] = $value->toArray();
			} else {
				$data[$name] = $value;
			}
		}
		
		return $data;
	}
	
	/**
	 * Merge this config instance with another
	 * 
	 * @param \Bliss\Config $config
	 */
	public function merge(Config $config)
	{
		$this->data = $this->_merge($this->data, $config->getData());
	}
	
	/**
	 * Recursively merge an array
	 * 
	 * @param array $base
	 * @param array $data
	 */
	private function _merge(array $base, array $data)
	{
		foreach ($data as $key => $value)
		{
			if (!is_array($value)) {
				$base[$key] = $value;
			} else {
				$baseValue = isset($base[$key]) ? $base[$key] : array();
				$base[$key] = $this->_merge($baseValue, $value);
			}
		}
		
		return $base;
	}
	
	/**
	 * Get a value from the data array.  If the value is an associative
	 * array, it will be wrapped in an instance of Config
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * Get a value from the config with an optional default value if the
	 * value cannot be found
	 *
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($name, $defaultValue = null)
	{
		if (isset($this->data[$name])) {
			return self::factory($this->data[$name]);
		} else {
			return $defaultValue;
		}
	}
	
	/**
	 * Magic method used to easily set config values
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$this->setData(array($name => $value));
	}
	
	/**
	 * Magic method for isset()
	 * Check if a value exists for a name
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}
	
	/**
	 * Attempt to load a file as a config instance
	 * 
	 * @param string $file
	 * @throws \Exception
	 * @return \Bliss\Config
	 */
	public static function loadFile($file)
	{
		if (!is_file($file)) {
			throw new \Exception("Could not load config file: {$file}");
		}
		
		$contents = include $file;
		if (!is_array($contents)) {
			throw new \UnexpectedValueException("Expecting an array, got ". gettype ($contents));
		}
		
		return new self($contents);
	}
	
	/**
	 * Implementation of \Iterator
	 */
		public function current() 
		{
			$value = current($this->data);
			if (is_array($value) && ArrayUtilities::isAssociative($value)) {
				$value = new self($value);
			}
			
			return $value;
		}
		public function key() { return key($this->data); }
		public function next() { return next($this->data); }
		public function rewind() { return reset($this->data); }
		public function valid() { $key = key($this->data); return $key !== false && $key !== null; }
		
	/**
	 * Implementation of \ArrayAccess
	 */
		public function offsetExists($offset) { return isset($this->data[$offset]); }

		public function offsetGet($offset) { return $this->data[$offset]; }

		public function offsetSet($offset, $value) { $this->data[$offset] = $value; }

		public function offsetUnset($offset) { unset($this->data[$offset]); }


}