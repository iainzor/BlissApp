<?php
namespace Front;

use Bliss\Module\AbstractModule,
	Assets\Source\JsSource;

class Module extends AbstractModule 
{
	const NAME = "front";
	
	public function getName() { return self::NAME; }
	
	public function init()
	{}
	
	public function postInit()
	{
		$datastore = $this->app()->datastore();
		$datastore->registerResource(Resource::RESOURCE_NAME, function() {
			$storage = new \DataStore\Storage\GenericArrayStorage();
			
			return new Query($storage);
		});
	}
	
	/**
	 * Run setup methods for the module
	 */
	public function setup()
	{
		$this->_initAssets();
	}
	
	/**
	 * Add module specific assets to the application
	 */
	private function _initAssets()
	{
		$assets = \Assets\Module::container();
		$assets->addSource(
			new JsSource("modules/Front/assets/js/module.js")
		);
	}
}