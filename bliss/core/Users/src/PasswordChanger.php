<?php
namespace Users;

class PasswordChanger
{
	/**
	 * @var \Users\User
	 */
	private $user;
	
	/**
	 * @var \Users\StorageInterface
	 */
	private $userStorage;
	
	/**
	 * Constructor
	 * 
	 * @param \Users\User
	 * @param \Users\StorageInterface $userStorage
	 */
	public function __construct(User $user, StorageInterface $userStorage)
	{
		$this->user = $user;
		$this->userStorage = $userStorage;
	}
	
	/**
	 * Change the user's password
	 * 
	 * @param string $currentPassword
	 * @param string $newPassword
	 * @throws \Exception
	 */
	public function change($currentPassword, $newPassword)
	{
		if (!User::hasher()->isValid($currentPassword, $this->user->getPassword())) {
			throw new \Exception("The current password provided is invalid");
		}
		
		$newHash = User::hasher()->hash($newPassword);
		$saver = new Saver($this->userStorage);
		$saver->savePassword($this->user, $newHash);
	}
}