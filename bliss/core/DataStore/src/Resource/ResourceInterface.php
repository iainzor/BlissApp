<?php
namespace DataStore\Resource;

use DataStore\Model\Model;

interface ResourceInterface
{
	/**
	 * @param \DataStore\Model\Model
	 * @return \DataStore\Resource\Resource
	 */
	public function toResource(Model $model);
	
	/**
	 * @return \DataStore\Filter\FilterInterface
	 */
	public function createFilter();
	
	/**
	 * @return \DataStore\Query\Query
	 */
	public function createQuery();
	
}