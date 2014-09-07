<?php
return array(
	"params" => [
		\Error\Module::PARAM_SHOW_CONSOLE => true,
		\Error\Module::PARAM_SHOW_TRACE => true
	],
	"application" => array(
		"router" => array(
			"routes" => array(
				"error" => array(
					"className" => "\\Bliss\Router\\RegexRoute",
					"options" => array(
						"route" => "/^error\/?([0-9]{3})?\.?([a-z]+)?/",
						"matches" => array(
							1 => "action",
							2 => "format"
						),
						"prefixes" => array(
							"action" => "error"
						),
						"defaults" => array(
							"module" => "error",
							"controller" => "error",
							"action" => "error"
						)
					)
				)
			)
		)
	)
);