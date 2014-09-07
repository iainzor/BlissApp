<?php
require_once "core/Bliss/src/Autoloader.php";

define("BLISS_PATH", dirname(__FILE__));
define("APP_START_TIME", microtime(true));

error_reporting(E_ALL);
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
date_default_timezone_set("UTC");

/**
 * @staticvar \Bliss\Application\Container $instance
 * @return \Bliss\Application\Container
 */
function app() {
	static $instance = null;
	
	if (!isset($instance)) {
		$env = getenv("APP_ENV");
		if (!$env) {
			$env = "production";
		}
		
		$autoloader = new \Bliss\Autoloader();
		$autoloader->registerNamespace("Bliss", BLISS_PATH ."/core/Bliss/src");
		
		$instance = new \Bliss\Application\Container($autoloader);
		$autoloader->setApp($instance);
	}
	
	return $instance;
}

app()->registerModulesDirectory("Bliss/core");
app()->registerModulesDirectory("Bliss/utilities");

app()->setErrorHandler(
	app()->modules(\Error\Module::NAME)
);