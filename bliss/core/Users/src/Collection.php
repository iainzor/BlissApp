<?php
namespace Users;

class Collection extends \System\Resource\ComponentCollection
{
	/**
	 * Add a user to the collection
	 * 
	 * @param \Users\User $user
	 */
	public function add(User $user)
	{
		$this->addComponent($user);
	}
	
	/**
	 * Convert the users in the collection to public facing
	 * 
	 * @return void
	 */
	public function makePublic()
	{
		$this->each(function(User $user) {
			$user->makePublic();
		});
	}
}