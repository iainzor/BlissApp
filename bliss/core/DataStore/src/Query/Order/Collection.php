<?php
namespace DataStore\Query\Order;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a order to the collection
	 * 
	 * @param \DataStore\Query\Order\Order $order
	 */
	public function add(Order $order)
	{
		$this->addItem($order);
	}
}