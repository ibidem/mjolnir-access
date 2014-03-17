<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class UserCollection extends \app\MarionetteCollection
{
	/**
	 * @return array
	 */
	static function config()
	{
		return \app\UserModel::config();
	}

	/**
	 * @return string
	 */
	static function table()
	{
		return \app\UserLib::table();
	}

} # class
