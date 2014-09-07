<?php
namespace DataStore\Model;

use DataStore\Query\Query;

interface ModelInterface
{
	public function getId();
	
	public function setQuery(Query $query);
	
	public function save();
	
	public function delete();
}