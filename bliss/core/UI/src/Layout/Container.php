<?php
namespace UI\Layout;

use UI\View\Container as View;

class Container
{
	/**
	 * @param \UI\View\Container $view
	 * @return string
	 */
	public function render(View $view)
	{
		// Wrap view content in layouts
		
		return $view->render();
	}
}