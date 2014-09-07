<?php
namespace Assets\Source;

use Bliss\Module\AbstractModule;

trait ModuleProviderTrait
{
	public function registerAssetSources(Container $container, AbstractModule $module)
	{
		$dirs = [
			$module->resolvePath("assets/js")	=> "js",
			$module->resolvePath("assets/css")	=> "css"
		];
		
		foreach ($dirs as $dir => $ext) {
			if (!is_dir($dir)) {
				continue;
			}
			
			foreach (new \DirectoryIterator($dir) as $subDir) {
				if (!$subDir->isDot() && $subDir->isDir()) {
					$fileName = $subDir->getBaseName() .".". $ext;
					$className = "\\Assets\\Source\\". ucfirst($ext) ."Source";
					$path = $dir ."/". $fileName;
					
					$container->addSource(new $className($path));
				}
			}
		}
	}
}