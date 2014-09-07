<?php
namespace Assets\Renderer;

use Assets\JSMinPlus;

class JsRenderer extends AbstractTextCollectionRenderer
{
	protected $extension = "js";

	/**
	 * Get the CSS mime type
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return "text/javascript";
	}

	/**
	 * Compress Javascript file contents
	 *
	 * @param string $string
	 * @return string
	 */
	public function compress($string)
	{
		return JSMinPlus::minify($string);
	}

}