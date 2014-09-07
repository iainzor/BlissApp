<?php
namespace UI;

class Module extends \Bliss\Module\AbstractModule implements UIInterface
{
	const NAME = "ui";
	
	const PARAM_UI_NAME = "ui.name";
	
	/**
	 * @var \UI\UIIinterface
	 */
	private $ui;
	
	/**
	 * @var \UI\View\Container
	 */
	private $view;
	
	/**
	 * @return string
	 */
	public function getName() { return self::NAME; }
	
	public function init() 
	{}
	
	/**
	 * Set the default UI 
	 * 
	 * @param \UI\UIInterface $ui
	 */
	public function setUI(UIInterface $ui)
	{
		$this->ui = $ui;
	}
	
	/**
	 * @return \UI\UIInterface
	 */
	public function getUI()
	{
		if (!isset($this->ui)) {
			$this->ui = $this;
		}
		
		return $this->ui;
	}
	
	public function view()
	{
		if (!isset($this->view)) {
			$this->view = new View\Container();
		}
		
		return $this->view;
	}
	
	/**
	 * @param \UI\View\Renderer\RendererInterface $renderer
	 * @return string
	 */
	public function wrap(View\Renderer\RendererInterface $renderer) 
	{
		return $renderer->render();
	}
}