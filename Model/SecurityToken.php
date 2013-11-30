<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_SecurityToken
{
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Utilities;
	use \app\Trait_Model_Collection;

	/**
	 * @var string
	 */
	protected static $table = 'mjolnir__securitytokens';

	/**
	 * @var array
	 */
	protected static $fieldformat = ['expires' => 'datetime'];

	// ------------------------------------------------------------------------
	// Factory

	/**
	 * @return \mjolnir\types\Validator
	 */
	static function check(array $fields)
	{
		return \app\Validator::instance($fields)
			->rule('purpose', 'not-empty');
	}

	/**
	 * ...
	 */
	static function process(array $fields)
	{
		static::inserter($fields, ['purpose', 'token', 'expires'])->run();
		static::$last_inserted_id = \app\SQL::last_inserted_id();

		// reset related caches
		foreach (static::related_caches() as $related_cache)
		{
			\app\Stash::purge(\app\Stash::tags($related_cache[0], $related_cache[1]));
		}
	}

	// ------------------------------------------------------------------------
	// etc

	/**
	 * Remove all expired tokens.
	 */
	static function purge()
	{
		static::statement
			(
				'
					DELETE FROM [table]
					 WHERE expires < NOW()
				'
			)
			->run();

		// clear cache
		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

} # class
