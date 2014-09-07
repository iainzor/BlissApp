<?php
namespace UI\View\Format;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a format to the collection
	 * 
	 * @param \Bliss\View\Format\FormatInterface $format
	 */
	public function add(FormatInterface $format)
	{
		$this->addItem($format);
	}
	
	/**
	 * Find a format by it's extension
	 * 
	 * @param string $extension
	 * @return \Bliss\View\Format\FormatInterface
	 */
	public function find($extension)
	{
		foreach ($this->getAll() as $format) {
			if ($format->getExtension() === $extension) {
				return $format;
			}
		}
		
		throw new \UnexpectedValueException("Extension is unsupported: {$extension}");
	}
}