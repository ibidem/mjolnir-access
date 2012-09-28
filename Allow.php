<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Allow
{
	/**
	 * @return \app\Protocol
	 */
	static function relays()
	{
		$args = \func_get_args();
		
		return \app\Protocol::instance()->relays($args);
	}
	
	/**
	 * @return \app\Protocol
	 */
	static function attributes($relay, array $args)
	{
		return \app\Protocol::instance()
			->relays([$relay])
			->attributes($args)
			->all_parameters();
	}
	
	/**
	 * @return \app\Protocol
	 */
	static function backend()
	{
		$args = \func_get_args();
		
		return \app\Protocol::instance()
			->relays(['\mjolnir\backend'])
			->attributes($args)
			->all_parameters();
	}

} # class
