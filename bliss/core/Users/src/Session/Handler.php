<?php
namespace Users\Session;

use Users\User,
	Users\Loader as UserLoader;

class Handler
{
	const SESSION_NAME = "user_session_id";
	
	/**
	 * @var \Users\Session\StorageInterface
	 */
	private $sessionStorage;
	
	/**
	 * @var \Users\Loader
	 */
	private $userLoader;
	
	/**
	 * @var \Users\Session\Session
	 */
	private $session;
	
	/**
	 * @var int
	 */
	private $sessionLifetime = 0;
	
	/**
	 * Constructor
	 * 
	 * @param \Users\Session\StorageInterface $sessionStorage
	 * @param \Users\Loader $userLoader
	 */
	public function __construct(StorageInterface $sessionStorage, UserLoader $userLoader)
	{
		$this->sessionStorage = $sessionStorage;
		$this->userLoader = $userLoader;
		$this->session = new Session();
	}
	
	/**
	 * Get the session instance
	 * 
	 * @return \Users\Session\Session
	 */
	public function getSession()
	{
		return $this->session;
	}
	
	/**
	 * Get the stored session ID
	 * 
	 * @return string|null 
	 */
	public function getSessionId()
	{
		$id = null;
		if (isset($_COOKIE[self::SESSION_NAME])) {
			$id = $_COOKIE[self::SESSION_NAME];
		} else if (isset($_SESSION[self::SESSION_NAME])) {
			$id = $_SESSION[self::SESSION_NAME];
		}
		return $id;
	}
	
	/**
	 * Set the number of seconds the session should stay valid for
	 * If this is anything greater than 0, a cookie will be set
	 * 
	 * @param int $seconds
	 */
	public function setSessionLifetime($seconds)
	{
		$this->sessionLifetime = (int) $seconds;
	}
	
	/**
	 * Attempt to log into a session
	 * 
	 * @param string $username
	 * @param string $password
	 * @throws \Users\Session\InvalidCredentialsException
	 * @return \Users\Session\Session
	 */
	public function login($username, $password)
	{
		$user = $this->userLoader->loadByUsernameOrEmail($username);
		
		if (!$user) {
			throw new InvalidUsernameException("Invalid username provided");
		}
		if (!$user->passwordIsValid($password)) {
			throw new InvalidPasswordException("Invalid password provided");
		}
		
		$this->session = $this->_generate($user);
		$saver = new Saver($this->sessionStorage);
		$saver->save($this->session);
		
		if ($this->sessionLifetime > 0) {
			setcookie(self::SESSION_NAME, $this->session->getId(), time()+$this->sessionLifetime, "/");
		} else {
			$_SESSION[self::SESSION_NAME] = $this->session->getId();
		}
		
		return $this->session;
	}
	
	/**
	 * Clear the session and log the user out
	 * 
	 * @return void
	 */
	public function logout()
	{
		$this->sessionStorage->delete(array(
			"params" => array(
				"id" => $this->session->getId()
			)
		));
		$this->session->clear();
		
		
		setcookie(self::SESSION_NAME, "", time()-3600, "/");
		unset($_SESSION[self::SESSION_NAME]);
	}
	
	/**
	 * Attempt to load a stored session
	 * 
	 * @throws \Users\Session\InvalidSessionIdException
	 * @return \User\Session\Session
	 */
	public function load()
	{
		$resources = \System\Module::resources();
		$loader = $resources->getLoader(Session::RESOURCE_NAME, []);
		$loader->setUserLoader($this->userLoader);
		$session = $loader->loadById(
			$this->getSessionId()
		);
		
		if (!$session) {
			throw new InvalidSessionIdException("Invalid session ID provided");
		}
		
		$this->session = $session;
		
		return $this->session;
	}
	
	
	/**
	 * Generate the session using a user instance
	 * 
	 * @param \Users\User $user
	 * @return \Users\Session\Session
	 */
	private function _generate(User $user)
	{
		$session = new Session();
		$session->setUser($user);
		$session->setId($this->_generateId());
		$session->setUserId($user->getId());
		$session->setCreated(time());
		$session->setIsValid(true);
		
		return $session;
	}
	
	/**
	 * Generate a new session ID
	 *
	 * @return string
	 */
	private function _generateId()
	{
		$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "127.0.0.1";
		$agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";

		return md5(uniqid($ip.$agent, true));
	}
}