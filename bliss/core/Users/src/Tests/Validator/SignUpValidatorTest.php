<?php
namespace Users\Tests\Validator;

use Users\Validator\SignUpValidator,
	Users\Tests\MockStorage as UserStorage;

class SignUpValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testInvalidResults()
	{
		$validator = new SignUpValidator(new UserStorage());
		$results = $validator->run(array(
			"email" => "iainedminster@gmail.com"
		));
		
		$this->assertFalse($results->areValid());
	}
	
	public function testRequiredResults()
	{
		$validator = new SignUpValidator(new UserStorage());
		$results = $validator->run(array());
		
		$this->assertFalse($results->areValid());
	}
	
	public function testValidResults()
	{
		$validator = new SignUpValidator(new UserStorage());
		$results = $validator->run(array(
			"username" => "JohnDoe",
			"nickname" => "JohnDoe",
			"email" => "johndoe@gmail.com",
			"password" => "abc123",
			"passwordConfirm" => "abc123"
		));
		
		$this->assertTrue($results->areValid());
	}
}