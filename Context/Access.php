<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Context
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Context_Access extends \app\Instantiatable
{
	/**
	 * @return array list of open authentication providers
	 */
	function authorized_a12n_providers()
	{
		// get all supported providers
		$providers = \app\CFS::config('mjolnir/auth')['signin'];

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

	/**
	 * Check if current user (ie. guest) can use signup feature.
	 *
	 * @return boolean
	 */
	function can_signup()
	{
		return \app\Access::can('mjolnir:access/auth.route', ['action' => 'signup'])
			&& \app\CFS::config('mjolnir/auth')['standard.signup']
			&& \app\Register::pull(['mjolnir:access/signup/public.reg'])['mjolnir:access/signup/public.reg'] === 'on';
	}

} # class
