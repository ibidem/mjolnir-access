<?php namespace ibidem\access;

/**
 * Shorthand for A12n.
 * 
 * @package    ibidem
 * @category   Access
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Auth
{
	static function role()
	{
		return \app\A12n::instance()->role();
	}
	
	static function id()
	{
		return \app\A12n::instance()->user();
	}

} # class
