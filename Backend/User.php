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
		return \app\Model_Role::entries(null, null);
	}
	
	function profile_info($id)
	{
		return \app\Model_Profile::profile_info($id);
	}
	
	function profile_fields()
	{
		return \app\Model_Profile::entries(null, null);
	}
	
	function action_update_profile()
	{
		$id = $_POST['id'];
		
		$validator = \app\Model_Profile::update_profile($id, $_POST);
		
		$errors = [];
		if ($validator !== null)
		{
			$errors = $validator->validate();
		}
		
		if (empty($errors))
		{
			\app\Layer_HTTP::redirect
				(
					'\ibidem\backend', 
					['slug' => $this->index]
				);
		}
		else # got errors
		{
			$key = \strtolower($this->model).'-update';
			$errors = [$key => $errors];
			
			return $errors;
		}
	}
	
} # class
