<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Context
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Context_Access extends \app\Instantiatable
{
	function authorized_a12n_providers()
	{
		// get all supported providers
		$providers = \app\CFS::config('ibidem/a12n')['signin'];
		
		// filter to enabled providers
		$enabled_providers = [];
		foreach ($providers as $provider)
		{
			$key = $provider['register'];
			$switch = \app\Register::pull([$key]);
			if ($switch[$key] == 'on')
			{
				$enabled_providers[] = $provider;
			}
		}
		
		return $enabled_providers;
	}

} # class
