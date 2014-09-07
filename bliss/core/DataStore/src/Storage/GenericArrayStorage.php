<?php
namespace DataStore\Storage;

class GenericArrayStorage extends AbstractArrayStorage
{
	public function getFields() { return null; }

	public function getFoundRows() { return 0; }

	public function getModelClass() { return null; }

	public function replace(\DataStore\Model\Model $record) {
		
	}

}