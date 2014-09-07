<?php
namespace UI\View\Renderer;

class JsonRenderer extends FileRenderer
{
	/**
	 * Attempt to render the file path
	 * If the file does not exist, return a JSON encoded string
	 * 
	 * @param string $filePath
	 * @return string
	 */
	public function render($filePath = null) 
	{
		try {
			return parent::render($filePath);
		} catch (\Exception $e) {
			return json_encode($this->response->getParams());
		}
	}
}