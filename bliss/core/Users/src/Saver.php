<?php
namespace Users;

use System\Params\Container as ParamContainer,
	System\Saver\AbstractSaver,
	System\Resource\AbstractComponent;

class Saver extends AbstractSaver
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
	private $safeSaving = true;
	
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
	 * Set the container used to save user parameters
	 * 
	 * @param \System\Params\Container $paramContainer
	 */
	public function setParamsContainer(ParamContainer $paramContainer)
	{
		$this->paramContainer = $paramContainer;
	}
	
	/**
	 * Set whether safe saving should be on
	 * If set to true, sensitive information will be striped out before the user is saved
	 * 
	 * @param type $flag
	 */
	public function setSafeSaving($flag = true)
	{
		$this->safeSaving = (boolean) $flag;
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
	 * @throws \Exception
	 */
	public function saveComponent(AbstractComponent $component) {
		return $this->_save($component);
	}

	/**
	 * Save a user to the storage instance
	 * If a parameter container is set, any available user parameters will also be saved
	 * 
	 * @param \Users\User $user
	 * @return \Users\User
	 */
	private function _save(User $user)
	{
		$user->setUpdated(time());
		
		if (!$user->getId()) {
			if ($this->safeSaving === true) {
				throw new \Exception("Cannot create user when safe saving is set and no user ID is provided");
			}
			
			$user->setCreated(time());
			$id = $this->storage->save($user->toArray());
			$user->setId($id);
		} else {
			$data = $this->safeSaving === true ? $user->toSafeArray() : $user->toArray();
			$this->storage->save($data, array(
				"id" => $user->getId()
			));
		}
		
		return $user;
	}
	
	/**
	 * Save a password for a user
	 * 
	 * @param \Users\User $user
	 * @param string $passwordHash
	 */
	public function savePassword(User $user, $passwordHash)
	{
		$this->storage->save(array(
			"password" => $passwordHash
		), array(
			"id" => $user->getId()
		));
	}
	
	/**
	 * @param \System\Resource\AbstractComponent $component
	 * @return \Users\User
	 */
	public function saveChildComponents(AbstractComponent $component) 
	{
		return $this->_saveComponents($component);
	}
	
	/**
	 * Save any components for the user
	 * 
	 * @param \Users\User $user
	 * @return \Users\User
	 */
	private function _saveComponents(User $user)
	{
		if (isset($this->paramContainer)) {
			$this->paramContainer->setAll($user->getParams());
		}
	}
}