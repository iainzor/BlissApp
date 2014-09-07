<?php
namespace UI\View;

use Bliss\Request\RequestInterface,
	Bliss\Response\ResponseInterface,
	Bliss\Module\AbstractModule;

class Container extends \Bliss\Component
{
	/**
	 * @var \UI\View\Format\Collection
	 */
	private $formats;
	
	/**
	 * @var \UI\View\Format\FormatInterface
	 */
	private $format;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->formats = new Format\Collection();
	}
	
	/**
	 * Register a format for the view
	 * 
	 * @param \Bliss\View\Format\FormatInterface $format
	 */
	public function registerFormat(Format\FormatInterface $format)
	{
		$this->formats->add($format);
	}
	
	/**
	 * Set the format the view will use to render
	 * 
	 * @param string $ext
	 * @throws \InvalidArgumentException
	 */
	public function setFormat($ext)
	{
		foreach ($this->formats as $format) {
			if ($format->getExtension() === $ext) {
				$this->format = $format;
			}
		}
		
		throw new \InvalidArgumentException("Invalid format extension: {$ext}");
	}
	
	/**
	 * Render the layout and return the contents
	 * 
	 * @return string
	 */
	public function render()
	{
		return null;
	}
	
	/**
	 * Render the response parameters according to the request parameters
	 * 
	 * @param \Bliss\Module\AbstractModule $module
	 * @param \Bliss\Request\RequestInterface $request
	 * @param \Bliss\Response\ResponseInterface $response
	 * @return \UI\View\Renderer\RendererInterface
	 */
	public function getRenderer(AbstractModule $module, RequestInterface $request, ResponseInterface $response)
	{
		$viewPath = $module->generateViewPath($request);
		$formatName = $request->getParam("format");
		
		if (!empty($formatName)) {
			$format = $this->formats->find($formatName);
		} else {
			$format = new Format\PhtmlFormat();
		}
		
		if ($format->getExtension()) {
			$viewPath .= ".". $format->getExtension();
		}
		
		$renderer = $format->generateRenderer($response);
		$renderer->setRootPath($module->resolvePath("views"));
		$renderer->setFilePath($viewPath);
		
		return $renderer;
	}
	
	/**
	 * Resolve a path to the view file to be rendered
	 * 
	 * @param string $viewPath
	 * @param \Bliss\View\Format\FormatInterface $format
	 * @return string|null
	 */
	public function resolvePath($viewPath, Format\FormatInterface $format)
	{
		if (!is_file($viewPath)) {
			$viewPath = $this->rootPath ."/{$viewPath}";
		}
		
		$extension = $format->getExtension();
		if ($extension) {
			$viewPath .= ".{$extension}";
		}
		$viewPath .= ".phtml";
		
		return $viewPath;
	}
}