<?php namespace ibidem\access;

/**
 * @package    ibidem
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
		// @todo encrypt, sign and timestamp session data
	}
	
	/**
	 * @return \ibidem\access\A12n
	 */
	public static function instance()
	{
		static $instance = null;
		
		if ($instance === null)
		{
			$instance = parent::instance();
			static::init($instance);
		}
		
		return $instance;
	}
	
	public function set_role($role)
	{
		// allow role manipulation in development for mockup purposes
		if (\defined('DEVELOPMENT') && DEVELOPMENT)
		{
			$this->role = $role;
		}
		else # access violation
		{
			throw new \app\Exception_NotAllowed
				('Security role manipulation violation.');
		}
	}
	
	/**
	 * @return int 
	 */
	public function user()
	{
		return $this->user;
	}
	
	/**
	 * @return string 
	 */
	public function role()
	{		
		return $this->role;
	}
	
	/**
	 * @return array|null user information
	 */
	public function current()
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
	public static function guest()
	{
		// unique identifier
		return '\ibidem\access\A12n::guest';
	}
	
	/**
	 * Retrieves the role name for the abstraction notion of "everybody"
	 * 
	 * ie. unauthentificated by us
	 * 
	 * @return string
	 */
	public static function oauth_guest()
	{
		// unique identifier
		return '\ibidem\access\A12n::oauth_guest';
	}
	
	/**
	 * Sign out current user. 
	 */
	public static function signout()
	{
		 \app\Session::destroy();
	}
	
	/**
	 * @param int user
	 * @param string role 
	 */
	public static function signin($user, $role)
	{
		\app\Session::set('user', $user);
		\app\Session::set('role', $role);
	}
	
	/**
	 * @param 
	 */
	public static function inferred_signin($identification, $email, $provider)
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
				
				$base_config = \app\CFS::config('ibidem/base');
				if (isset($base_config['site:frontend']))
				{
					\app\Layer_HTTP::redirect_to_url('//'.$base_config['domain'].$base_config['path'].$base_config['frontend']);
				}
				else # no frontend
				{
					// redirect to access page
					\app\Layer_HTTP::redirect(\app\Relay::route('\ibidem\access')->url());
				}
			}
			catch (\Exception $e)
			{
				throw $e;
				throw new \app\Exception_NotApplicable('Failed automated signup process.');
			}
		}
		
	}

} # class
