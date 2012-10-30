<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class A12n extends \app\Instantiatable
{
	/**
	 * @var int
	 */
	private $user;
	
	/**
	 * @var string 
	 */
	private $role;
	
	/**
	 * @param mixed instance 
	 */
	protected static function init($instance)
	{
		// check session
		$instance->user = \app\Session::get('user', null);
		$instance->role = \app\Session::get('role', static::guest());
		
		if ($instance->user === null)
		{
			// verify cookies
			$user = \app\Cookie::get('user', null);
			$token = \app\Cookie::get('accesstoken', null);
			
			if ($user !== null && $token !== null)
			{
				$entry = \app\Model_UserSigninToken::find_entry(['user' => $user]);
				if ( ! empty($entry) && $entry['token'] === $token)
				{
					static::remember_user($user);
				}
			}
		}
	}
	
	/**
	 * Store remember me information.
	 */
	static function remember_user($user)
	{
		$role = \app\Model_User::role_for($user);

		\app\Session::set('user', $user);
		\app\Session::set('role', $role);

		$instance = static::instance();
		$instance->user = $user;
		$instance->role = $role;

		// generate and save new token
		$token = \sha1(\uniqid('user_tokens', true));

		\app\Model_UserSigninToken::refresh($user, $token);

		\app\Cookie::set('user', $user);
		\app\Cookie::set('accesstoken', $token);
	}
	
	/**
	 * @return \mjolnir\access\A12n
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
	
	function set_role($role)
	{
		$base_config = \app\CFS::config('mjolnir/base');
		
		// allow role manipulation in development for mockup purposes
		if (isset($base_config['development']) && $base_config['development'])
		{
			$this->role = $role;
		}
		else # access violation
		{
			throw new \app\Exception_NotAllowed
				('Role manipulation violation detected. Terminating.');
		}
	}
	
	/**
	 * @return int 
	 */
	function user()
	{
		return $this->user;
	}
	
	/**
	 * @return int 
	 */
	static function id()
	{
		return static::instance()->user();
	}
	
	/**
	 * @return string 
	 */
	function role()
	{		
		return $this->role;
	}
	
	/**
	 * @return array|null user information
	 */
	function current()
	{
		static $current = null;
		
		if ($this->user === null)
		{
			return null;
		}
		else # actual id provided
		{	
			if ($current === null)
			{
				$current = \app\SQL::prepare
					(
						__METHOD__,
						'
							SELECT * 
							  FROM `'.\app\Model_User::table().'`
							 WHERE id = :id
						',
						'mysql'
					)
					->set_int(':id', $this->user)
					->execute()
					->fetch_array();
			}
			
			return $current;
		}
	}
	
	/**
	 * Retrieves the role name for the abstraction notion of "everybody"
	 * 
	 * ie. unauthentificated (such as guests and otherwise)
	 * 
	 * @return string
	 */
	static function guest()
	{
		// unique identifier
		return '\mjolnir\access\A12n::guest';
	}
	
	/**
	 * Retrieves the role name for the abstraction notion of "everybody"
	 * 
	 * ie. unauthentificated by us
	 * 
	 * @return string
	 */
	static function oauth_guest()
	{
		// unique identifier
		return '\mjolnir\access\A12n::oauth_guest';
	}
	
	/**
	 * Sign out current user. 
	 */
	static function signout()
	{
		\app\Model_UserSigninToken::purge(static::instance()->user());
		\app\Session::destroy();
		\app\Cookie::delete('user');
		\app\Cookie::delete('accesstoken');
	}
	
	/**
	 * @param int user
	 * @param string role 
	 */
	static function signin($user, $role)
	{
		// reset signin attempts
		\app\Model_User::reset_pwdattempts($user);
		
		\app\Session::set('user', $user);
		\app\Session::set('role', $role);
	}
	
	/**
	 * @param 
	 */
	static function inferred_signin($identification, $email, $provider)
	{
		// check if user exists
		$user = \app\Model_User::for_email($email);
		if ($user !== null)
		{
			\app\Session::set('user', $user);
			\app\Session::set('role', \app\Model_User::role_for($user));
		}
		else
		{
			// does not exist; auto-register
			try
			{
				$default_role = \app\CFS::config('model/User')['signup']['role'];
				
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
				
				$base_config = \app\CFS::config('mjolnir/base');
				if (isset($base_config['site:frontend']))
				{
					\app\Server::redirect('//'.$base_config['domain'].$base_config['path'].$base_config['site:frontend']);
				}
				else # no frontend
				{
					// redirect to access page
					\app\Server::redirect(\app\CFS::config('mjolnir/access')['default.signin']);
				}
			}
			catch (\Exception $e)
			{
				throw new \app\Exception_NotApplicable('Failed automated signup process. Feel free to try again. Sorry for the inconvenience.');
			}
		}
		
	}

} # class
