<?php
namespace DataStore\Query\Relation;

interface RelationInterface
{
	/**
	 * @return stringdddd
	 */
	public function getName();
	
	/**
	 * @return \DataStore\Query\Query
	 */
	public function getQuery();
	
	/**
	 * @return array
	 */
	public function getForeignKey();
	
	/**
	 * @return array
	 */
	public function getLocalKey();
	
	public function disable();
	
	public function enable();
	
	/**
	 * @return boolean
	 */
	public function isEnabled();
}