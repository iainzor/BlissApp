<?php
namespace Users\Controller;

use Bliss\Controller\MultiActionController,
	Users\User,
	Users\DbStorage as UserStorage,
	Users\Saver as UserSaver,
	Users\PasswordChanger,
	Users\Session\InvalidCredentialsException,
	Users\Validator\SignUpValidator;

class AccountController extends MultiActionController 
{
	public function init() 
	{}
	
	/**
	 * Overview of the user's account
	 * 
	 * @url /account[/index]
	 */
	public function indexAction()
	{}
	
	/**
	 * Allows the user to edit their profile
	 * 
	 * @url /account/profile
	 */
	public function profileAction()
	{
		if ($this->request->isPost()) {
			$user = User::factory($this->request->getParam("user"));
			
			$this->_saveUser($user);
			
			$this->view->setAttributes(array(
				"user" => $user
			));
		}
	}
	
	/**
	 * Allows the user to change their password
	 * 
	 * @url /account/password
	 * @param string $currentPassword
	 * @param string $newPassword
	 * @param string $newPasswordConfirm
	 */
	public function passwordAction()
	{
		if ($this->request->isPost()) {
			$user  = \Users\Module::session()->getUser();
			$currentPassword = $this->request->getParam("currentPassword");
			$newPassword = $this->request->getParam("newPassword");
			$newPasswordConfirm = $this->request->getParam("newPasswordConfirm");
			
			if ($newPassword !== $newPasswordConfirm) {
				throw new \Exception("New password and confirmation do not match");
			}
			
			$userStorage = new UserStorage($this->database);
			$passwordChanger = new PasswordChanger($user, $userStorage);
			$passwordChanger->change($currentPassword, $newPassword);
		}
	}
	
	/**
	 * Allows the user to adjust specific application settings
	 * 
	 * @url /account/settings
	 * @param array $user
	 */
	public function settingsAction()
	{
		if ($this->request->isPost()) {
			$user = User::factory($this->request->getParam("user"));
			$this->_saveUser($user);

			$this->view->setAttributes(array(
				"user" => $user
			));
		}
	}
	
	/**
	 * Page used to sign users into the system
	 * 
	 * @url /account/sign-in
	 */
	public function signInAction()
	{
		if ($this->request->isPost()) {
			$username = $this->getParam("username");
			$password = $this->getParam("password");
			
			$sessionHandler = \Users\Module::sessionHandler();
			try {
				$session = $sessionHandler->login($username, $password);
				$user = $session->getUser();
			} catch (InvalidCredentialsException $e) {
				throw new \Exception("Invalid user credentials provided", $e->getCode(), $e);
			}
			
			\Logs\Module::logger()->log("signed in", \Users\User::RESOURCE_NAME, $user->getId());
			
			$this->view->setAttributes([
				"user" => $user
			]);
		}
	}
	
	/**
	 * Page used to sign users out of the system
	 * 
	 * @url /account/sign-out
	 */
	public function signOutAction()
	{
		$user = \Users\Module::session()->getUser();
		
		\Logs\Module::logger()->log("signed out", \Users\User::RESOURCE_NAME, $user->getId());
		
		$sessionHandler = \Users\Module::sessionHandler();
		$sessionHandler->logout();
		
		$self = $this;
		$this->application->addEvent(\Bliss\Application::EVENT_EXIT, function() use ($self) {
			$self->redirect("/");
		});
	}
	
	/**
	 * Page used to handle user sign ups
	 * 
	 * @url /account/sign-up
	 */
	public function signUpAction()
	{
		if ($this->request->isPost() || 1 == 1) {
			$userData = $this->getParam("user", []);
			unset($userData["id"]);
			
			$user = User::factory($userData);
			$validator = new SignUpValidator(
				new UserStorage($this->database)
			);
			$results = $validator->run($userData);
			
			if ($results->areValid()) {
				$user->setIsActive(true);
				$user->setRole(User::ROLE_USER);
				$user->setParam("gravatarEmail", $user->getEmail());
				
				$saver = UserStorage::generateSaver($this->database);
				$saver->setSafeSaving(false);
				$saver->save($user);
				$saver->savePassword($user, $user->hasher()->hash($userData["password"]));
				
				$sessionHandler = \Users\Module::sessionHandler();
				$sessionHandler->login($user->getUsername(), $userData["password"]);
			}
			
			$this->view->setAttributes(array(
				"results" => $results,
				"user" => $user
			));
		}
	}
	
	/**
	 * Save a user to the storage instance
	 * 
	 * @param \Users\User $user
	 */
	private function _saveUser(User $user)
	{
		$user->setId(\Users\Module::session()->getUserId());
		
		$userStorage = new UserStorage($this->database);
		$userSaver = new UserSaver($userStorage);
		$userSaver->setParamsContainer(\System\Module::params());
		$userSaver->save($user);
	}
}