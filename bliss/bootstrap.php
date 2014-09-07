<?php
define("ROOT_PATH", dirname(__FILE__));

if (!defined("APP_PATH")) {
	define("APP_PATH", ROOT_PATH);
}

$env = getenv("APP_ENV");
if (!$env) {
	$env = "production";
}
define("APP_ENV", $env);

require_once ROOT_PATH ."/lib/Bliss/Autoloader.php";

set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_PATH ."/lib");

$autoloader = Bliss\Autoloader::getInstance();
$autoloader->registerNamespace("Bliss", ROOT_PATH ."/lib/Bliss");
$autoloader->registerNamespace("PHPUnit", ROOT_PATH ."/lib/PHPUnit");
$autoloader->registerNamespace("PHP", ROOT_PATH ."/lib/PHP");
$autoloader->registerNamespace("dflydev", ROOT_PATH ."/lib/dflydev");