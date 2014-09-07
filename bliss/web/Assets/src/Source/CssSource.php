<?php
namespace Assets\Source;

class CssSource extends AbstractSource
{
	const TYPE = "css";
	
	/**
	 * Get asset source type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return self::TYPE;
	}
	
	/**
	 * Get the file extension of the source files
	 * 
	 * @return string
	 */
	public function getExtension()
	{
		return "css";
	}
}