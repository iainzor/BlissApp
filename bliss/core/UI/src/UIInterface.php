<?php
namespace UI;

interface UIInterface
{
	/**
	 * @param \UI\View\Renderer\RendererInterface
	 * @return string
	 */
	public function wrap(View\Renderer\RendererInterface $renderer);
}