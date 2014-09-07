<?php
namespace Assets\Renderer;

use Bliss\FileSystem\File;

class FileRenderer extends AbstractRenderer
{
	/**
	 * Get the content type of the file
	 *
	 * @return string
	 */
	public function getContentType()
	{
		$ext = pathinfo($this->path, PATHINFO_EXTENSION);
		return File::getMimeType($ext);
	}

	/**
	 * Get the contents of the file
	 *
	 * @return string
	 */
	public function getContents()
	{
		if (!is_file($this->path)) {
			throw new \Exception("File not found: {$this->path}", 500);
		}

		return file_get_contents($this->path);
	}

	/**
	 * Get the time the file was last modified
	 *
	 * @return int
	 */
	public function getLastModified()
	{
		return filectime($this->path);
	}

}