<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_UserSigninToken extends \app\Instantiatable
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
	protected static $table = 'tokens_user_signin';
	
	/**
	 * @var array
	 */
	protected static $field_format = [];
	
	/**
	 * Inserts or updates token for user.
	 */
	static function refresh($user, $token)
	{
		if (static::exists($user, 'user'))
		{
			static::statement
				(
					__METHOD__.':update',
					'
						UPDATE :table
						   SET token = :token
						 WHERE user = :user
					'
				)
				->set(':token', $token)
				->set_int(':user', $user)
				->execute();
		}
		else # entry does not exist
		{
			static::statement
				(
					__METHOD__.':insert',
					'
						INSERT INTO :table (user, token)
							VALUES (:user, :token)
					'
				)
				->set(':token', $token)
				->set_int(':user', $user)
				->execute();
		}
	}
	
	static function purge($user)
	{
		static::statement
			(
				__METHOD__,
				'
					DELETE FROM :table
					 WHERE `user` = :user
				'
			)
			->set_int(':user', $user)
			->execute();
	}
	
} # class
