<?php
namespace PaperUI;

use UI\View\Renderer\RendererInterface;

class Module extends \WebUI\Module
{
	const NAME = "paper-ui";
	
	public function getName() { return self::NAME; }

	public function init() 
	{}
	
	public function postInit()
	{
		$uiModule = $this->modules("ui");
		$uiModule->setUi($this);
	}
	
	public function wrap(RendererInterface $renderer) 
	{
		$pre = '<style type="text/css">body { font-family: arial; }</style>';
		$content = $pre ."\n\n". parent::wrap($renderer);
		
		return $content;
	}
}