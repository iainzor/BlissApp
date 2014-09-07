<?php
namespace UI\View\Format;

use Bliss\Response\ResponseInterface;

interface FormatInterface
{
	/**
	 * @return string
	 */
	public function getExtension();
	
	/**
	 * @param \Bliss\Response\ResponseInterface
	 * @return \Bliss\View\Renderer\RendererInterface
	 */
	public function generateRenderer(ResponseInterface $response);
}