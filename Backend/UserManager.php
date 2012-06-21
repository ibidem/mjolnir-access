<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_UserManager extends \app\Instantiatable
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
		
		\app\Model_DB_User::delete($_POST['selected']);
		
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-manager'], 
				['tab' => 'access']
			);
	}
	
	function action_user_delete()
	{
		\app\Model_DB_User::delete([$_POST['id']]);
		
		\app\Layer_HTTP::redirect
			(
				'\ibidem\backend', 
				['slug' => 'user-manager'], 
				['tab' => 'access']
			);
	}
	
	function action_user_update()
	{
		$errors = [];
		$user = $_POST['id'];
		$validator = \app\Model_DB_User::update($user, $_POST);
		
		if ($validator !== null)
		{
			$errors = $validator->validate();
		}
		
		if ( ! empty($_POST['password']))
		{
			$password_validator = \app\Model_DB_User::change_password($user, $_POST);
			
			if ($password_validator !== null)
			{
				$errors = $password_validator->validate(); // \array_merge($errors, $password_validator->validate());
			}
		}
		
		if (empty($errors))
		{
			\app\Layer_HTTP::redirect('\ibidem\backend', ['slug' => 'user-manager']);
		}
		else # got errors
		{
			$errors = ['\ibidem\access\backend\user-update' => $errors];
			
			return $errors;
		}
	}
	
	function users($page, $limit)
	{
		return \app\Model_DB_User::entries($page, $limit);
	}
	
		
	function user($id)
	{
		return \app\Model_DB_User::entry($id);
	}
	
	function users_pager()
	{
		return \app\Pager::instance(\app\Model_DB_User::count())
			->url_base(\app\Relay::route('\ibidem\backend')->url(['slug' => 'user-manager']));
	}
	
	function user_roles()
	{
		return \app\Model_DB_Role::entries(1, 9999);
	}

} # class
