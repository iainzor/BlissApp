<?php
namespace Bliss\Response;

abstract class AbstractResponse extends \Bliss\Component implements ResponseInterface
{
	/**
	 * @var array
	 */
	protected $parameters = [];
	
	/**
	 * @var string
	 */
	protected $content = null;
	
	/**
	 * Get a single parameter
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @param int $filter The method of filtering the parameter
	 * @see filter_var()
	 * @return mixed
	 */
	public function getParam($name, $defaultValue = null, $filter = FILTER_DEFAULT) 
	{
		$value = isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
		
		if (!is_object($value)) {
			return filter_var($value, $filter);
		} else {
			return $value;
		}
	}

	/**
	 * Get all response parameters
	 * 
	 * @param int|array $filterDefinition The method of filtering the parameters
	 * @see filter_var_array
	 * @return array
	 */
	public function getParams($filterDefinition = FILTER_DEFAULT) 
	{
		return filter_var_array($this->parameters, $filterDefinition);
	}

	/**
	 * Set a single response parameter
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value) 
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * Set multiple response parameters
	 * 
	 * @param array $parameters
	 */
	public function setParams(array $parameters) 
	{
		$this->parameters = array_merge($this->parameters, $parameters);
	}
	
	/**
	 * Clear all response parameters
	 */
	public function clearParams()
	{
		$this->parameters = [];
	}
	
	/**
	 * Set the response's content
	 * 
	 * @param string $content
	 */
	public function setContent($content) 
	{
		$this->content = $content;
	}
	
	/**
	 * Get the response's content
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
	
	/**
	 * Convert the response to a string
	 * 
	 * @return string
	 */
	public function toString()
	{
		return $this->getContent();
	}
}