<?php
namespace UI;

class Container
{
	/**
	 * @var \UI\View\Container
	 */
	private $view;
	
	/**
	 * @var \UI\Layout\Container 
	 */
	private $layout;
	
	/**
	 * Render the user interface and return the contents
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->layout()->render(
			$this->view()
		);
	}
	
	/**
	 * @return \UI\View\Container
	 */
	public function view()
	{
		if (!isset($this->view)) {
			$this->view = new View\Container();
		}
		
		return $this->view;
	}
	
	/**
	 * @return \UI\Layout\Container
	 */
	public function layout()
	{
		if (!isset($this->layout)) {
			$this->layout = new Layout\Container();
		}
		
		return $this->layout;
	}
}