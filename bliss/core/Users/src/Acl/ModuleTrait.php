<?php
namespace Users\Acl;

use Bliss\Module\AbstractModule,
	Acl\Builder\Container as AclBuilder;

trait ModuleTrait
{
	/**
	 * Add a module's acl configuration to an ACL builder
	 * 
	 * @param \Acl\Builder\Container $builder
	 * @param \Bliss\Module\AbstractModule $module
	 */
	public function applyUserAcl(AclBuilder $builder, AbstractModule $module)
	{
		$path = $module->resolvePath("config/acl.php");
		
		if (!is_file($path)) {
			throw new \Exception("Could not load ACL file: {$path}");
		}
		
		$builder->addSource(
			new \Acl\Builder\Source\FileArraySource($path)
		);
	}
}