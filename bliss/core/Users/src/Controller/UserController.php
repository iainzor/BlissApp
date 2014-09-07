<?php
namespace Users\Controller;

use Bliss\Controller\MultiActionController,
	Users\User;

class UserController extends MultiActionController 
{
	public function init()
	{}
	
	public function indexAction()
	{
		$resources = \System\Module::resources();
		
		if ($this->request->getParam("format") === "json") {
			$loader = $resources->getLoader(User::RESOURCE_NAME, $this->request->getParams());
			$userId = $this->getParam("id");
			$user = $loader->load(array(
				"params" => array(
					"id" => $userId
				)
			));
			if (empty($user)) {
				throw new \Exception("Could not find user by ID #{$userId}", 404);
			}
			$user->makePublic();
			
			$this->view->setAttributes(array(
				"user" => $user
			));
		}
	}
}