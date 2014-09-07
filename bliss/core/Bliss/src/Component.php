<?php
namespace Bliss;

class Component
{
	/**
	 * @var boolean
	 */
	private $_isBasic = false;
	
	/**
	 * @var \Bliss\Collection
	 */
	private $parentCollection = null;
	
	/**
	 * Set the component's parent collection
	 * 
	 * @param \Bliss\Collection $collection
	 */
	public function setParentCollection(Collection $collection)
	{
		$this->parentCollection = $collection;
	}
	
	/**
	 * Get the parent collection the component belongs to
	 * 
	 * @return \Bliss\Collection|null
	 */
	public function getParentCollection()
	{
		return $this->parentCollection;
	}
	
	/**
	 * Set if the component should be converted a basic model
	 * 
	 * When the toArray method is called all values that are instances of 
	 * \Bliss\Component or \Bliss\Collection will be ignored
	 * 
	 * @param boolean $flag
	 */
	public function setIsBasic($flag = true)
	{
		$this->_isBasic = (boolean) $flag;
	}


	/**
	 * Attempt to set the properties of the object using a key => value pair array
	 *
	 * @param array $properties
	 */
	public function setProperties(array $properties)
	{
		foreach ($properties as $key => $value) {
			if (method_exists($this, "set{$key}")) {
				call_user_func(array($this, "set{$key}"), $value);
			}
		}
	}

	/**
	 * Convert the component's properties to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		$refClass = new \ReflectionClass($this);
		$props = $refClass->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);

		foreach ($props as $refProp) {
			$name = $refProp->getName();
			$property = $this->{$name};
			
			if ($this->_isBasic && ($property instanceof Component || $property instanceof Collection)) {
				continue;
			}

			if (is_object($property) && method_exists($property, "toArray") ) {
				$data[$name] = $property->toArray();
			} else if ($property instanceOf \DateTime) {
				$data[$name] = $property->getTimestamp();
			} else if (method_exists($this, "get{$name}")) {
				$data[$name] = call_user_func(array($this, "get{$name}"));
			} else if (!is_object($property)) {
				$data[$name] = $property;
			}
		}
		
		if ($this instanceOf Resource\ResourceInterface) {
			$data["resourceId"] = $this->getResourceId();
		}
		
		# Add constants to the array
		$data = array_merge($data, $refClass->getConstants());

		return $data;
	}
	
	/**
	 * Populate a component using an array of data
	 * 
	 * @param \Bliss\Component $component
	 * @param array $data The values to populate
	 * @param array $defaults Collection of default values, also used to restrict only certain values from being set
	 * @return \Bliss\Component
	 */
	public static function populateComponent(Component $component, array $data, array $defaults)
	{
		foreach ($defaults as $name => $defaultValue) {
			$value = isset($data[$name]) ? $data[$name] : $defaultValue;
			$method = "set{$name}";
			
			if ($value !== null) {
				call_user_func(array($component, $method), $value);
			}
		}
		
		return $component;
	}
}