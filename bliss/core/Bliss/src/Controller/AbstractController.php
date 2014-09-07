<?php
namespace Bliss\Controller;

use Bliss\Component,
	Bliss\String,
	Bliss\Module\AbstractModule;

abstract class AbstractController extends Component
{
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var \Bliss\Module\AbstractModule
	 */
	protected $module;

	/**
	 * Additional parameters for the controller
	 * @var array
	 */
	protected $parameters = [];

	/**
	 * Initialize the controller
	 */
	abstract public function init();

	/**
	 * Execute the controller
	 * 
	 * @param array $parameters
	 */
	abstract public function execute();

	/**
	 * Constructor
	 *
	 * @param string $name
	 * @param \Bliss\Module\AbstractModule $module
	 */
	public function __construct($name, AbstractModule $module)
	{
		$this->name = $name;
		$this->module = $module;
	}
	
	/**
	 * Get the application's instance of the controller
	 * 
	 * @return \Bliss\Application\Container
	 */
	public function app()
	{
		return $this->module->app();
	}
	
	/**
	 * Execute a set of parameters
	 * 
	 * @param array $parameters
	 * @return string
	 */
	public function exec(array $parameters)
	{
		$this->setParameters($parameters);
		
		return $this->execute();
	}

	/**
	 * Set multiple controller parameters
	 *
	 * @param array $params
	 */
	public function setParameters(array $params)
	{
		$this->parameters = array_merge($this->parameters, $params);
	}

	/**
	 * Set a single parameter value
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * Get a parameter from the controller
	 *
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getParam($name, $defaultValue = null)
	{
		return isset($this->parameters[$name])
			? $this->parameters[$name]
			: $defaultValue;
	}
	
	/**
	 * @return array
	 */
	public function parameters()
	{
		return $this->parameters;
	}
	
	/**
	 * Get and decode a JSON parameter
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getJsonParam($name, $defaultValue = null)
	{
		$value = $this->getParam($name);
		if ($value) {
			return json_decode($value, true);
		} else {
			return $defaultValue;
		}
	}

	/**
	 * Redirect to a uri with the application's base URL prepended
	 *
	 * @param string $uri
	 */
	protected function redirect($uri)
	{
		$uri = ltrim($uri, "/");
		$url = $this->request->getBaseUrl() ."{$uri}";
		$this->response->redirect($url);
	}
	
	/**
	 * Generate a new controller instances
	 * 
	 * @param \Bliss\Module\AbstractModule $module
	 * @param string $name
	 * @return \Bliss\Controller\AbstractController
	 * @throws InvalidControllerException
	 */
	public static function factory(AbstractModule $module, $name)
	{
		$className = $module->getNamespace() ."\\Controller\\". String::toCamelCase($name) ."Controller";
		
		if (!class_exists($className)) {
			throw new InvalidControllerException("Could not find controller class: \\{$className}");
		} else {
			return new $className($name, $module);
		}
	}
}