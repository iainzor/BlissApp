<?php
namespace Bliss\Request;

interface RequestInterface
{
	public function init();
	
	/**
	 * @param array $params
	 */
	public function setParams(array $params);
	
	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value);
	
	/**
	 * @return array
	 */
	public function getParams();
	
	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return string
	 */
	public function getParam($name, $defaultValue = null);
	
	/**
	 * @return \Bliss\Router\RouterInterface
	 */
	public function router();
}