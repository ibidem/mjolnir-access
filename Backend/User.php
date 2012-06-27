<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_User extends \app\Backend_Collection
{
	protected $model = 'User';
	protected $index = 'user-index';
	
	function roles()
	{
		return \app\Model_DB_Role::entries(null, null);
	}

} # class
