<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_Settings extends \app\Instantiatable
{
	function action_update()
	{
		$signin_providers = \app\CFS::config('mjolnir/a12n')['signin'];
		$providers = \app\Collection::gather($signin_providers, 'register');

		$access_fields = array
			(
				'\mjolnir\access\signup\public', 
				'\mjolnir\access\signup\capcha',
			);
		
		$fields = \array_merge($providers, $access_fields); 
		
		$filtered = \app\Collection::filter
			(
				$_POST, # collection
				function ($key, $value) use ($fields) 
					{
						return \in_array($key, $fields);
					}
			);
		
		foreach ($filtered as $register => $value)
		{
			\app\Register::push($register, $value);
		}
	}

} # class
