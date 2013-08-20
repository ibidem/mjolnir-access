<?php namespace mjolnir\access;

/**
 * If possible use the more readable \app\Auth equivalent of the methods here.
 *
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, 2013 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class User extends \app\Instantiatable
{
	/**
	 * @var int
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $role;

	/**
	 * @param mixed instance
	 */
	protected static function init($instance)
	{
		// check session
		$instance->user = \app\Session::get('user', null);
		$instance->role = \app\Session::get('role', \app\Auth::Guest);

		if ($instance->user === null)
		{
			// verify cookies
			$user = \app\Cookie::get('user', null);
			$token = \app\Cookie::get('accesstoken', null);

			if ($user !== null && $token !== null)
			{
				if (\app\Model_User::confirm_token($user, $token, 'mjolnir:access/remember-me'))
				{
					static::remember($user);
				}
			}
		}
	}

	/**
	 * @return static
	 */
	static function instance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = parent::instance();
			static::init($instance);
		}

		return $instance;
	}

	/**
	 * Store remember me information. Will also change the current user to the
	 * given user.
	 */
	static function remember($user)
	{
		$role = \app\Model_User::role_for($user);

		\app\Session::set('user', $user);
		\app\Session::set('role', $role);

		$instance = static::instance();
		$instance->user = $user;
		$instance->role = $role;

		// generate new token
		$token = \app\Model_User::token($user, '+1 month', 'mjolnir:access/remember-me');
		$timeout = \app\CFS::config('mjolnir/auth')['remember_me.timeout'];

		\app\Cookie::set('user', $user, $timeout);
		\app\Cookie::set('accesstoken', $token, $timeout);
	}

	/**
	 * @return int|null
	 */
	function id()
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	function role()
	{
		return $this->role;
	}

	/**
	 * @return array|null
	 */
	function info()
	{
		return \app\Model_User::entry($this->user);
	}

} # class
