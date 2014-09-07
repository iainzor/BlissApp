<?php
namespace DataStore\Filter;

use DataStore\Query\Query;

interface FilterInterface
{
	/**
	 * @param array $valuePairs
	 */
	public function setValues(array $valuePairs);
	
	/**
	 * @param \DataStore\Filter\Field\Collection $fields 
	 */
	public function setFields(Field\Collection $fields);
	
	/**
	 * @return \DataStore\Filter\Field\Collection
	 */
	public function getFields();
	
	/**
	 * @param \DataStore\Query\Query $query
	 */
	public function applyToQuery(Query $query);
}