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
				\app\Layer_HTTP::redirect
					(
						'\ibidem\backend', 
						['slug' => 'user-manager'], 
						['tab' => 'access']
					);
				
				return null;
			}
		}
		
		return null;
	}
	
	function action_users_delete()
	{
		if ( ! isset($_POST['selected']))
		{
			$_POST['selected'] = [];
		}
		
		\app\Model_DB_User::mass_delete($_POST['selected']);
		
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-manager'], 
				['tab' => 'access']
			);
	}
	
	function action_user_delete()
	{
		\app\Model_DB_User::mass_delete([$_POST['id']]);
		
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-manager'], 
				['tab' => 'access']
			);
	}
	
	function action_user_edit()
	{
		
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
				\app\Layer_HTTP::redirect
					(
						'\ibidem\backend', 
						['slug' => 'user-roles'], 
						['tab' => 'access']
					);
				
				return null;
			}
		}
		
		return null;
	}
	
	function action_role_delete()
	{
		\app\Model_DB_User::mass_delete_roles([$_POST['id']]);
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-roles'], 
				['tab' => 'access']
			);
	}
	
	function action_roles_delete()
	{
		if ( ! isset($_POST['selected']))
		{
			$_POST['selected'] = [];
		}
		
		\app\Model_DB_User::mass_delete_roles($_POST['selected']);
		
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-roles'], 
				['tab' => 'access']
			);
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
