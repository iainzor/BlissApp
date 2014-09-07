<?php
namespace Bliss\Router\Route;

use Bliss\Component;

abstract class AbstractRoute extends Component implements RouteInterface
{
	/**
	 * @var int
	 */
	protected $priority = 0;
	
	/**
	 * Default values for parameters
	 * @var array
	 */
	protected $defaults = array();
	
	/**
	 * Set the priority of the route.  Higher means the route is more likely to be used
	 * 
	 * @param int $i
	 */
	public function setPriority($i = 0)
	{
		$this->priority = (int) $i;
	}
	
	/**
	 * Get the route's priority
	 * 
	 * @return int
	 */
	public function priority()
	{
		return $this->priority;
	}
	
	/**
	 * Set the default value of parameters
	 * 
	 * @param array $defaults
	 */
	public function setDefaults(array $defaults)
	{
		$this->defaults = array_merge($this->defaults, $defaults);
	}
}