<?php
namespace Bliss\Response;

interface ResponseInterface
{
	public function setParam($name, $value);
	
	public function setParams(array $parameters);
	
	public function getParam($name, $defaultValue = null, $filter = FILTER_DEFAULT);
	
	public function getParams($filter = FILTER_DEFAULT);
	
	public function clearParams();
	
	public function setCode($code);
	
	public function setContent($content);
	
	public function getContent();
	
	public function setContentType($contentType);
	
	public function toString();
}