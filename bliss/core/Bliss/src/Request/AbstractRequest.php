<?php
namespace Bliss\Request;

abstract class AbstractRequest implements RequestInterface
{
	/**
	 * @var array
	 */
	protected $params = [];

	/**
	 * Set the request parameters
	 * 
	 * @param array $params
	 */
	public function setParams(array $params) 
	{
		$this->params = $params;
	}
	
	/**
	 * Set a single parameter's value
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value) 
	{
		$this->params[$name] = $value;
	}
	
	/**
	 * Get all requested parameters
	 * 
	 * @return array
	 */
	public function getParams() 
	{
		return $this->params;
	}
	
	/**
	 * Get a single parameter's value
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getParam($name, $defaultValue = null) 
	{
		return isset($this->params[$name])
			? $this->params[$name]
			: $defaultValue;
	}
}