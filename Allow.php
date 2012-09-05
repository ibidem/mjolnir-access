<?php namespace ibidem\access;

/**
 * @package    ibidem
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
			->attributes($args);
	}
	
	/**
	 * @return \app\Protocol
	 */
	static function backend()
	{
		$args = \func_get_args();
		
		return \app\Protocol::instance()
			->relays(['\ibidem\backend'])
			->attributes($args);
	}

} # class
