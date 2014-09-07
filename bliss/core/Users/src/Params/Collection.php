<?php
namespace Users\Params;

use System\Params\Parameter;

class Collection extends \System\Params\Collection
{
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$this->registerCondition("gravatarEmail", array($this, "generateGravatarHash"));
	}
	
	/**
	 * Generate an MD5 hash to use for gravatars
	 * 
	 * @param \System\Params\Parameter $gravatarEmail
	 */
	public function generateGravatarHash(Parameter $gravatarEmail) 
	{
		$gravatarHash = clone $gravatarEmail;
		$gravatarHash->setName("gravatarHash");
		$gravatarHash->setValue(md5($gravatarEmail->getValue()));
		$gravatarHash->setIsPrivate(false);
		
		$this->add($gravatarHash);
	}
	
	/**
	 * Create a new user parameter collection from an array of pairs
	 * 
	 * @param array $params
	 * @param boolean $propertyArray
	 * @see \System\Params\Collection::factory()
	 * @return \Users\Params\Collection
	 */
	public static function factory(array $params, $propertyArray = false)
	{
		$baseParams = parent::factory($params, $propertyArray);
		$userParams = new self();
		foreach ($baseParams as $parameter) {
			$userParams->add($parameter);
		}
		$userParams->setResourceName(\Users\User::RESOURCE_NAME);
		
		return $userParams;
	}
}