<?php
namespace Bliss\Module;

use Bliss\Config,
	Bliss\Console,
	Bliss\Request\RequestInterface;

abstract class AbstractModule extends \Bliss\Application\Container
{
	const EVENT_PRE_EXECUTE = "module.preExecute";
	const EVENT_POST_EXECUTE = "module.postExecute";
	
	const CONFIG_DEFAULT_CONTROLLER = "defaultController";
	
	/**
	 * @var \Bliss\Application\Container
	 */
	private $app;

	/**
	 * The root path to the module
	 * @var string
	 */
	private $path;

	/**
	 * The namespace used to load classes within the module
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * @var boolean
	 */
	protected $isEnabled = true;
	
	/**
	 * @var array
	 */
	protected $params = [];
	
	/**
	 * Get the module's name
	 * 
	 * @return string
	 */
	abstract public function getName();

	/**
	 * Initialize the module
	 */
	abstract public function init();
	
	public function preInit() {}
	public function postInit() {}

	/**
	 * Constructor
	 * 
	 * @param string $path The root path of the module
	 * @param \Bliss\Application\Container $app
	 */
	public function __construct($path, \Bliss\Application\Container $app)
	{
		parent::__construct($app->autoloader());
		
		$this->path = $path;
		$this->app = $app;
		$this->namespace = $this->_findNamespace();
		
		$this->initSubModules();
		$this->initConfig();
	}
	
	/**
	 * Load and initialize all sub-modules
	 */
	private function initSubModules()
	{
		$modules = new Collection($this);
		$path = $this->resolvePath("modules");
		
		if (is_dir($path)) {
			foreach (new \DirectoryIterator($path) as $dir) {
				if ($dir->isDir() && !$dir->isDot()) {
					$module = $modules->add(
						$dir->getBasename(),
						$dir->getRealPath()
					);
					$module->init();
				}
			}
		}
		
		$this->modules = $modules;
	}
	
	/**
	 * Initialize the module's configuration
	 */
	private function initConfig()
	{
		$config = $this->config();
		
		$this->params = $config->get("params", []);
	}
	
	/**
	 * Find the namespace of the module
	 * 
	 * @return string
	 */
	private function _findNamespace()
	{
		$parts = explode("\\", get_class($this));
		array_pop($parts);
		
		return implode("\\", $parts);
	}
	
	/**
	 * Get a parameter's value from the module
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
	
	/**
	 * Get the application instance the module belongs to
	 * 
	 * @return \Bliss\Application\Container
	 */
	public function app()
	{
		return $this->app;
	}
	
	/**
	 * Get the namespace of the module
	 * 
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * Get a controller instance by its name
	 *
	 * @param string $controllerName
	 * @return \Bliss\Controller\AbstractController
	 * @throws \InvalidArgumentException
	 */
	public function getController($controllerName)
	{
		return \Bliss\Controller\AbstractController::factory($this, $controllerName);
	}
	
	/**
	 * Get the root path of the module
	 * 
	 * @return string
	 */
	public function path()
	{
		return $this->path;
	}

	/**
	 * Get the full path for a file within the module directory
	 *
	 * @param string $filename
	 * @return string
	 */
	public function resolvePath($filename = null)
	{
		return strlen($filename) ? $this->path ."/". $filename : $this->path;
	}
	
	/**
	 * Generate a view path based on a request instance
	 * 
	 * @param \Bliss\Request\RequestInterface $request
	 * @return string
	 */
	public function generateViewPath(RequestInterface $request)
	{
		$controllerName = $request->getParam("controller", "index");
		$actionName = $request->getParam("action", "index");
		$parts = [$controllerName];
		
		if ($actionName) {
			$parts[] = $actionName;
		}
		
		return implode("/", $parts);
	}

	/**
	 * Attempt to get the module's configuration
	 *
	 * @return \Bliss\Config
	 */
	public function config()
	{
		$path = $this->resolvePath("config/module.php");
		if (file_exists($path)) {
			return Config::loadFile($path);
		} else {
			return new Config();
		}
	}
	
	/**
	 * Attempt to get the default controller name for the module
	 * 
	 * @return string|null
	 */
	public function getDefaultController()
	{
		$config = $this->config();
		
		return $config->get(self::CONFIG_DEFAULT_CONTROLLER, "index");
	}
	
	/**
	 * Disable the module
	 */
	public function disable()
	{
		$this->isEnabled = false;
	}
	
	/**
	 * Enable the module
	 */
	public function enable()
	{
		$this->isEnabled = true;
	}
	
	/**
	 * Check if the module is enabled
	 * 
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->isEnabled;
	}
	
	/**
	 * Execute a request against the module
	 * 
	 * @param \Bliss\Request\RequestInterface $request
	 * @return array
	 */
	public function exec(RequestInterface $request) 
	{
		$parameters = $request->getParams();
		
		if (isset($parameters["module"]) && $parameters["module"] !== $this->getName()) {
			return parent::exec($request);
		}
		
		$controllerName = isset($parameters["controller"]) ? $parameters["controller"] : $this->getDefaultController();
		$controller = $this->getController($controllerName);
		$controller->init();
		
		$request->setParam("controller", $controllerName);
		
		Console::log("Executing controller: ". $controllerName);
		
		return $controller->exec($parameters);
	}
	
	/**
	 * If a submodule of $this module cannot be found, check the module's parent
	 * application
	 * 
	 * @param string $moduleName
	 * @return \Bliss\Module\AbstractModule
	 * @throws \Bliss\Module\InvalidModuleException
	 */
	public function modules($moduleName = null) {
		try {
			return parent::modules($moduleName);
		} catch (InvalidModuleException $e) {
			if ($this->app) {
				return $this->app->modules($moduleName);
			}
			throw $e;
		}
	}
	
	/**
	 * Override the default toArray method to add additional properties
	 * 
	 * @return array
	 */
	public function toArray() {
		$data = array_merge(parent::toArray(), [
			"name" => $this->getName()
		]);
		
		return $data;
	}
}