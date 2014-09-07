<?php
namespace Users;

use Bliss\Hash\Blowfish as BlowfishHash,
	Bliss\String,
	System\Params\Parameter,
	System\Params\Collection as ParamCollection,
	System\Resource\AbstractComponent,
	Acl\Acl;

class User extends AbstractComponent
{
	const RESOURCE_NAME = "user";
	
	const ROLE_GUEST = "guest";
	const ROLE_ADMIN = "admin";
	const ROLE_USER = "user";
	
	/**
	 * @var int
	 */
	protected $id = 0;
	
	/**
	 * @var boolean
	 */
	protected $isActive = false;

	/**
	 * @var string
	 */
	protected $role = "guest";

	/**
	 * @var string
	 */
	protected $email = null;

	/**
	 * @var string
	 */
	protected $username = null;
	
	/**
	 * @var string
	 */
	protected $alias = null;

	/**
	 * @var string
	 */
	private $password = null;

	/**
	 * @var string
	 */
	private $salt = null;

	/**
	 * @var string
	 */
	protected $nickname = null;
	
	/**
	 * @var string
	 */
	protected $firstName = null;
	
	/**
	 * @var string
	 */
	protected $lastName = null;

	/**
	 * @var int
	 */
	protected $created = 0;

	/**
	 * @var int
	 */
	protected $updated = 0;
	
	/**
	 * @var \Acl\Acl
	 */
	protected $acl;

	/**
	 * @var \System\Params\Collection
	 */
	protected $params;

	/**
	 * @var \Bliss\Hash\HashInterface
	 */
	private static $_hasher;
	
	/**
	 * Constructor
	 *
	 * @param int $id
	 */
	public function __construct($id = 0)
	{
		$this->id = (int) $id;
		
		$this->params = new Params\Collection();
		$this->acl = Acl::factory(array(
			"allowByDefault" => false
		));
	}
	
	/**
	 * Set the user's ID
	 * 
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = (int) $id;
		$this->params->setResourceId($this->id);
	}
	
	/**
	 * Get the user's ID
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the user's role
	 *
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * Set the user's role
	 *
	 * @param string $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}

	/**
	 * Get the user's email address
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set the user's email address
	 *
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Get the user's hashed password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Set the user's hashed password
	 *
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * Set the value of the salt used when creating the user's password hash
	 *
	 * @param string
	 */
	public function setSalt($salt)
	{
		$this->salt = $salt;
	}

	/**
	 * Get the user's salt value
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Get the user's username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set the user's username
	 *
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		$this->alias = String::hyphenate($username);
	}

	/**
	 * Set the UNIX timestamp of when the user was created
	 *
	 * @var int
	 */
	public function setCreated($timestamp)
	{
		$this->created = (int) $timestamp;
	}

	/**
	 * Get the date of when the user was created
	 *
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}
	
	/**
	 * Set the UNIX timestamp of when the user was last updated
	 * 
	 * @param int $timestamp
	 */
	public function setUpdated($timestamp)
	{
		$this->updated = (int) $timestamp;
	}
	
	/**
	 * Get the UNIX timestamp of when the user was last updated
	 * 
	 * @return int
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * Set the ACL used to check permissions
	 *
	 * @param \Acl\Acl
	 */
	public function setAcl(Acl $acl)
	{
		$this->acl = $acl;
	}
	
	/**
	 * Get the user's ACL
	 * 
	 * @return \Acl\Acl
	 */
	public function getAcl()
	{
		return $this->acl;
	}

	/**
	 * Check if the user has a permission
	 *
	 * @param string $resourceName
	 * @param mixed $actions 
	 * @param array $params
	 * @return boolean
	 */
	public function isAllowed($resourceName, $actions, array $params = array())
	{
		return $this->acl->isAllowed($resourceName, $actions, $params);
	}

