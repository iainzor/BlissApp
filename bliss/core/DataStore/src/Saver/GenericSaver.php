<?php
namespace DataStore\Saver;

use DataStore\Query\Query,
	DataStore\Query\Relation\Collection as Relationships,
	DataStore\Query\Relation\RelationInterface,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection;

class GenericSaver implements SaverInterface
{
	public function save(Query $query, Model $model) 
	{
		$storage = $query->getStorage();
		$model->setUpdated(time());
		
		if (!$model->getId()) {
			if (!$model->getCreated()) {
				$model->setCreated(time());
			}
			
			$storage->insert($model);
			$model->setId(
				$storage->getLastInsertedId()
			);
		} else {
			$storage->update($model);
		}
	}

	/**
	 * Save all models provided
	 * 
	 * @param \DataStore\Query\Query $query
	 * @param \DataStore\Model\Collection $models
	 */
	public function saveAll(Query $query, ModelCollection $models) 
	{
		foreach ($models as $model) {
			$this->save($query, $model);
		}
	}
	
	/**
	 * Insert or replace a model in the query's storage
	 * 
	 * @param \DataStore\Query\Query $query
	 * @param \DataStore\Model\Model $model
	 */
	public function replace(Query $query, Model $model)
	{
		$storage = $query->getStorage();
		$storage->replace($model);
	}

	public function saveRelationships(Model $model, Relationships $relationships) 
	{
		foreach ($relationships as $relation) {
			/* @var $relation \DataStore\Query\Relation\RelationInterface */
			
			$localKeys = $relation->getLocalKey();
			$localModel = $model->get($relation->getName());
			$foreignKeys = $relation->getForeignKey();
			
			foreach ($localKeys as $i => $lKey) {
				$localValue = $model->get($lKey);
				$fKey = isset($foreignKeys[$i]) ? $foreignKeys[$i] : $foreignKeys[0];
				
				if ($localModel instanceof Model || $localModel instanceof ModelCollection) {
					$localModel->set($fKey, $localValue);
				}
			}
			
			$query = $relation->getQuery();
			$storage = $query->getStorage();
			
			if (!$storage->getFields()) {
				continue;
			}
			
			if ($localModel instanceof Model) {
				$query->save($localModel);
			} elseif ($localModel instanceof ModelCollection) {
				$localModel->each(function($model) use ($query) {
					try {
						$query->save($model);
					} catch (\PDOException $e) {}
				});
			}
		}
	}

}