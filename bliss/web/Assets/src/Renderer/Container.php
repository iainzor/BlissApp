<?php
namespace Assets\Renderer;

use Bliss\String,
	Bliss\Application,
	Assets\Source\Collection as SourceCollection,
	Assets\Source\AbstractSource;

class Container
{
	/**
	 * @var \Assets\Renderer\AbstractRenderer
	 */
	protected $renderer;
	
	/**
	 * @var \Bliss\Application
	 */
	protected $application;

	/**
	 * @var \Bliss\Request
	 */
	protected $request;

	/**
	 * @var \Bliss\Response
	 */
	protected $response;

	/**
	 * @var \Bliss\Cache\Container
	 */
	protected $cache;
	
	/**
	 * @var int
	 */
	protected $version = 1;

	/**
	 * Constructor
	 *
	 * @param \Bliss\Application $application
	 */
	public function __construct(Application $application)
	{
		$this->application = $application;
		$this->request = $application->getRequest();
		$this->response = $application->getResponse();
		$this->cache = $application->getContainer("cache");
	}

	/**
	 * Set the renderer by it's name
	 *
	 * @param string $name
	 * @param array|\Bliss\Config $config
	 */
	public function setRenderer($name, $config = null)
	{
		$className = __NAMESPACE__ ."\\". String::toCamelCase($name) ."Renderer";
		if (!class_exists($className)) {
			throw new \InvalidArgumentException("Invalid renderer name: {$name}");
		}

		$this->renderer = new $className();
		if (isset($config)) {
			$this->renderer->setConfig($config);
		}
	}
	
	/**
	 * Set the version of the asset to be rendered
	 * 
	 * @param int $version
	 */
	public function setVersion($version)
	{
		$this->version = (int) $version;
	}
	
	/**
	 * Set the source files to render
	 * 
	 * @param \Assets\Source\Collection $sources
	 */
	public function setSources(SourceCollection $sources)
	{
		$app = $this->application;
		$sources->each(function(AbstractSource $source) use ($app) {
			$rootPath = $source->getRootPath();
			if (!$rootPath) {
				$rootPath = $app->resolvePath($source->getPath());
				$source->setRootPath($rootPath);
			}
		});
		$sources->sort(function(AbstractSource $a, AbstractSource $b) {
			if ($a->getPriority() === $b->getPriority()) {
				return 0;
			}
			
			return $a->getPriority() < $b->getPriority() ? -1 : 1;
		});
		
		$this->renderer->setSources($sources);
	}

	/**
	 * Render a file or path
	 *
	 * @param string $path
	 * @param int $version
	 */
	public function render($path = null, $version = 1)
	{
		if (isset($this->cache)) {
			$this->renderer->setCache($this->cache);
		}

		$this->renderer->setPath($path);
		$this->renderer->setVersion($version);
		$this->renderer->render($this->request, $this->response);
	}
}