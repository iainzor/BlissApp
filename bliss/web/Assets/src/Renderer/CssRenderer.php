<?php
namespace Assets\Renderer;

class CssRenderer extends AbstractTextCollectionRenderer
{
	protected $extension = "css";

	/**
	 * Get the CSS mime type
	 *
	 * @return string
	 */
	public function getContentType()
	{
		return "text/css";
	}

	/**
	 * Compress a stylesheet
	 *
	 * @param string $string
	 */
	public function compress($string)
	{
		$string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);
		$string = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   '), '', $string);

		return $string;
	}

}