	/**
	 * Set the parameters for the user
	 * 
	 * @param \System\Params\Collection $params
	 */
	public function setParams(ParamCollection $params)
	{
		$params->setResourceId($this->id);
		$params->executeConditions();
		
		$this->params = $params;
	}
	
	/**
	 * Set a single parameter value for the user
	 * 
	 * @param string $name
	 * @param string $value
	 * @param boolean $isPrivate
	 */
	public function setParam($name, $value, $isPrivate = true)
	{
		$parameter = new Parameter();
		$parameter->setResourceName(self::RESOURCE_NAME);
		$parameter->setResourceId($this->id);
		$parameter->setName($name);
		$parameter->setValue($value);
		$parameter->setIsPrivate($isPrivate);
		
		$this->params->add($parameter);
	}
	
	/**
	 * Get the user's parameters
	 * 
	 * @return \System\Params\Collection
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Get a value from the user's meta data
	 *
	 * @param string $key
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = null)
	{
		$param = $this->params->get(self::RESOURCE_NAME, $this->id, $key);
		if (empty($param)) {
			return $defaultValue;
		} else {
			return $param->getValue();
		}
	}

	/**
	 * Set the user's nickname
	 *
	 * @param string $nickname
	 */
	public function setNickname($nickname)
	{
		$this->nickname = $nickname;
	}

	/**
	 * Get the user's nickname
	 *
	 * @return string
	 */
	public function getNickname()
	{
		return $this->nickname;
	}

	/**
	 * Set whether the user is active
	 *
	 * @param boolean $flag
	 */
	public function setIsActive($flag = true)
	{
		$this->isActive = (boolean) $flag;
	}
	
	/**
	 * Check if the user is active
	 * 
	 * @return boolean
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	/**
	 * Check if the user is active
	 *
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->isActive;
	}
	
	/**
	 * Set the user's first name
	 * 
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * Get the user's first name
	 * 
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}
	
	/**
	 * Set the user's last name
	 * 
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * Get the user's last name
	 * 
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}
	
	/**
	 * Check if a password matches the one stored in the user's instance
	 * 
	 * @param string $password
	 * @return boolean
	 */
	public function passwordIsValid($password)
	{
		return self::hasher()->isValid($password, $this->password);
	}
	
	/**
	 * Alter the user to remove any private information
	 */
	public function makePublic()
	{
		$this->email = null;
		$this->params->makePublic();
		$this->acl = null;
	}
	
	/**
	 * Get the resource path to the user
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return "users/{$this->id}-{$this->alias}";
	}
	
	/**
	 * Override the toArray method to add additional information
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$data = parent::toArray();
		$data["path"] = $this->getPath();
		
		return $data;
	}
	
	/**
	 * Convert the user to an array and remove sensitive data
	 * 
	 * @return array
	 */
	public function toSafeArray()
	{
		$data = $this->toArray();
		$toRemove = array(
			"role",
			"isActive",
			"username",
			"email"
		);
		$clean = [];
		foreach ($data as $key => $value) {
			if (!in_array($key, $toRemove)) {
				$clean[$key] = $value;
			}
		}
		return $clean;
	}
	
	/**
	 * Create a new user instance using a data array
	 * 
	 * @param array $data
	 * @return \Users\User
	 */
	public static function factory(array $data)
	{
		$user = new self();
		$user->populate($data);
		
		return $user;
	}
	
	public function populate(array $properties) {
		if (isset($properties["params"])) {
			$properties["params"] = Params\Collection::factory($properties["params"]);
		}
		
		if (isset($properties["acl"])) {
			$properties["acl"] = Acl::factory($properties["acl"]);
		}
		
		parent::populate($properties);
	}
	
	/**
	 * Get the hasher used to hash user passwords
	 * 
	 * @return \Bliss\Hash\HashInterface
	 */
	public static function hasher()
	{
		if (!isset(self::$_hasher)) {
			self::$_hasher = new BlowfishHash();
		}
		
		return self::$_hasher;
	}
}