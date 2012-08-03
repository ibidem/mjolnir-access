<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Schematic
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Schematic_Default_Ibidem_Access_Oauth extends \app\Schematic_Base
{
	function build()
	{
		// inject openid providers
		$providers = \app\CFS::config('ibidem/a12n')['signin'];
		foreach ($providers as $provider)
		{
			\app\Register::inject($provider['register'], 'off');
		}
	}

} # class
