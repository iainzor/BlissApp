<?php
namespace Assets\Source;

abstract class AbstractSource implements SourceInterface
{
	/**
	 * @var string
	 */
	protected $path;
	
	/**
	 * @var int
	 */
	protected $priority = 1000;
	
	/**
	 * @var string
	 */
	protected $rootPath;
	
	/**
	 * @var \Assets\Source\Container
	 */
	protected $container;
	
	/**
	 * Constructor
	 * 
	 * @param string $path The path to an asset file or directory
	 * @param int $priority The order in which the source will be prioritized
	 */
	public function __construct($path, $priority = 1000)
	{
		$this->path = preg_replace("/^(.*)\.[a-z0-9]+$/i", "$1", $path);
		$this->priority = (int) $priority;
	}
	
	/**
	 * Get the path to the asset source
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}
	
	/**
	 * Set the path of the source file
	 * 
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
	
	/**
	 * Set the absolute path to the asset
	 * 
	 * @param string $filePath
	 */
	public function setRootPath($rootPath)
	{
		$this->rootPath = $rootPath;
	}
	
	/**
	 * Get the absolute path to the asset
	 * 
	 * @return string
	 */
	public function getRootPath()
	{
		return $this->rootPath;
	}
	
	/**
	 * Get the source's priority
	 * 
	 * @return int
	 */
	public function getPriority() 
	{
		return $this->priority;
	}

	/**
	 * Set the source's priority
	 * 
	 * @param int $priority
	 */
	public function setPriority($priority) 
	{
		$this->priority = (int) $priority;
	}

		
	/**
	 * Set the request instance
	 * 
	 * @param \Assets\Source\Container
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;
	}
	
	/**
	 * Get the full URI to the asset
	 * 
	 * @return string
	 */
	public function getUri()
	{
		if (preg_match("/^((http)s?:)?\/\//i", $this->path)) {
			return $this->path;
		}
		
		$version = $this->container->getVersion();
		$uri = "./assets/". $this->path .".". $version .".". $this->getExtension();
		
		return $uri;
	}
}
