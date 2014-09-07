<?php
namespace Bliss\Router\Route;

class RegexRoute extends AbstractRoute
{
	/**
	 * @var string
	 */
	private $expression;
	
	/**
	 * The names of each numeric match from the regular expression route starting at 1
	 * @var array
	 */
	private $matches = [];

	/**
	 * Prefix strings for any matches found formatted:
	 * [matched value] => [prefix]
	 *
	 * @var array [string matchedValue => string prefix]
	 */
	private $prefixes = [];
	
	/**
	 * @var array
	 */
	private $parameters = [];
	
	/**
	 * Constructor
	 * 
	 * @param string $expression The regular expression used to match against values
	 */
	public function __construct($expression)
	{
		$this->expression = $expression;
	}

	/**
	 * Set the matchs for the regex search
	 *
	 * @param array $matches
	 */
	public function setMatches(array $matches)
	{
		$this->matches = $matches;
	}

	/**
	 * Set the strings to append to the matches
	 *
	 * @param array $prefixes
	 */
	public function setPrefixes(array $prefixes)
	{
		$this->prefixes = $prefixes;
	}

	/**
	 * Check if route is valid
	 *
	 * @return boolean
	 */
	public function matches($value)
	{
		$isMatch = preg_match($this->expression, $value, $matches);
		
		$this->parameters = $this->_parse($matches);
		
		return $isMatch;
	}
	
	/**
	 * Parse the matches found from preg_match
	 * 
	 * @param array $matches
	 * @return array
	 */
	private function _parse(array $matches)
	{
		$parameters = [];
		
		foreach ($this->matches as $i => $name) {
			if (empty($matches[$i])) {
				continue;
			}

			$prefix = isset($this->prefixes[$name]) ? $this->prefixes[$name] : null;
			$parameters[$name] = $prefix . $matches[$i];
		}
		
		return $parameters;
	}

	/**
	 * Return all paramters found in the parsed value
	 * 
	 * @return array
	 */
	public function parameters()
	{
		return array_merge($this->defaults, $this->parameters);
	}
	
	/**
	 * Generate a new RegexRoute instance using a set of properties
	 * 
	 * @param string $expression
	 * @param array $properties
	 * @return \Bliss\Router\Route\RegexRoute
	 */
	public static function factory($expression, array $properties)
	{
		$route = new self($expression);
		$route->setProperties($properties);
		
		return $route;
	}
}