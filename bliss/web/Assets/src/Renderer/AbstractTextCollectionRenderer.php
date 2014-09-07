<?php
namespace Assets\Renderer;

use Bliss\FileSystem\RecursiveFileCollection;

abstract class AbstractTextCollectionRenderer extends AbstractTextRenderer
{
	/**
	 * @var \Bliss\FileSystem\RecursiveFileCollection
	 */
	private $collection;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * Get the asset collection
	 *
	 * @return \Bliss\FileSystem\RecursiveFileCollection
	 */
	public function preRender()
	{
		if (!isset($this->extension)) {
			throw new \Exception("Extension has not been set!");
		}

		$this->collection = new RecursiveFileCollection();
		
		if (!empty($this->sources)) {
			foreach ($this->sources as $source) {
				/* @var $source \Assets\Source\AbstractSource */
				$path = $source->getRootPath();
				$file = $path .".{$this->extension}";
				$found = false;
				
				if (is_file($file)) {
					$this->collection->addItem($file);
					$found = true;
				}
				if (is_dir($path)) {
					$this->collection->collectFiles($path, $this->extension);
					$found = true;
				}
				
				if ($found === false) {
					throw new \Exception("Could not load source: {$path}");
				}
			}
		} else {
			if (is_file($this->path .".{$this->extension}")) {
				$this->collection->addItem($this->path .".{$this->extension}");
			}
			if (is_dir($this->path)) {
				$this->collection->collectFiles($this->path, $this->extension);
			}
		}

		return $this->collection;
	}

	/**
	 * Get the CSS contents
	 *
	 * @return string
	 */
	public function getContents()
	{
		$contents = $this->collection->compile(true);

		if ($this->compressing === true) {
			$contents = $this->compress($contents);
		}

		return $contents;
	}

	/**
	 * Get the timestamp of the last modified file
	 *
	 * @return int
	 */
	public function getLastModified()
	{
		return (int) $this->collection->getLastModified();
	}

	/**
	 * Compress a string
	 */
	abstract public function compress($string);
}