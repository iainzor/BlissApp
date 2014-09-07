<?php
return array(
	"sessionLifetime" => 0,
	"application" => array(
		"router" => array(
			"routes" => array(
				"account" => array(
					"options" => array(
						"route" => "/^account\/?([0-9a-z\-]+)?(\.([0-9a-z]+))?/i",
						"matches" => array(
							1 => "action",
							3 => "format"
						),
						"defaults" => array(
							"module" => "users",
							"controller" => "account",
							"action" => "index"
						)
					)
				),
				"users" => array(
					"options" => array(
						"route" => "/^users\/?([0-9a-z\-]+)?(\.([0-9a-z]+))?/i",
						"matches" => array(
							1 => "action",
							3 => "format"
						),
						"defaults" => array(
							"module" => "users",
							"controller" => "users",
							"action" => "index"
						)
					)
				),
				"user" => array(
					"options" => array(
						"route" => "/^users\/([0-9]+)(\-([a-z0-9\-]+))?\/?([0-9a-z\-]+)?(\.([0-9a-z]+))?/i",
						"matches" => array(
							1 => "id",
							3 => "alias",
							4 => "action",
							6 => "format"
						),
						"defaults" => array(
							"module" => "users",
							"controller" => "user",
							"action" => "index"
						)
					)
				),
				"user-view" => array(
					"options" => array(
						"route" => "/^user\/([a-z0-9\-]+)\.html/i",
						"matches" => array(
							1 => "action"
						),
						"defaults" => array(
							"module" => "users",
							"controller" => "user",
							"format" => "html"
						)
					)
				),
				"user-partial" => array(
					"options" => array(
						"route" => "/^user\/(partials\/[a-z0-9\-]+)\.html/i",
						"matches" => array(
							1 => "action"
						),
						"defaults" => array(
							"module" => "users",
							"controller" => "user",
							"format" => "html"
						)
					)
				)
			)
		)
	)
);