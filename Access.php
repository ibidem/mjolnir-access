<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
final class Access
{
	/**
	 * @var array
	 */
	private static $whitelist;

	/**
	 * @var array
	 */
	private static $blacklist;

	/**
	 * @var array
	 */
	private static $aliaslist;

	/**
	 * @param array config
	 */
	static function protocols(array $config)
	{
		self::$whitelist = $config['whitelist'];
		self::$blacklist = $config['blacklist'];
		self::$aliaslist = $config['aliaslist'];
	}

	/**
	 * @param array permissions
	 * @param string relay
	 * @param array context information (action, etc)
	 * @param string attribute associated to the relay
	 * @return boolean
	 */
	private static function match_check(array $permissions, $relay, $context, $attribute)
	{
		if (isset($context['owner']))
		{
			// if we need owner computations we store the user
			$user = \app\A12n::instance()->user();
		}

		// check if no exception exists
		foreach ($permissions as $permission)
		{
			// check permission
			if ($permission->matches($relay, $context, $attribute))
			{
				// check self inference
				// null means it doens't require self nor require !self
				if ($permission->get_self() !== null)
				{
					// if we didn't get an owner parameter we deny access
					if ( ! isset($context['owner']) || $context['owner'] == null)
					{
						// NOTE: there are objects that have NULL owner, it
						// means they were submitted anoynmously (usually) so
						// because there is no user access of this kind on them
						// makes no sense and only lead to attack vectors
						continue;
					}

					// permission only in effect if user is owner of object
					if ($permission->get_self() == true)
					{
						// route must be object belonging to owner
						if ($user == $context['owner'])
						{
							// matched
							return true;
						}
					}
					// permission only in effect if user is NOT owner of object
					elseif ($permission->get_self() == false)
					{
						// route must be object NOT belonging to owner
						if ($user != $context['owner'])
						{
							// matched
							return true;
						}
					}
				}
				else # self is NULL, no further checks required
				{
					// matched
					return true;
				}
			}
		}

		// failed match
		return false;
	}

	/**
	 * @param string relay
	 * @param array context information (action, etc)
	 * @param string attribute associated to the relay
	 * @return boolean
	 */
	static function can($relay, array $context = null, $attribute = null, $user_role = null)
	{
		// get role of current user
		$user_role = $user_role !== null ? $user_role : \app\A12n::instance()->role();

		// initial status
		$status = false; # unauthorized

		if (isset(self::$whitelist[$user_role]))
		{
			// attempt to authorize
			$status = self::match_check(self::$whitelist[$user_role], $relay, $context, $attribute);
		}

		// failed authorization? check aliases for addition rules
		if ( ! $status && isset(self::$aliaslist[$user_role]))
		{
			foreach (self::$aliaslist[$user_role] as $alias)
			{
				if (isset(self::$whitelist[$alias]) && self::match_check(self::$whitelist[$alias], $relay, $context, $attribute))
				{
					$status = true; # authorized
					break;
				}
			}
		}

		// authorized? confirm blacklist
		if ($status && isset(self::$blacklist[$user_role]) && self::match_check(self::$blacklist[$user_role], $relay, $context, $attribute))
		{
			$status = false; # cancel authorization
		}

		return $status;
	}

} # class
