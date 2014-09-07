<?php
namespace Bliss\Controller;

abstract class ActionController extends AbstractController
{
	/**
	 * The path to the view to render
	 * @var string
	 */
	protected $viewPath = null;

	/**
	 * Whether to auto-render the view
	 * @var boolean
	 */
	protected $autoRender = true;

	/**
	 * If set to TRUE an exception will be thrown if a default
	 * view file (eg: index.phtml) is not found.
	 *
	 * @var boolean
	 */
	protected $forceDefaultViewRender = true;

	abstract public function execAction();

	/**
	 * Set whether the controller should automatically render the view
	 *
	 * @param boolean $flag
	 */
	public function setAutoRender($flag = true)
	{
		$this->autoRender = (boolean) $flag;
	}

	/**
	 * @see self::$forceDefaultViewRender
	 * @param boolean $flag
	 */
	public function setForceDefaultViewRender($flag = true)
	{
		$this->forceDefaultViewRender = (boolean) $flag;
	}

	/**
	 * Get the view path for the controller
	 *
	 * @return string
	 */
	public function getViewPath()
	{
		$controllerName = $this->request->getControllerName();

		return "views/{$controllerName}/{$controllerName}";
	}

	/**
	 * Determine the correct action to execute
	 * 
	 * @return array
	 */
	public function execute()
	{
		return $this->execAction();
	}
}