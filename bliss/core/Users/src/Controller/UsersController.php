<?php
namespace Users\Controller;

use Bliss\Controller\MultiActionController,
	Users\User,
	Users\DbStorage as UserStorage,
	Users\Loader as UserLoader,
	Users\Saver as UserSaver,
	Acl\Acl;

class UsersController extends MultiActionController
{
	public function init()
	{}
	
	/**
	 * Display a list of users
	 * 
	 * @url /users[/index]
	 */
	public function indexAction()
	{	
		$resources = \System\Module::resources();
		$loader = $resources->getLoader(User::RESOURCE_NAME);
		
		if ($this->request->getParam("format") === "json") {
			$users = $loader->loadAll(array(
				"paginator" => array(
					"maxResults" => 50
				),
				"sorter" => array(
					"nickname" => "ASC"
				)
			));
			$users->makePublic();
			
			$this->view->setAttributes(array(
				"users" => $users
			));
		}
	}
	
	/**
	 * Create new users
	 * 
	 * @url /users/new
	 */
	public function newAction()
	{	
		$account = \Users\Module::session()->getUser();
		$account->getAcl()->assertIsAllowed(\Users\Module::RESOURCE_NAME, Acl::CREATE);
		
		$resources = \System\Module::resources();
		
		if ($this->request->isPost()) {
			$user = User::factory(
				$this->request->getParam("user")
			);
			$user->setIsActive(true);
			$user->setParam("gravatarEmail", $user->getEmail());
			$saver = $resources->getSaver(User::RESOURCE_NAME, $user->toArray());
			$saver->setSafeSaving(false);
			$saver->save($user);
			$saver->savePassword($user, User::hasher()->hash($user->getPassword()));
			
			$this->view->setAttributes(array(
				"user" => $user
			));
		}
	}
}