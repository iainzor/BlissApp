<?php
namespace Assets\Controller;

use Bliss\Controller\MultiActionController,
	Assets\Renderer\Container as RendererContainer,
	Assets\Source\CssSource,
	Assets\Source\JsSource;

class RenderController extends MultiActionController
{
	public function init()
	{
		$this->setAutoRender(false);
		//$this->view->setRenderLayout(false);
	}

	/**
	 * Render global application assets
	 */
	public function defaultAction()
	{
		$this->_render($this->getParam("path"), "/assets/");
	}
	
	/**
	 * Render module-specific assets
	 */
	public function moduleAction()
	{
		$moduleName = $this->getParam("moduleName");
		$module = $this->app()->modules($moduleName);
		$path = $module->resolvePath($this->getParam("path"));
		
		$this->_render($path);
	}
	
	/**
	 * Renders all assets in a single source
	 * 
	 * @url /assets/all[.$version].$source
	 * @param string $type The source type to render
	 * @param int $version
	 */
	public function allAction()
	{
		$type = $this->getParam("type");
		$version = $this->getParam("version", 1);
		$assets = \Assets\Module::container();
		$container = new RendererContainer($this->application);
		$container->setRenderer($type,
			$this->module->config()->get($type, array())
		);
		$container->setVersion($version);
		
		switch ($type) {
			case CssSource::TYPE:
				$container->setSources($assets->getByType(CssSource::TYPE));
				break;
			case JsSource::TYPE:
				$container->setSources($assets->getByType(JsSource::TYPE));
				break;
		}
		
		$container->render("all.{$version}.{$type}");
	}
	
	/**
	 * Render an asset
	 * 
	 * @param string $path
	 * @param string $prepend A string to prepend to the file path
	 * @throws \InvalidArgumentException
	 */
	private function _render($path, $prepend = null)
	{
		if (!preg_match("/^(.*)\.?([0-9]+)?\.([a-z]+)$/i", $path, $matches)) {
			throw new \InvalidArgumentException("Invalid asset path provided: {$path}");
		}
		
		$filepath = $prepend . $path;
		$type = $matches[3];
		$version = empty($matches[2]) ? 1 : $matches[2];
		
		if (!in_array($type, array("js", "css"))) {
			$type = "file";
		}
		
		/*
		if (!is_file($filepath)) {
			$filepath = $this->application->resolvePath($prepend . $matches[1]);
			$version = $matches[3];
			$type = $matches[4];
			
			if ($type == "file") {
				$filepath .= ".{$type}";
			}
		}
		*/
		$container = new RendererContainer($this->app());
		$container->setRenderer($type,
			$this->module->config()->get($type, array())
		);
		$container->render($filepath, $version);
	}
}