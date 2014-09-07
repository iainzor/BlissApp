<?php
use Bliss\Router\Route\RegexRoute,
	UI\View\Format\JsonFormat,
	UI\View\Format\HtmlFormat;

include "app.php";

$baseUrl = preg_replace("/([a-z0-9-_]+)\.php$/i", "", filter_input(INPUT_SERVER, "SCRIPT_NAME"));
$uri = preg_replace("~^{$baseUrl}([^?]*)(.*)~i", "\\1", filter_input(INPUT_SERVER, "REQUEST_URI"));

$request = new \Bliss\Request\HttpRequest();
$request->setBaseUrl($baseUrl);
$request->setUri($uri);
$request->router()->addRoute(
	RegexRoute::factory("/^(.*)$/i", [
			"priority" => -1,
			"matches" => [
				1 => "routePath"
			],
			"defaults" => [
				"module" => "bliss",
				"controller" => "router",
				"action" => "route"
			]
		]
	)
);

$response = new \Bliss\Response\HttpResponse();

app()->setRequest($request);
app()->setResponse($response);

app()->registerModulesDirectory("Bliss/web");

$view = app()->view();
$view->registerFormat(new JsonFormat());
$view->registerFormat(new HtmlFormat());

return app();