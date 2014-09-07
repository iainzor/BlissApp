<?php
namespace Assets\Source;

interface SourceInterface
{
	public function getType();
	
	public function getExtension();
}