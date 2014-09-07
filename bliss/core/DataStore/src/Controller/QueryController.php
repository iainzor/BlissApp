<?php
namespace DataStore\Controller;

use DataStore\Resource\Registry;

class QueryController extends \Bliss\Controller\ActionController
{
	/**
	 * @var \DataStore\Resource\ResourceInterface
	 */
	private $query;
	
	public function init() 
	{
		$resourceName = $this->getParam("action");
		if (!$resourceName) {
			throw new \UnexpectedValueException("No resource name has been provided: /datastore/query/[resourceName].json");
		}
		$this->query = Registry::instance()->getQuery($resourceName);
	}

	public function execAction() 
	{
		$params = $this->request->getParams();
		$filter = $this->query->createFilter();
		$filter->setValues($params);
		$filter->applyToQuery($this->query);
		
		$results = $this->query->loadAll();
		
		$this->view->setAttributes([
			"results" => $results,
			"query" => $this->query,
			"filter" => $filter
		]);
	}

}