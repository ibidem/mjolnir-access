<?php namespace app;

// these functions can't be place in the framework since this entire
// folder has to be completely independent

\defined('APPVERSION') or \define('APPVERSION', '0.0');

// error reporting may duplicate the definition
if ( ! \function_exists('\app\index'))
{
	// we use an in-file function so ruby can process the file
	function index()
	{
		$args = \func_get_args();

		$result = [];
		foreach ($args as $array)
		{
			foreach ($array as $item)
			{
				if ( ! \in_array($item, $result))
				{
					$result[] = $item;
				}
			}
		}

		return $result;
	}
}