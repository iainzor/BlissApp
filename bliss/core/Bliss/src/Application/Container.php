<?php
namespace Bliss\Application;

use Bliss\Request\RequestInterface,
	Bliss\Response\ResponseInterface,
	Bliss\ErrorHandler\ErrorHandlerInterface,
	Bliss\ErrorHandler\DefaultErrorHandler,
	Bliss\Module\InvalidModuleException,
	Bliss\Module\Collection as ModuleCollection,
	Bliss\Console;

class Container extends \Bliss\Component
{
	/**
	 * @var \Bliss\Autoloader
	 */
	private $autoloader;
	
	/**
	 * @var \Bliss\ErrorHandler\ErrorHandlerInterface
	 */
	private $errorHandler;
	
	/**
	 * @var \Bliss\Module\Collection
	 */
	protected $modules;
	
	/**
	 * @var \Bliss\Request\RequestInterface
	 */
	protected $request;
	
	/**
	 * @var \Bliss\Response\ResponseInterface
	 */
	protected $response;
	
	/**
	 * Constructor
	 */
	public function __construct(\Bliss\Autoloader $autoloader = null)
	{
		$this->autoloader = $autoloader;
		$this->modules = new ModuleCollection($this);
	}
	
	/**
	 * Get the application's autoloader instance
	 * 
	 * @return \Bliss\Autoloader
	 */
	public function autoloader()
	{
		if (!isset($this->autoloader)) {
			throw new \UnexpectedValueException("No auto loader instance has been set");
		}
		
		return $this->autoloader;
	}
	
	/**
	 * @return \DataStore\Module
	 */
	public function datastore()
	{
		return $this->modules(\DataStore\Module::NAME);
	}
	
	/**
	 * If no module name is provided, returns all active modules registered in 
	 * the application, otherwise it attempts to return the specified module
	 * 
	 * @return \Bliss\Module\Collection|\Bliss\Module\AbstractModule
	 * @throws \Bliss\Module\InvalidModuleException
	 */
	public function modules($moduleName = null)
	{
		if (isset($moduleName)) {
			foreach ($this->modules as $module) {
				if ($module->getName() === $moduleName) {
					return $module;
				}
			}
			throw new InvalidModuleException("Could not find module '{$moduleName}'");
		}
		
		return $this->modules->filter(function($module) {
			return $module->isEnabled();
		});
	}
	
	/**
	 * Register a directory containing modules that will be used by the application
	 * 
	 * @param string $directory
	 */
	public function registerModulesDirectory($directory)
	{
		$this->modules->addFromDirectory($directory);
	}
	
	/**
	 * Get the application's view container
	 * 
	 * @return \Bliss\View\Container
	 */
	public function view()
	{
		return $this->modules(\UI\Module::NAME)->view();
	}
	
	/**
	 * Set the request instance used to determine what to execute 
	 * 
	 * @param \Bliss\Request\RequestInterface $request
	 */
	public function setRequest(RequestInterface $request)
	{
		$this->request = $request;
	}
	
	/**
	 * Get the application's request instance
	 * 
	 * @return \Bliss\Request\RequestInterface
	 * @throws \Exception
	 */
	public function request()
	{
		if (!isset($this->request)) {
			throw new \Exception("No request has been set for the application");
		}
		
		return $this->request;
	}
	
	/**
	 * Set the response instance used by the application
	 * 
	 * @param \Bliss\Response\ResponseInterface $response
	 */
	public function setResponse(ResponseInterface $response)
	{
		$this->response = $response;
	}
	
	/**
	 * Get the application's response instance
	 * 
	 * @return \Bliss\Response\ResponseInterface
	 * @throws \Exception
	 */
	public function response()
	{
		if (!isset($this->response)) {
			throw new \Exception("No response has been set for the application");
		}
		
		return $this->response;
	}
	
	/**
	 * Set the application's error handler
	 * 
	 * @param \Bliss\ErrorHandler\ErrorHandlerInterface $errorHandler
	 */
	public function setErrorHandler(ErrorHandlerInterface $errorHandler)
	{
		set_error_handler([$errorHandler, "handleError"]);
		set_exception_handler([$errorHandler, "handleException"]);
		
		$this->errorHandler = $errorHandler;
	}
	
	/**
	 * Get the application's error handler
	 * 
	 * @return \Bliss\ErrorHandler\ErrorHandlerInterface
	 */
	public function errorHandler()
	{
		if (!isset($this->errorHandler)) {
			$this->errorHandler = new DefaultErrorHandler();
		}
		
		return $this->errorHandler;
	}
	
	/**
	 * Run the application using parameters found from the request instance
	 */
	public function run()
	{
		Console::log("Running application");
		
		try {
			$request = $this->request();
			$request->init();
			
			$this->modules()->preInit();
			$this->modules()->init();
			$this->modules()->postInit();
			
			$this->exec($request);
		} catch (\Exception $e) {
			echo "<pre>";
			echo "<h1>Critical Error</h1>";
			echo "<h3>". $e->getMessage() ."</h3>";
			echo $e->getTraceAsString();
			echo "</pre>";
		}
		
		$this->close();
	}
	
	/**
	 * Execute a set of parameters
	 * The only required parameter is 'module'
	 * 
	 * @param \Bliss\Request\RequestInterface $request
	 * @return \Bliss\Response\ResponseInterface
	 * @throws \InvalidArgumentException
	 */
	public function exec(RequestInterface $request)
	{
		Console::log("Executing request: ". json_encode($request->getParams()));
		
		$this->request = $request;
		
		$moduleName = $request->getParam("module");
		
		if (!$moduleName) {
			throw new \InvalidArgumentException("A module name must be provided in order to execute the request");
		}
		
		$module = $this->modules->get($moduleName);
		$response = $this->response();
		$response->clearParams();
		
		try {
			Console::log("Executing module: ". $moduleName);
			
			$responseParams = $module->exec($request);
		} catch (\Exception $e) {
			$this->errorHandler->handleException($e);
			$this->close();
		}
		
		if (!empty($responseParams)) {
			$response->setParams($responseParams);
		}
		
		Console::log("Rendering view");
		
		$renderer = $this->view()->getRenderer($module, $request, $response);
		$ui = $this->modules(\UI\Module::NAME)->getUI();
		$response->setContent(
			$ui->wrap($renderer)
		);
		
		return $response;
	}
	
	/**
	 * Output the response and close the application
	 */
	public function close()
	{
		$errorModule = $this->modules(\Error\Module::NAME);
		
		echo $this->response->toString();
		
		if ($errorModule->getParam(\Error\Module::PARAM_SHOW_CONSOLE) === true) {
			echo "<!--\n\n";
			echo Console::toString();
			echo "\n\n-->";
		}
		
		exit;
	}
}