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
	 * @return int 
	 */
	public function id()
	{
		// @todo \ibidem\access\A12n::id
		return 1234;
	}
	
	/**
	 * @return string 
	 */
	public function role()
	{
		// @todo \ibidem\access\A12n::role
		return 'member';
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

} # class
