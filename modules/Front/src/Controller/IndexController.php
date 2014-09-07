<?php
namespace Front\Controller;

use Bliss\Controller\MultiActionController;

class IndexController extends MultiActionController 
{
	public function init()
	{}
	
	public function indexAction()
	{
		$datastore = $this->app()->datastore();
		$query = $datastore->createQuery(\Front\Resource::RESOURCE_NAME);
		
		return [
			"app" => $this->app(),
			"request" => $this->app()->request()
		];
	}
}