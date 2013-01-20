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
	use \app\Trait_Model_Collection
		{
			\app\Trait_Model_Collection::delete as protected collection_delete;
		}

	/**
	 * @var string
	 */
	protected static $table = 'user_secondaryemails';

	/**
	 * @var array
	 */
	protected static $fieldformat = [];

	// ------------------------------------------------------------------------
	// Factory

	/**
	 * @return \app\Validator
	 */
	static function check(array $fields, $context = null)
	{
		return \app\Validator::instance($fields)
			->test('email', \app\Email::valid($fields['email']))
			->test('email', static::unique_email($fields['email'], $fields['user']));
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

	// ------------------------------------------------------------------------
	// etc

	/**
	 * Remove all secondary emails for given user; used when loacking accounts.
	 */
	static function purge_for($user)
	{
		static::statement
			(
				__METHOD__,
				'
					DELETE FROM :table
					 WHERE user = :user
				'
			)
			->num(':user', $user)
			->run();

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

	// ------------------------------------------------------------------------
	// Validation Helpers

	/**
	 * @return boolean
	 */
	static function unique_email($email, $user)
	{
		$detected_user_id = \app\Model_User::for_email($email);

		if ($detected_user_id !== null)
		{
			return false;
		}

		$entry = static::find_entry(['email' => $email]);

		if ($entry !== null)
		{
			return false;
		}
		else # did not find entry
		{
			return true;
		}
	}

	/**
	 * ...
	 */
	static function delete(array $entries)
	{
		static::collection_delete($entries);
		\app\Stash::purge(\app\Stash::tags('\app\Model_User', ['change']));
	}

} # class
