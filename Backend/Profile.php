<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_Profile extends \app\Backend_Collection
{
	protected $model = 'Profile';
	protected $index = 'user-profile-index';

	function fieldtypes()
	{
		return \app\Collection::mirror
			(
				\array_keys(\app\CFS::config('ibidem/profile-fieldtypes'))
			);
	}
	
	function update_profile()
	{
		$id = $_POST['id'];
		
		$validator = \app\Model_Profile::update($id, $_POST);
		
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
