<?php
namespace Front;

class Query extends \DataStore\Query\Query implements \DataStore\Resource\ResourceInterface
{
	public function createFilter() {}
	
	public function createQuery() {
		return new self($this->getStorage());
	}

	public function toResource(\DataStore\Model\Model $model) {
		
	}

}