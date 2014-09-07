<?php
namespace DataStore\Query\Join;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a join definition to the collection
	 * 
	 * @param \DataStore\Query\Join\Join $join
	 */
	public function add(Join $join)
	{
		$this->addItem($join);
	}
}