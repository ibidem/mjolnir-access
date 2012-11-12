<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_SecondaryEmail
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
	protected static $table = 'user_secondaryemails';
	
	/**
	 * @var array
	 */
	protected static $field_format = [];
	
	// ------------------------------------------------------------------------
	// Factory
	
	/**
	 * @return \app\Validator
	 */
	static function check(array $fields, $context = null)
	{
		// @todo HIGH test if email belongs to user already
		// @todo HIGH test if email is not the current main email
		
		return \app\Validator::instance([], $fields)
			->test('email', ':valid', \app\Email::valid($fields['email']));
	}
	
	/**
	 * ...
	 */
	static function process(array $fields)
	{
		// @todo HIGH lock accounts if email is presetnt on another
		
		static::inserter($fields, ['email'], [], ['user'])->run();
		static::$last_inserted_id = \app\SQL::last_inserted_id();

		// reset related caches
		foreach (static::related_caches() as $related_cache)
		{
			\app\Stash::purge(\app\Stash::tags($related_cache[0], $related_cache[1]));
		}
	}

} # class
