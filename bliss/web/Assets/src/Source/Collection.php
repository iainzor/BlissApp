<?php
namespace Assets\Source;

class Collection extends \Bliss\Collection
{
	/**
	 * Add an asset source to the collection
	 * 
	 * @param \Assets\Source\SourceInterface $source
	 */
	public function add(SourceInterface $source)
	{
		$this->addItem($source);
	}
}