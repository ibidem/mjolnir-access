<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Model
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_SecurityToken
{
	# last_inserted_id, table, push, update, update_check
	use \app\Trait_Model_Factory;
	# stash, statement, snatch, inserter, updater
	use \app\Trait_Model_Utilities;
	# entries, entry, find, find_entry, clear_entry_cache, delete, count, exists
	use \app\Trait_Model_Collection;
	
	/**
	 * @var string
	 */
	protected static $table = 'securitytokens';
	
	/**
	 * @var array
	 */
	protected static $field_format = ['expires' => 'datetime'];
	
	// ------------------------------------------------------------------------
	// Factory
	
	/**
	 * @return \app\Validator
	 */
	static function check(array $fields)
	{
		return \app\Validator::instance([], $fields)
			->ruleset('not_empty', ['purpose']);
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
				__METHOD__,
				'
					DELETE FROM :table
					 WHERE expires < NOW()
				'
			)
			->run();
		
		// clear cache
		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}
	
} # class
