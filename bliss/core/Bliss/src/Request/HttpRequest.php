<?php
namespace Bliss\Request;

class HttpRequest extends AbstractRequest
{
	/**
	 * @var \Bliss\Router\UriRouter
	 */
	private $router;
	
	/**
	 * @var string
	 */
	private $baseUrl = "/";
	
	/**
	 * @var string
	 */
	private $uri = "";
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->router = new \Bliss\Router\UriRouter();
	}
	
	/**
	 * Initialize the request
	 */
	public function init()
	{
		$params = $this->router->getParams($this->uri);
		$getParams = filter_input_array(INPUT_GET);
		$postParams = filter_input_array(INPUT_POST);
		
		if (!empty($getParams)) {
			$params = array_merge($params, $getParams);
		}
		
		if (!empty($postParams)) {
			$params = array_merge($params, $postParams);
		}
		
		$this->setParams($params);
	}
	
	/**
	 * Get the request's router instance
	 * 
	 * @return \Bliss\Router\UriRouter
	 */
	public function router() 
	{
		return $this->router;
	}
	
	/**
	 * Get the base URL of the request
	 * 
	 * @return string
	 */
	public function baseUrl() 
	{
		return $this->baseUrl;
	}

	/**
	 * Set the request's base URL
	 * 
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl) 
	{
		$this->baseUrl = $baseUrl;
	}

	/**
	 * Set the request's URI
	 * 
	 * @param string $uri
	 */
	public function setUri($uri) 
	{
		$this->uri = $uri;
	}
	
	/**
	 * Get the request's URI
	 * 
	 * @return string
	 */
	public function uri() 
	{
		return $this->uri;
	}
}