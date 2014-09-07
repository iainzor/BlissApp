<?php
namespace UI\View\Format;

use Bliss\Response\ResponseInterface,
	UI\View\Renderer\FileRenderer;

class HtmlFormat extends AbstractFormat 
{
	const EXTENSION = "html";
	
	/**
	 * @return string
	 */
	public function getExtension() { return self::EXTENSION; }
	
	/**
	 * @param \Bliss\Response\ResponseInterface $response
	 * @return \Bliss\View\Renderer\FileRenderer
	 */
	public function generateRenderer(ResponseInterface $response) 
	{
		$response->setContentType("text/html");
		return new FileRenderer($response);
	}
}