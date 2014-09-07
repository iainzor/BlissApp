<?php
namespace UI\View\Renderer;

interface RendererInterface
{
	/**
	 * @param string $rootPath
	 */
	public function setRootPath($rootPath);
	
	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath);
	
	/**
	 * @param string $filePath
	 * @return string
	 */
	public function render($filePath = null);
}