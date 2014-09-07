<?php
namespace Assets\Renderer;

abstract class AbstractTextRenderer extends AbstractRenderer
{
	/**
	 * @var boolean
	 */
	protected $compressing = false;

	/**
	 * Set whether the renderer should compress its contents
	 *
	 * @param boolean $flag
	 */
	public function setCompressing($flag = true)
	{
		$this->compressing = (boolean) $flag;
	}
}