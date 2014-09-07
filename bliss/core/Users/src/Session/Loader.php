<?php
namespace Users\Session;

use Users\Loader as UserLoader,
	System\Loader\AbstractLoader,
	System\Resource\AbstractComponent;

class Loader extends AbstractLoader
{
	/**
	 * @var \Users\Loader 
	 */
	private $userLoader;
	
	/**
	 * Constructor
	 * 
	 * @param \Users\Session\StorageInterface $sessionStorage
	 */
	public function __construct(StorageInterface $sessionStorage)
	{
		$this->setStorage($sessionStorage);
	}
	
	/**
	 * Set the loader instance used to load user information for sessions
	 * 
	 * @param \Users\Loader $userLoader
	 */
	public function setUserLoader(UserLoader $userLoader)
	{
		$this->userLoader = $userLoader;
	}
	
	/**
	 * Load a session using its ID
	 * 
	 * @param string $id
	 * @return \Users\Session\Session|null
	 */
	public function loadById($id)
	{
		$session = $this->load(array(
			"params" => array(
				"id" => $id
			)
		));
		
		if (!$session) {
			return null;
		}
		
		return $session;
	}
	
	/**
	 * @return string
	 */
	public function getResourceName() 
	{
		return Session::RESOURCE_NAME;
	}
	
	/**
	 * @param \System\Resource\AbstractComponent $component
	 * @return \User\Session\Session
	 */
	public function loadChildComponents(AbstractComponent $component) 
	{
		return $this->_attachComponents($component);
	}
	
	/**
	 * Attach any available components to the session
	 * 
	 * @param \Users\Session\Session $session
	 * @return \Users\Session\Session
	 */
	private function _attachComponents(Session $session)
	{
		$params = [
			"id" => $session->getUserId()
		];
		
		try {
			$userLoader = $this->resources->getLoader(\Users\User::RESOURCE_NAME, $params);
			$user = $userLoader->load([
				"params" => $params
			]);

			if ($user) {
				$session->setUser($user);
			}
		} catch(\Exception $e) {}
		
		return $session;
	}
}