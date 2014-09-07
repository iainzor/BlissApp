<?php
namespace DataStore\Loader;

use DataStore\Query\Query,
	DataStore\Query\Relation\Collection as Relationships,
	DataStore\Model\Collection as ModelCollection;

interface LoaderInterface
{
	public function load(Query $query);
	
	public function loadAll(Query $query);
	
	public function attachRelationships(ModelCollection $models, Query $query);
}