<?php
namespace Bliss\Controller;

use Bliss\Console,
	Bliss\String;

abstract class MultiActionController extends ActionController
{
	protected function _getViewPath()
	{
		if (!isset($this->viewPath)) {
			$controllerName = $this->request->getControllerName();
			$viewPath = "views/{$controllerName}";
		} else {
			$viewPath = $this->viewPath;
		}

		$action = $this->request->getParam("action", "index");

		return "{$viewPath}/{$action}";
	}

	/*
	public function exec() {
		$this->viewPath = $this->_getViewPath();

		parent::exec();
	}
	*/
	
	/**
	 * Execute an action of the controller
	 * 
	 * @return array
	 * @throws \Exception
	 */
	public function execAction()
	{
		$action = $this->getParam("action", "index");
		$method = String::toCamelCase($action)."Action";

		if (!method_exists($this, $method)) {
			throw new InvalidActionException("Invalid action: {$action}");
		}

		Console::log("Executing action: {$action}");

		return call_user_func(array($this, $method));
	}
}