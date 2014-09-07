<?php
namespace UI\View\Renderer;

use Bliss\Response\ResponseInterface;

class FileRenderer implements RendererInterface
{
	/**
	 * @var \Bliss\Response\ResponseInterface
	 */
	protected $response;
	
	/**
	 * @var string
	 */
	protected $rootPath;
	
	/**
	 * @var string 
	 */
	protected $filePath;
	
	/**
	 * Constructor
	 * 
	 * @param \Bliss\Response\ResponseInterface $response
	 */
	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}
	
	/**
	 * @param string $rootPath
	 */
	public function setRootPath($rootPath) 
	{
		$this->rootPath = $rootPath;
	}
	
	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}
	
	/**
	 * Get a parameter from the response
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($name, $defaultValue = null)
	{
		return $this->response->getParam($name, $defaultValue);
	}
	
	/**
	 * Render the file and return the result
	 * 
	 * @param string $filePath
	 * @return string
	 * @throws \Exception
	 */
	public function render($filePath = null)
	{
		if ($filePath !== null) {
			$this->filePath = $filePath;
		}
		
		$realPath = $this->filePath .".phtml";
		
		if (!is_file($realPath)) {
			$realPath = $this->rootPath ."/{$realPath}";
		}
		
		if (!is_file($realPath)) {
			throw new \Exception("File could not be found: {$this->filePath}");
		}
		
		ob_start();
		include $realPath;
		return ob_get_clean();
	}
}