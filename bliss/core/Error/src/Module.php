<?php
namespace Error;

use Bliss\Module\AbstractModule,
	Bliss\ErrorHandler\ErrorHandlerInterface,
	Acl\Builder\Source\FileArraySource,
	Bliss\Request\HttpRequest;

class Module extends AbstractModule implements ErrorHandlerInterface
{
	const NAME = "error";
	
	const PARAM_SHOW_CONSOLE = "error.showConsole";
	const PARAM_SHOW_TRACE = "error.showTrace";
	
	public function getName() { return self::NAME; }
	
	public function init()
	{
#		$this->application->addEvent(\Bliss\Application::EVENT_DISPATCH, array($this, "setup"));
	}
	
	public function setup()
	{
		$this->_initUserAcl();
	}
	
	private function _initUserAcl()
	{
		$builder = \Users\Module::acl();
		$builder->addSource(
			new FileArraySource($this->resolvePath("config/acl.php"))
		);
	}
	
	# 
	# Implementation of \Bliss\ErrorHandler\ErrorHandlerInterface
	#
	
	/**
	 * @param int $num
	 * @param string $string
	 * @param string $file
	 * @param int $line
	 * @param array $context
	 */
	public function handleError($num, $message, $file, $line, array $context = null) 
	{
		$e = new \Exception("<strong>{$message}</strong> on line {$line} of {$file}");
		return $this->handleException($e);
	}

	/**
	 * @param \Exception $e
	 * @return \Bliss\Response\ResponseInterface
	 */
	public function handleException(\Exception $e) 
	{
		$request = new HttpRequest();
		$request->setParams([
			"module" => "error",
			"controller" => "error",
			"action" => "error",
			"format" => $this->app()->request()->getParam("format"),
			"exception" => $e
		]);

		return $this->app()->exec($request);
	}
}