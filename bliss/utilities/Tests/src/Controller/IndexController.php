<?php
namespace Tests\Controller;

class IndexController extends \Bliss\Controller\MultiActionController
{
	public function init()
	{}
	
	public function indexAction()
	{
		$bootstrapPath = $this->module->resolvePath("bootstrap.php");
		$testPath = $this->module->resolvePath("src/UnitTest.php");
		$configPath = $this->module->resolvePath("data/test.config");
		$xmlPath = $this->module->resolvePath("data/test.xml");
		
		$this->_buildConfig($configPath, $xmlPath);
		
		$command = "phpunit --bootstrap {$bootstrapPath} -c {$xmlPath}";
		$output = shell_exec($command);
		
		echo "<pre>";
		echo $command ."\n\n";
		echo $output;
		echo "</pre>";
		exit;
	}
	
	private function _buildConfig($configPath, $xmlPath)
	{
		$modules = [];
		$xml = "<phpunit><testsuites>";
		foreach ($this->app()->modules() as $module) {
			$modules[] = [
				"name" => $module->getName(),
				"path" => $module->path(),
				"namespace" => $module->getNamespace()
			];
			$xml .=	"<testsuite name=\"{$module->getName()}\">" 
				 .	"<directory>{$module->resolvePath("src")}</directory>" 
				 .	"</testsuite>";
		}
		$xml .= "</testsuites></phpunit>";
		
		file_put_contents($configPath, serialize([
			"modules" => $modules
		]));
		file_put_contents($xmlPath, $xml);
	}
}