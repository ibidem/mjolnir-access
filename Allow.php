<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Allow
{
	/**
	 * @return \mjolnir\types\Protocol
	 */
	static function relays()
	{
		$args = \func_get_args();

		return \app\Protocol::instance()
			->relays($args)
			->is('Allow::relay Protocol');
	}

	/**
	 * @return \mjolnir\types\Protocol
	 */
	static function attrs($relay, array $args)
	{
		return \app\Protocol::instance()
			->relays([$relay])
			->attrs($args)
			->unrestricted()
			->is('Allow::attrs Protocol');
	}

	/**
	 * @return \mjolnir\types\Protocol
	 */
	static function backend()
	{
		$args = \func_get_args();

		return \app\Protocol::instance()
			->relays(['mjolnir:backend.route'])
			->attrs($args)
			->unrestricted()
			->is('Allow::backend Protocol');
	}

} # class
