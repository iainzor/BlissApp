<?php
namespace Bliss;

class ArrayUtilities
{
	/**
	 * Check if an array is an associative array
	 * 
	 * @param array $array
	 * @return boolean
	 */
	public static function isAssociative(array $array)
	{
		$keys = array_keys($array);
		
		if (count($keys) && !is_numeric($keys[0])) {
			return true;
		} else {
			return false;
		}
	}
}