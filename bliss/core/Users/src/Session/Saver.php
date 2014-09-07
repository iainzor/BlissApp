<?php
namespace Users\Session;

class Saver
{
	/**
	 * @var \Users\Session\StorageInterface
	 */
	private $sessionStorage;
	
	/**
	 * Constructor
	 * 
	 * @param \Users\Session\StorageInterface $sessionStorage
	 */
	public function __construct(StorageInterface $sessionStorage)
	{
		$this->sessionStorage = $sessionStorage;
	}
	
	/**
	 * Save a session instance to the session storage
	 * 
	 * @param \Users\Session\Session $session
	 */
	public function save(Session $session)
	{
		$this->sessionStorage->save(array(
			"id" => $session->getId(),
			"userId" => $session->getUserId(),
			"created" => $session->getCreated(),
			"isValid" => $session->isValid()
		));
	}
}