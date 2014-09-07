<?php
return array(
	"application" => array(
		"router" => array(
			"routes" => array(
				"asset" => array(
					"options" => array(
						"route" => "/^assets\/(.*)$/i",
						"matches" => array(
							1 => "path",
						),
						"defaults" => array(
							"module" => "assets",
							"controller" => "render",
							"action" => "default"
						)
					)
				),
				"all-asset" => array(
					"options" => array(
						"route" => "/^assets\/all(\.([0-9]+))?\.([a-z]+)$/i",
						"matches" => array(
							2 => "version",
							3 => "type"
						),
						"defaults" => array(
							"module" => "assets",
							"controller" => "render",
							"action" => "all"
						)
					)
				),
				"module-asset" => array(
					"options" => array(
						"route" => "/^assets\/modules\/([a-z0-9-_]+)\/(.*)$/i",
						"matches" => array(
							1 => "moduleName",
							2 => "path"
						),
						"defaults" => array(
							"module" => "assets",
							"controller" => "render",
							"action" => "module"
						)
					)
				)
			)
		)
	)
);