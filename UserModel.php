<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class UserModel extends \app\MarionetteModel
{
	/**
	 * @var array
	 */
	static $configfile = 'mjolnir/models/user';

	/**
	 * @return string
	 */
	static function table()
	{
		return \app\UserLib::table();
	}

} # class
