<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_Access extends \app\Instantiatable
{
	function action_user_new()
	{
		$errors = array('\ibidem\access\backend\user-new' => []);
		
		if (\app\Layer_HTTP::request_method() === \ibidem\types\HTTP::POST)
		{
			if ($validator = \app\Model_DB_User::factory($_POST))
			{
				// got validator, so we failed
				$errors['\ibidem\access\backend\user-new'] = $validator->validate();
				return $errors;
			}
			else # success
			{
				return null;
			}
		}
		
		return null;
	}
	
	function action_role_new()
	{
		$errors = array('\ibidem\access\backend\role-new' => []);
		
		if (\app\Layer_HTTP::request_method() === \ibidem\types\HTTP::POST)
		{
			if ($validator = \app\Model_DB_User::build_role($_POST))
			{
				// got validator, so we failed
				$errors['\ibidem\access\backend\role-new'] = $validator->validate();
				return $errors;
			}
			else # success
			{
				return null;
			}
		}
		
		return null;
	}
	
	function users($page, $limit)
	{
		return \app\Model_DB_User::users($page, $limit);
	}
	
	function users_pager()
	{
		return \app\Pager::instance(\app\Model_DB_User::count());
	}

	function user_roles()
	{
		return \app\Model_DB_User::user_roles();
	}
	
	function roles()
	{
		return \app\Model_DB_User::user_roles();
	}
	
	function roles_pager()
	{
		return \app\Pager::instance(\app\Model_DB_User::roles_count());
	}
	
} # class
