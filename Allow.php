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
	 * This method accepts both a single array of relays or list of parameters
	 * representing the array of relays.
	 *
	 * @return \mjolnir\types\Protocol
	 */
	static function relays(/* args... */)
	{
		$args = \func_get_args();

		if (\count($args) == 1 && \is_array($args[0]))
		{
			$relays = $args[0];
		}
		else # count != 1 || ! is_array(args[0])
		{
			$relays = $args;
		}

		return \app\Protocol::instance()
			->relays($relays)
			->is('Allow::relay Protocol');
	}

	/**
	 * This method accepts both a single array of attributes or list of
	 * parameters representing the array of attributes.
	 *
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
	  This method accepts both a single array of backend or list of
	 * parameters representing the array of backends.
	 *
	 * @return \mjolnir\types\Protocol
	 */
	static function backend(/* args... */)
	{
		$args = \func_get_args();

		if (\count($args) == 1 && \is_array($args[0]))
		{
			$relays = $args[0];
		}
		else # count != 1 || ! is_array(args[0])
		{
			$relays = $args;
		}

		return \app\Protocol::instance()
			->relays(['mjolnir:backend.route'])
			->attrs($args)
			->unrestricted()
			->is('Allow::backend Protocol');
	}

} # class
