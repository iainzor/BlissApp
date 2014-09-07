<?php
namespace Users\Validator;

use Users\StorageInterface as UserStorage,
	Bliss\Validate\Validator;

class SignUpValidator extends \Bliss\Validate\Validator
{
	/**
	 * Constructor
	 * 
	 * @param \Users\StorageInterface $userStorage
	 */
	public function __construct(UserStorage $userStorage) 
	{
		$this->add(["email", "username", "password", "passwordConfirm", "nickname"], "NotEmpty", "This field is required");
		
		$this->add("email", [
			new Validator\EmailAddress(),
			new Validator\Closure(function($value) use ($userStorage) {
				return !$userStorage->emailExists($value);
			})
		], [
			"Invalid email address", 
			"That email address is already being used"
		]);
		
		$this->add("username", new Validator\Closure(function($value) use ($userStorage) {
			return !$userStorage->usernameExists($value);
		}), "That username is already being used");
		
		$this->add("password", new Validator\Closure(array($this, "checkPassword")), "Password and confirmation do not match");
	}
	
	/**
	 * Check if the password and confirmation password match
	 * 
	 * @param string $password
	 * @return boolean
	 */
	public function checkPassword($password) 
	{
		$confirmation = $this->getField("passwordConfirm")->getValue();
		return $confirmation === $password;
	}
}