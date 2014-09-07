<?php
namespace Bliss;

class Collection implements \Iterator, \Countable
{
	/**
	 * @var array
	 */
	private $items = array();

	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * Add an item to the collection
	 *
	 * @param mixed $item
	 */
	final protected function addItem($item)
	{
		$this->items[] = $item;
	}

	/**
	 * Add multiple items to the collection
	 *
	 * @param array $items
	 */
	final protected function addItems(array $items)
	{
		foreach ($items as $item) {
			$this->addItem($item);
		}
	}

	/**
	 * Set the items in the collection
	 *
	 * @param array $items
	 */
	final protected function setItems(array $items)
	{
		$this->items = $items;
	}

	/**
	 * Clear all items in the collection
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->items = array();
	}
	
	/**
	 * Alias for clear()
	 */
	public function clearItems()
	{
		$this->clear();
	}

	/**
	 * Get the items in the collection
	 *
	 * @return array
	 */
	public function getAll()
	{
		return $this->items;
	}
	
	/**
	 * Alias for getAll()
	 * 
	 * @return array
	 */
	public function getItems()
	{
		return $this->getAll();
	}

	/**
	 * Get an item using its index
	 *
	 * @param int $index
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getByIndex($index, $defaultValue = null)
	{
		return isset($this->items[$index])
			? $this->items[$index]
			: $defaultValue;
	}
	
	/**
	 * Get the last item in the collection
	 * 
	 * @return mixed
	 */
	public function getLastItem()
	{
		$count = count($this->items);
		if ($count > 0) {
			$i = $count - 1;
			return $this->items[$i];
		}
		return null;
	}
	
	/**
	 * Get the first item in the collection
	 * 
	 * @return mixed
	 */
	public function getFirstItem()
	{
		return isset($this->items[0])
			? $this->items[0]
			: null;
	}

	/**
	 * Convert the collection to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$items = array();
		foreach ($this->items as $key => $item) {
			if (is_object($item) && method_exists($item, "toArray")) {
				$items[$key] = $item->toArray();
			} else {
				$items[$key] = $item;
			}
		}

		return $items;
	}

	/**
	 * Run a function against all items in the collection
	 *
	 * @param callable $callback
	 */
	public function each($callback)
	{
		if (!is_callable($callback)) {
			throw new \Exception("Non-callable callback provided!");
		}

		array_map($callback, $this->items);
	}

	/**
	 * Sort the items in the collection using a callback
	 *
	 * @param callable $callback
	 * @see http://php.net/usort
	 */
	public function sort($callback)
	{
		usort($this->items, $callback);
	}

	/**
	 * Reverse the order of the items in the collection
	 *
	 * @param boolean $preserveKeys
	 */
	public function reverse($preserveKeys = false)
	{
		$this->items = array_reverse($this->items, $preserveKeys);
	}
	
	/**
	 * Loop through the collection and filter out all items that return true
	 * to the callback function
	 * 
	 * @param callable $callback
	 * @return \Bliss\Collection
	 */
	public function filter($callback)
	{
		$newCollection = clone $this;
		$newCollection->clear();
		foreach ($this->items as $item) {
			$result = call_user_func($callback, $item);
			if ($result == true) {
				$newCollection->addItem($item);
			}
		}
		return $newCollection;
	}
	
	/**
	 * Delete an item from the collection
	 * 
	 * @param int $index
	 */
	public function delete($index)
	{
		unset($this->items[$index]);
	}
	
	/**
	 * Merge another collection into this one
	 * 
	 * @param \Bliss\Collection $collection
	 */
	public function merge(Collection $collection)
	{
		if ($collection != null) {
			foreach ($collection as $item) {
				$this->items[] = $item;
			}
		}
		
		$this->rewind();
	}
	
	/**
	 * Check if the collection is empty
	 * 
	 * @return boolean
	 */
	public function isEmpty()
	{
		return count($this->items) === 0;
	}

	/**
	 * Implement \Iterator
	 */
		public function current() { return $this->items[$this->position]; }
		public function key() { return $this->position; }
		public function next() { return $this->position++; }
		public function rewind() { $this->position = 0; }
		public function valid() { return isset($this->items[$this->position]); }

	/**
	 * Implement \Countable
	 */
		public function count() { return count($this->items); }

}