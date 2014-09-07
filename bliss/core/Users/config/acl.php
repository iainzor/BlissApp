<?php
use Acl\Acl;

return array(
	"roles" => array(
		"guest" => array(
			"allowByDefault" => false,
			"defaultPermissions" => array(
				Acl::READ => true
			),
			"resources" => array(
				array(
					"name" => Users\Module::RESOURCE_NAME,
					"permissions" => array(
						array(
							"action" => Acl::READ,
							"params" => array(
								"controller" => "account",
								"action" => "*"
							),
							"isAllowed" => false
						),
						array(
							"action" => Acl::READ,
							"params" => array(
								"controller" => "account",
								"action" => "sign-in"
							),
							"isAllowed" => true
						),
						array(
							"action" => Acl::READ,
							"params" => array(
								"controller" => "account",
								"action" => "sign-up"
							),
							"isAllowed" => true
						)
					)
				)
			)
		),
		"user" => [
			"allowByDefault" => false,
			"defaultPermissions" => [
				Acl::READ => true
			],
			"resources" => [
				[
					"name" => Users\Module::RESOURCE_NAME,
					"permissions" => [
						[
							"action" => Acl::READ,
							"params" => [
								"controller" => "account",
								"action" => "sign-in"
							],
							"isAllowed" => false
						],
						[
							"action" => Acl::READ,
							"params" => [
								"controller" => "account",
								"action" => "sign-up"
							],
							"isAllowed" => false
						]
					]
				]
			]
		],
		"admin" => array(
			"allowByDefault" => true,
			"inherits" => array("user")
		)
	)
);