<?php
namespace Users;

use System\Params\Container as ParamContainer,
	System\Loader\AbstractLoader,
	System\Resource\AbstractComponent;

class Loader extends AbstractLoader
{	
	/**
	 * @var \Users\StorageInterface
	 */
	protected $storage;
	
	/**
	 * @var \System\Params\Container
	 */
	private $paramContainer;
	
	/**
	 * @var boolean
	 */
	private $makeUsersPublic = false;
	
	/**
	 * Constructor
	 * 
	 * @param \Users\StorageInterface $userStorage
	 */
	public function __construct(StorageInterface $userStorage)
	{
		$this->setStorage($userStorage);
	}
	
	/**
	 * Set the parameter container used to get and set user parameters
	 * 
	 * @param \System\Params\Container $paramContainer
	 */
	public function setParamContainer(ParamContainer $paramContainer)
	{
		$this->paramContainer = $paramContainer;
	}
	
	/**
	 * Whether to make all loaded users public
	 * 
	 * @param boolean $flag
	 */
	public function makeUsersPublic($flag = true)
	{
		$this->makeUsersPublic = (boolean) $flag;
	}
	
	/**
	 * Load all available users
	 * 
	 * @param array $config Storage configuration
	 * @return \Users\Collection
	 */
	public function loadAll(array $config)
	{
		$collection = parent::loadAll($config);
		
		if ($this->makeUsersPublic) {
			$collection->makePublic();
		}
		
		return $collection;
	}
	
	/**
	 * Attempt to load a user by either their username or email address
	 * 
	 * @param string $username
	 * @return \Users\User|null
	 */
	public function loadByUsernameOrEmail($username)
	{
		$result = $this->storage->loadByUsernameOrEmail($username);
		if (!empty($result)) {
			$user = User::factory($result);
			
			if ($this->loadChildComponents) {
				$this->loadChildComponents($user);
			}
			
			return $user;
		}
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getResourceName() 
	{
		return User::RESOURCE_NAME;
	}
	
	/**
	 * @param \System\Resource\AbstractComponent $component
	 * @return \Users\User
	 */
	public function loadChildComponents(AbstractComponent $component) 
	{
		return $this->_attachComponents($component);
	}
	
	/**
	 * Attach any components available to the user
	 * 
	 * @param \Users\User $user
	 * @return \Users\User
	 */
	private function _attachComponents(User $user)
	{
		if (isset($this->paramContainer)) {
			$params = $this->paramContainer->getAll(User::RESOURCE_NAME, $user->getId());
			$user->setParams($params);
		}
		
		return $user;
	}
}