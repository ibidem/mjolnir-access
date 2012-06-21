<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_RoleManager extends \app\Instantiatable
{
	function action_role_new()
	{
		$errors = array('\ibidem\access\backend\role-new' => []);
		
		if (\app\Layer_HTTP::request_method() === \ibidem\types\HTTP::POST)
		{
			if ($validator = \app\Model_DB_Role::factory($_POST))
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
	
	function action_role_update()
	{
		$errors = [];
		$role = $_POST['id'];
		$validator = \app\Model_DB_Role::update($role, $_POST);
		
		if ($validator !== null)
		{
			$errors = ['\ibidem\access\backend\role-update' => $validator->validate()];
			return $errors;
		}
		else # failed validation
		{
			\app\Layer_HTTP::redirect('\ibidem\backend', ['slug' => 'role-manager']);
		}
	}
		
	function action_role_delete()
	{
		\app\Model_DB_Role::delete([$_POST['id']]);
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
		
		\app\Model_DB_Role::delete($_POST['selected']);
		
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-roles'], 
				['tab' => 'access']
			);
	}
	
	function roles()
	{
		return \app\Model_DB_Role::entries(1, 9999);
	}
	
	function role($id)
	{
		return \app\Model_DB_Role::entry($id);
	}
	
	function roles_pager()
	{
		return \app\Pager::instance(\app\Model_DB_Role::count())
			->url_base(\app\Relay::route('\ibidem\backend')->url(['slug' => 'user-roles']));
	}

} # class
