<?php
namespace Assets\Renderer;

use Bliss\Component,
	Bliss\Response,
	Bliss\Request,
	Bliss\Cache\Container as CacheContainer,
	Assets\Source\Collection as SourceCollection;

abstract class AbstractRenderer extends Component
{
	/**
	 * @var string
	 */
	protected $path = null;

	/**
	 * @var boolean
	 */
	protected $httpCaching = false;

	/**
	 * @var boolean
	 */
	protected $fileCaching = false;

	/**
	 * @var int
	 */
	protected $version = 1;

	/**
	 * @var \Bliss\Cache\Container
	 */
	protected $cache;
	
	/**
	 * @var \Assets\Source\Collection
	 */
	protected $sources;

	/**
	 * Constructor
	 *
	 * @param string $path
	 * @param array|\Bliss\Config $config
	 */
	public function __construct($path = null, $config = null)
	{
		if (isset($config)) {
			$this->setConfig($config);
		}

		$this->path = $path;
		$this->init();
	}

	/**
	 * Set the cache container
	 *
	 * @param \Bliss\Cache\Container $cache
	 */
	public function setCache(CacheContainer $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * Initializer implemented by subclasses
	 */
	public function init()
	{}


	/**
	 * Set the path to render
	 *
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * Set the asset's version
	 *
	 * @param int $version
	 */
	public function setVersion($version)
	{
		$this->version = (int) $version;
	}

	/**
	 * Set whether the asset should have HTTP cache headers set
	 *
	 * @param boolean $flag
	 */
	public function setHttpCaching($flag = true)
	{
		$this->httpCaching = (boolean) $flag;
	}

	/**
	 *
	 * @param type $flag
	 */
	public function setFileCaching($flag = true)
	{
		$this->fileCaching = (boolean) $flag;
	}
	
	/**
	 * Set the source files to render
	 * 
	 * @param \Assets\Source\Collection $sources
	 */
	public function setSources(SourceCollection $sources)
	{
		$this->sources = $sources;
	}

	/**
	 * Operation performed before the render
	 */
	public function preRender()
	{}

	/**
	 * Render the contents of the file and set the response body
	 *
	 * @param \Bliss\Request $request
	 * @param \Bliss\Response $response
	 */
	public function render(Request $request, Response $response)
	{
		$this->preRender();

		$response->header("Content-Type: ". $this->getContentType());
		$response->setCode(200);

		$contents = $this->_getContents();
		$mtime = gmdate("D, j M Y G:i:s T", $this->getLastModified());
		$msince = $request->getHeader("IF_MODIFIED_SINCE", null);

		if ($this->httpCaching === true) {
			if ($mtime == $msince) {
				$response->setCode(304);
				$response->setBody(null);
				$response->output();
			} else {
				$cacheTime = isset($this->cache)
					? $this->cache->getMaxAge()
					: 0;

				if ($cacheTime < 0) {
					$expireTime = mktime(date("H"), date("i"), date("s"), date("m"), date("j"), date("Y")+1);
				} else {
					$expireTime = time() + $cacheTime;
				}

				$response->header("Expires: ". gmdate("D, d M Y H:i:s \G\M\T", $expireTime));
				$response->header("Last-Modified: ". $mtime);
				$response->header("Cache-Control: public");
				$response->header("Pragma: cache");
				$response->setBody($contents);
				$response->output();
			}
		} else {
			$response->setBody($contents);
			$response->output();
		}

	}

	/**
	 * Attempt to load the cached version of the asset
	 *
	 * @return string
	 */
	private function _getContents()
	{
		$cacheId = "assets:{$this->path}.{$this->version}";

		if ($this->fileCaching && isset($this->cache)) {
			$contents = $this->cache->load($cacheId);

			if ($contents !== false) {
				return $contents;
			}
		}

		$contents = $this->getContents();
		if ($this->fileCaching && isset($this->cache)) {
			$this->cache->save($cacheId, $contents);
		}

		return $contents;
	}

	/**
	 * Get the contents of the file
	 *
	 * @return string
	 */
	abstract public function getContents();

	/**
	 * Get the time the file was last modified
	 *
	 * @return int
	 */
	abstract public function getLastModified();

	/**
	 * Get the content type of the file
	 *
	 * @return string
	 */
	abstract public function getContentType();
}