<?php
namespace Users\Session;

use Users\User,
	Users\GuestUser;

class Session extends \System\Resource\AbstractComponent
{
	const RESOURCE_NAME = "user-session";
	
	/**
	 * @var int
	 */
	protected $userId = 0;
	
	/**
	 * @var int
	 */
	protected $created = 0;
	
	/**
	 * @var boolean
	 */
	protected $isValid = false;
	
	/**
	 * @var \Users\User
	 */
	protected $user = null;
	
	public function __construct()
	{
		$this->user = new GuestUser();
	}
	
	/**
	 * Set the session's ID
	 * 
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Get the session's ID
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Set the ID of the user the session belongs to
	 * 
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = (int) $userId;
	}
	
	/**
	 * Get the ID of the user the session belongs to
	 * 
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}
	
	/**
	 * Set the UNIX timestamp of when the session was created
	 * 
	 * @param int $created
	 */
	public function setCreated($created)
	{
		$this->created = (int) $created;
	}
	
	/**
	 * Get the UNIX timestamp of when the session was created
	 * 
	 * @return int
	 */
	public function getCreated()
	{
		return $this->created;
	}
	
	/**
	 * Set whether the session is valid
	 * 
	 * @param boolean $flag
	 */
	public function setIsValid($flag = true)
	{
		$this->isValid = (boolean) $flag;
	}
	
	/**
	 * Check if the session is valid
	 * 
	 * @return boolean
	 */
	public function getIsValid()
	{
		return $this->isValid;
	}
	
	/**
	 * Check if the session is valid
	 * 
	 * @return boolean
	 */
	public function isValid()
	{
		return $this->isValid;
	}
	
	/**
	 * Set the user instance the session belongs to
	 * 
	 * @param \Users\User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * Get the user instance the session belongs to
	 * 
	 * @return \Users\User
	 */
	public function getUser()
	{
		return isset($this->user) ? $this->user : new GuestUser();
	}
	
	/**
	 * Clear the session
	 * 
	 * @return void
	 */
	public function clear()
	{
		$this->user = null;
		$this->id = null;
		$this->userId = 0;
		$this->created = 0;
		$this->isValid = false;
	}
	
	/**
	 * Create a new session instance using an array of properties
	 * 
	 * @param array $properties
	 * @return \Users\Session\Session
	 */
	public static function factory(array $properties)
	{
		$session = new self();
		$session->populate($properties);
		
		return $session;
	}
	
	public function populate(array $properties) {
		if (isset($properties["user"])) {
			$properties["user"] = \Users\User::factory($properties["user"]);
		}
		
		return parent::populate($properties);
	}
}