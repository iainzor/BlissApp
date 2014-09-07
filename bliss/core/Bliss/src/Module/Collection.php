<?php
namespace Bliss\Module;

class Collection extends \Bliss\Collection
{
	/**
	 * @var \Bliss\Application\Container
	 */
	private $app;
	
	/**
	 * @var array
	 */
	private $directories = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\Application $app
	 */
	public function __construct(\Bliss\Application\Container $app) 
	{
		$this->app = $app;
	}
	
	/**
	 * Add all modules found in a directory
	 * 
	 * @param string $directory
	 * @throws \Exception
	 */
	public function addFromDirectory($directory)
	{
		if (!is_dir($directory)) {
			throw new \Exception("Invalid module directory provided: {$directory}");
		}
		
		$di = new \DirectoryIterator($directory);
		foreach ($di as $item) {
			if (!$item->isDot() && $item->isDir()) {
				$ns = $item->getBasename();
				$dir = $item->getRealPath();
				
				$this->add($ns, $dir);
			}
		}
		
		$this->createAll();
	}
	
	/**
	 * Add a module to the collection
	 * 
	 * @param string $namespace
	 * @param string $directory
	 * @return \Bliss\Module\AbstractModule
	 */
	public function add($namespace, $directory)
	{
		$this->app->autoloader()->registerNamespace($namespace, $directory ."/src");
		
		$this->directories[$namespace] = $directory;
	}
	
	/**
	 * Attempt to create and add a module from a namespace
	 * 
	 * @param string $namespace
	 * @param string $directory
	 * @param boolean $recursive
	 * @return \Bliss\Module\AbstractModule
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function create($namespace, $directory, $recursive = false)
	{
		unset($this->directories[$namespace]);
		
		$className = "{$namespace}\\Module";
		if (!class_exists($className)) {
			if (!empty($this->directories) && $recursive) {
				$this->createAll($this->directories);
			}
		}
		
		if (!class_exists($className)) {
			throw new \Exception("Module class '{$className}' does not exist");
		}
		
		$module = new $className($directory, $this->app);
		$this->addModule($module);
		
		return $module;
	}
	
	/**
	 * Create an add all modules
	 * 
	 * @param array $directories
	 */
	public function createAll(array $directories = null)
	{
		if ($directories === null) {
			$directories = $this->directories;
		}
		
		foreach ($directories as $namespace => $directory) {
			$this->create($namespace, $directory, true);
		}
	}
	
	/**
	 * Add a module instance to the collection
	 * 
	 * @param \Bliss\Module\AbstractModule $module
	 */
	public function addModule(AbstractModule $module)
	{
		$this->app->autoloader()->registerNamespace(
			$module->getNamespace(), 
			$module->resolvePath("src")
		);
		
		$this->addItem($module);
	}
	
	/**
	 * Get a module's instance by its name
	 * If the module does not exist, return FALSE
	 * 
	 * @param string $moduleName
	 * @return \Bliss\Module\AbstractModule
	 * @throws \Bliss\Module\InvalidModuleException
	 */
	public function get($moduleName)
	{
		foreach ($this->getAll() as $module) {
			if ($module->getName() === $moduleName) {
				return $module;
			}
		}
		
		throw new InvalidModuleException("Could not find '{$moduleName}' module");
	}
	
	/**
	 * Initialize all modules in the collection
	 */
	public function init()
	{
		foreach ($this->getAll() as $module) {
			$module->init();
		}
	}
	
	public function preInit()
	{
		$this->each(function($module) {
			$module->preInit();
		});
	}
	
	public function postInit()
	{
		$this->each(function($module) {
			$module->postInit();
		});
	}
}