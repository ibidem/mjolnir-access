<?php namespace mjolnir\access;

/**
 * Shorthand for A12n.
 * 
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Auth
{
	/**
	 * @return role string
	 */
	static function role()
	{
		return \app\A12n::instance()->role();
	}
	
	/**
	 * @return int user id
	 */
	static function id()
	{
		return \app\A12n::instance()->user();
	}

} # class
