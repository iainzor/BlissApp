<?php
namespace DataStore\Storage;

use DataStore\Query\Query,
	DataStore\Model\Model,
	DataStore\Model\Collection as ModelCollection;

interface StorageInterface
{
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * @return string
	 */
	public function getAlias();
	
	/**
	 * @return string
	 */
	public function getModelClass();
	
	/**
	 * @return string
	 */
	public function getCollectionClass();
	
	/**
	 * @return \DataStore\Query\Field\Collection
	 */
	public function getFields();
	
	/**
	 * @return int
	 */
	public function getLastInsertedId();
	
	/**
	 * @return int
	 */
	public function getFoundRows();
	
	
	public function insert(Model $record);
	
	public function insertAll(ModelCollection $records);
	
	public function update(Model $record);
	
	public function replace(Model $record);
	
	public function delete(Query $query);
	
	public function deleteAll(Query $query = null);
	
	public function find(Query $query = null);
	
	public function findAll(Query $query = null);
	
	public function count(Query $query = null);
}