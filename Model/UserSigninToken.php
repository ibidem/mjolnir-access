<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_UserSigninToken
{
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Utilities;
	use \app\Trait_Model_Collection;

	/**
	 * @var string
	 */
	protected static $table = 'mjolnir__tokens_user_signin';

	/**
	 * @var array
	 */
	protected static $fieldformat = [];

	/**
	 * @var string
	 */
	protected static $unique_key = 'user';

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
				->str(':token', $token)
				->num(':user', $user)
				->run();
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
				->str(':token', $token)
				->num(':user', $user)
				->run();
		}

		static::clear_cache();
	}

	/**
	 * ...
	 */
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
			->num(':user', $user)
			->run();

		static::clear_cache();
	}

} # class
