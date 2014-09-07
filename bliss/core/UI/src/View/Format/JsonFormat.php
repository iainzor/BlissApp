<?php
namespace UI\View\Format;

use Bliss\Response\ResponseInterface,
	UI\View\Renderer\JsonRenderer;

class JsonFormat extends AbstractFormat
{
	const EXTENSION = "json";
	
	/**
	 * @return string
	 */
	public function getExtension() { return self::EXTENSION; }
	
	/**
	 * @param \Bliss\Response\ResponseInterface $response
	 * @return \Bliss\View\Renderer\JsonRenderer
	 */
	public function generateRenderer(ResponseInterface $response) 
	{
		return new JsonRenderer($response);
	}
}