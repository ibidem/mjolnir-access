<?php namespace ibidem\access;

/**
 * @package    ibidem
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
			$switch = \app\Register::pull([$provider['register']]);
			if ($switch == 'on')
			{
				$enabled_providers[] = $provider;
			}
		}
	}

} # class
