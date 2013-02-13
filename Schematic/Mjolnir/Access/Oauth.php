<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Schematic_Mjolnir_Access_Oauth extends \app\Instantiatable implements \mjolnir\types\Schematic
{
	use \app\Trait_Schematic;

	/**
	 * ...
	 */
	function build()
	{
		// inject openid providers
		$providers = \app\CFS::config('mjolnir/auth')['signin'];
		foreach ($providers as $provider)
		{
			\app\Register::inject($provider['register'], 'off');
		}
	}

} # class
