<?php
namespace DataStore\Saver;

use DataStore\Query\Query,
	DataStore\Query\Relation\Collection as Relationships,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection;

interface SaverInterface
{
	public function save(Query $query, Model $model);
	
	public function saveAll(Query $query, ModelCollection $models);
	
	public function saveRelationships(Model $model, Relationships $relationships);
}