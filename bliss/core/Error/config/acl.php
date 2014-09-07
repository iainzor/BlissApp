<?php
use Acl\Acl;

return array(
	"roles" => array(
		"*" => array(
			"resources" => array(
				array(
					"name" => \Error\Module::RESOURCE_NAME,
					"permissions" => array(
						array(
							"action" => Acl::READ,
							"isAllowed" => true
						)
					)
				)
			)
		)
	)
);