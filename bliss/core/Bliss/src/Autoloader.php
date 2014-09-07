<?php
namespace Bliss;

class Autoloader
{
	/**
	 * Collection of registered namespaces
	 * @var array	[namespace => directory]
	 */
	private $namespaces = array();
	
	/**
	 * @var \Bliss\Application\Container
	 */
	private $app;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		spl_autoload_register(array($this, "_load"));
	}
	
	/**
	 * @param \Bliss\Application\Container $app
	 */
	public function setApp(Application\Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Register a directory for a single namespace
	 *
	 * @param string $namespace
	 * @param string $directory
	 */
	public function registerNamespace($namespace, $directory)
	{
		$this->namespaces[$namespace] = $directory;
	}

	/**
	 * Attempt to load a class file
	 *
	 * @param string $className
	 */
	private function _load($className)
	{
		foreach ($this->namespaces as $namespace => $dir)
		{
			$search = preg_quote($namespace);
			if (preg_match("/{$search}/", $className)) {
				$file = str_replace("_", "\\", $className);
				$file = str_replace("{$namespace}\\", "", $file) .".php";
				$file = str_replace("\\", "/", $file);
				$path = $dir ."/{$file}";

				if (file_exists($path)) {
					require_once $path;
					return true;
				}
			}
		}
	}
}