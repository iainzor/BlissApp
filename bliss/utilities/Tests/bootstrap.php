<?php
global $argv;
if (isset($argv[4])) {
	putenv("APP_ENV={$argv[4]}");
} else {
	putenv("APP_ENV=development");
}

$root = realpath(__DIR__."/../../");

require_once $root ."/app.php";
//require_once $root ."/bootstrap.php";


/*
$autoloader = \Bliss\Autoloader::getInstance();
$autoloader->registerNamespace("Bliss", $root ."/lib/Bliss");
$autoloader->registerNamespace("PHPUnit", $root ."/lib/PHPUnit");

$configPath = realpath(__DIR__ ."/data/test.config");
$config = unserialize(file_get_contents($configPath));
foreach ($config["modules"] as $module) {
	$autoloader->registerNamespace($module["namespace"], $module["path"] ."/src");
}

/*
// Add modules to the autoloader
$di = new DirectoryIterator($root ."/app/modules");
foreach ($di as $dir) {
	if ($dir->isDir() && substr($dir->getBasename(), 0, 1) !== ".") {
		$autoloader->registerNamespace(
			$dir->getBasename(),
			$dir->getPathname() ."/src"
		);
	}
	unset($dir);
}
unset($di);
*/