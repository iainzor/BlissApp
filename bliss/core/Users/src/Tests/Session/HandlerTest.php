<?php
namespace Users\Tests\Session;

use Users\User,
	Users\Collection as UserCollection,
	Users\Loader as UserLoader,
	Users\Tests\MockStorage as UserStorage,
	Users\Session\Session,
	Users\Session\Collection as SessionCollection,
	Users\Session\Handler as SessionHandler,
	Users\Session\Loader as SessionLoader,
	Users\Session\InvalidCredentialsException;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$resources = app()->resources();
		$resources->register(User::RESOURCE_NAME, [
			"component" => new User(),
			"collection" => new UserCollection(),
			"loader" => new UserLoader(new UserStorage())
		]);
		$resources->register(Session::RESOURCE_NAME, [
			"component" => new Session(),
			"collection" => new SessionCollection(),
			"loader" => new SessionLoader(new MockStorage())
		]);
	}
	/**
	 * @return \Users\Session\Handler
	 */
	private static function createHandler()
	{
		$resources = \System\Module::resources();
		$userLoader = $resources->getLoader(User::RESOURCE_NAME); //new UserLoader(new UserStorage());
		
		return new SessionHandler(new MockStorage(), $userLoader);
	}
	
	public function testInitialConditions()
	{
		$handler = self::createHandler();
		$this->assertFalse($handler->getSession()->isValid());
	}
	
	public function testSuccessfulLogin()
	{
		$handler = self::createHandler();
		$session = $handler->login("iainedminster@gmail.com", "abc123");
		
		$this->assertInstanceOf("\\Users\Session\\Session", $session);
		$this->assertTrue($session->isValid());
	}
	
	public function testInvalidCredentials()
	{
		$handler = self::createHandler();
		$success = true;
		try {
			$handler->login("iainedminster@gmail.com", "123abc");
		} catch (InvalidCredentialsException $e) {
			$success = false;
		}
		
		$this->assertFalse($success);
		$this->assertFalse($handler->getSession()->isValid());
	}
	
	public function testLoginAndSessionLoading()
	{
		$handler = self::createHandler();
		$handler->login("iainedminster@gmail.com", "abc123");
		$newSession = $handler->load();
		
		$this->assertTrue($newSession->isValid());
		$this->assertEquals("iainedminster@gmail.com", $newSession->getUser()->getEmail());
	}
	
	public function testClearSession()
	{
		$handler = self::createHandler();
		$session = $handler->login("iainedminster@gmail.com", "abc123");
		
		$this->assertTrue($session->isValid());
		
		$session->clear();
		
		$this->assertFalse($session->isValid());
	}
}