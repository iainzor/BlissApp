<?php
namespace DataStore\Query\Group;

class Collection extends \Bliss\Collection
{
	/**
	 * Add a group to the collection
	 * 
	 * @param \DataStore\Query\Group\Group $group
	 */
	public function add(Group $group)
	{
		$this->addItem($group);
	}
	
	/**
	 * Generate a new collection from an array of groups
	 * 
	 * @param array $groups
	 * @return \DataStore\Query\Group\Collection
	 */
	public static function factory(array $groups)
	{
		$collection = new self();
		foreach ($groups as $groupData) {
			$group = Group::factory($groupData);
			$collection->add($group);
		}
		return $collection;
	}
}