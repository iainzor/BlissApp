<?php
namespace Users\Search;

use Users\User,
	Search\Provider\AbstractDbProvider,
	Search\Result\Result,
	Search\Result\Collection as ResultCollection;

class DbProvider extends AbstractDbProvider
{
	public function getResourceName() { return \Users\Module::RESOURCE_NAME; }

	public function getResourceTitle() { return \Users\Module::RESOURCE_TITLE; }

	public function getResults($query) 
	{
		$maxResults = $this->container->getMaxResults();
		$limitClause = $maxResults > 0 ? "LIMIT {$maxResults}" : null;
		$results = $this->db->fetchAll("
			SELECT		*
			FROM		users
			WHERE		firstName LIKE :query
						OR lastName LIKE :query
						OR nickname LIKE :query
						OR username LIKE :query
			ORDER BY	firstName ASC,
						lastName ASC,
						nickname ASC,
						username ASC
			{$limitClause}
		", array(
			":query" => "%{$query}%"
		));
		$collection = new ResultCollection();
		
		foreach ($results as $data) {
			$user = User::factory($data);
			$result = new Result();
			$result->setId($user->getId());
			$result->setResourceName($this->getResourceName());
			$result->setTitle($user->getNickname());
			$result->setDescription("Member since ". date("F, Y", $user->getCreated()));
			$result->setPath($user->getPath());
			
			$collection->add($result);
		}
		
		return $collection;
	}

}