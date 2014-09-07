<?php
namespace Users\Session;

class Collection extends \System\Resource\ComponentCollection
{
	/**
	 * Add a session to the collection
	 * 
	 * @param \Users\Session\Session $session
	 */
	public function add(Session $session)
	{
		$this->addComponent($session);
	}
}