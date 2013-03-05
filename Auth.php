<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, 2013 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Auth
{
	const Guest = 'mjolnir:access/guest.role';

	// ------------------------------------------------------------------------
	// Shorthand for User class

	/**
	 * @return int|null
	 */
	static function id()
	{
		return \app\User::instance()->id();
	}

	/**
	 * @return string
	 */
	static function role()
	{
		return \app\User::instance()->role();
	}

	/**
	 * @return array|null
	 */
	static function info()
	{
		return \app\User::instance()->info();
	}

	// ------------------------------------------------------------------------
	// Access Control

	/**
	 * Sign out current user.
	 */
	static function signout()
	{
//		\app\Model_UserSigninToken::purge(static::id());
		\app\Model_User::purgetoken(static::id());
		\app\Session::destroy();
		\app\Cookie::delete('user');
		\app\Cookie::delete('accesstoken');
	}

	/**
	 * ...
	 */
	static function signin($user, $role)
	{		
		// reset signin attempts
		\app\Model_User::reset_pwdattempts($user);
		\app\Model_User::update_last_singin($user);

		\app\Session::set('user', $user);
		\app\Session::set('role', $role);
	}

	/**
	 * Signs in user, or adds provider to current account.
	 *
	 * Email is used when associating user to account. This function has
	 * additional parameters for use when overwriting it.
	 */
	static function inferred_signin($identification, $email, $provider, $attributes = null)
	{
		// check if user exists
		$user = \app\Model_User::for_email($email);

		// handle logged in state
		if (static::role() !== static::Guest)
		{
			if ($user === static::id())
			{
				// we don't need to do anything since the email is already
				// linked to this user

				return;
			}

			\app\SQL::begin();
			if ($user !== null && $user !== static::id())
			{
				// close other account
				\app\Model_User::lock($user);
			}

			// add email to current user's secondary emails
			$errors = \app\Model_SecondaryEmail::push
				(
					[
						'email' => $email,
						'user' => static::id()
					]
				);

			if ($errors !== null)
			{
				\app\SQL::rollback();
				throw new \Exception('Failed to add secondary email ['.$email.'] to [user] '.static::id());
			}
			else # success
			{
				\app\SQL::commit();
				\app\Notice::make(\app\Lang::term('Your :provider account has been linked to your site account.', [':provider' => $provider]))
					->classes(['alert-info'])
					->save();
			}

			$user = \app\Model_User::entry(static::id())['id'];
		}

		// continue signin process
		if ($user !== null)
		{
			\app\Session::set('user', $user);
			\app\Session::set('role', \app\Model_User::role_for($user));
		}
		else # no user exists at the moment
		{
			// auto-signup user
			try
			{
				$default_role = \app\CFS::config('model/user')['signup']['role'];

				\app\Model_User::inferred_signup
					(
						[
							'identification' => $identification,
							'provider' => $provider,
							'email' => $email,
							'role' => $default_role,
						]
					);

				$user = \app\Model_User::last_inserted_id();

				\app\Session::set('user', $user);
				\app\Session::set('role', \app\Model_User::role_for($user));

				\app\Server::redirect(\app\Server::url_frontpage());
			}
			catch (\Exception $e)
			{
				throw new \app\Exception_NotApplicable('Failed automated signup process. Feel free to try again. Sorry for the inconvenience.');
			}
		}

		\app\Model_User::update_last_singin($user);
		return \app\Model_User::entry($user);
	}

} # class
