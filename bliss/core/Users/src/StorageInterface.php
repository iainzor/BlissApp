<?php
namespace Users;

interface StorageInterface extends \Bliss\Storage\StorageInterface
{
	/**
	 * @param string $username
	 * @return array
	 */
	public function loadByUsernameOrEmail($username);
	
	/**
	 * @param string $email
	 * @return boolean
	 */
	public function emailExists($email);
	
	/**
	 * @param string $username
	 * @return boolean
	 */
	public function usernameExists($username);
}