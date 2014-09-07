<?php
namespace Assets\Source;

class JsSource extends AbstractSource
{
	const TYPE = "js";
	
	/**
	 * Get the asset source type
	 * 
	 * @return string
	 */
	public function getType() 
	{
		return self::TYPE;
	}
	
	/**
	 * Get the extension used for the source's files
	 * 
	 * @return string
	 */
	public function getExtension() 
	{
		return "js";
	}
}