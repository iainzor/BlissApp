<?php
namespace DataStore\Resource;

class Resource extends \DataStore\Model\Model
{
	/**
	 * @var string
	 */
	protected $title;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $path;
	
	/**
	 * Constructor
	 * 
	 * @param int $id
	 */
	public function __construct($id) 
	{
		$this->setId($id);
	}
	
	/**
	 * Get the title of the resource
	 * 
	 * @return string
	 */
	public function getTitle() 
	{
		return $this->title;
	}
	
	/**
	 * Set the title of the resource
	 * 
	 * @param string $title
	 */
	public function setTitle($title) 
	{
		$this->title = $title;
	}

	/**
	 * Get the description of the resource
	 * 
	 * @return string
	 */
	public function getDescription() 
	{
		return $this->description;
	}
	
	/**
	 * Set the description of the resource
	 * 
	 * @param string $description
	 */
	public function setDescription($description) 
	{
		$this->description = $description;
	}

	/**
	 * Get the path to the resource
	 * 
	 * @return string
	 */
	public function getPath() 
	{
		return $this->path;
	}

	/**
	 * Set the path to the resource
	 * 
	 * @param string $path
	 */
	public function setPath($path) 
	{
		$this->path = $path;
	}
}