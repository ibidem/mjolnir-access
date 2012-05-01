<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class A12n extends \app\Instantiatable
	implements \ibidem\types\Auth
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
	 * @return \ibidem\access\A12n
	 */
	public static function instance()
	{
		static $instance = null;
		
		if ($instance === null)
		{
			$instance = parent::instance();
			// check session
			$instance->user = \app\Session::get('user', null);
			$instance->role = \app\Session::get('role', static::guest());
			// @todo encrypt, sign and timestamp session data
		}
		
		return $instance;
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
						'ibidem\access\a12n:current',
						'
							SELECT * 
							  FROM `'.\app\Model_HTTP_User::table().'`
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
		return '\ibidem\access\A12n::guest';
	}
	
	public static function signout()
	{
		 \app\Session::destroy();
	}
	
	public static function signin($user, $role)
	{
		\app\Session::set('user', $user);
		\app\Session::set('role', $role);
	}

} # class
