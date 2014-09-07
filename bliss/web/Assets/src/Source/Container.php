<?php
namespace Assets\Source;

use Bliss\Application,
	Bliss\Module\AbstractModule;

class Container
{
	/**
	 * @var \Assets\Source\Collection
	 */
	private $sources;
	
	/**
	 * @var \Bliss\Application
	 */
	private $app;
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->sources = new Collection();
	}
	
	/**
	 * Add an asset source to the container
	 * 
	 * @param \Assets\Source\AbstractSource $source
	 */
	public function addSource(AbstractSource $source)
	{
		$source->setContainer($this);
		
		$this->sources->add($source);
	}
	
	/**
	 * Add an asset source for a module
	 * 
	 * @param \Bliss\Module\AbstractModule $module
	 * @param \Assets\Source\AbstractSource $source
	 */
	public function addModuleSource(AbstractModule $module, AbstractSource $source)
	{
		$source->setRootPath($module->resolvePath($source->getPath()));
		$source->setPath("modules/". $module->getName() ."/". $source->getPath());
		
		$this->addSource($source);
	}
	
	/**
	 * Get all asset sources by a single type
	 * 
	 * @param string $type
	 * @return \Assets\Source\Collection
	 */
	public function getByType($type)
	{
		return $this->sources->filter(function(AbstractSource $source) use ($type) {
			return $type === $source->getType();
		});
	}
	
	/**
	 * Get the asset version
	 * 
	 * @return int
	 */
	public function getVersion()
	{
		return (int) $this->app->build()->get("number", 1);
	}
}
