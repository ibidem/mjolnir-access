<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Backend
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Backend_User extends \app\Backend_Collection
{
	protected $model = 'User';
	protected $index = 'user-index';

	/**
	 * @return array
	 */
	function roles()
	{
		return \app\RoleLib::entries(null, null);
	}

	/**
	 * @return array
	 */
	function profile_info($id)
	{
		return \app\ProfileFieldLib::profile_info($id);
	}

	/**
	 * @return array
	 */
	function profile_fields()
	{
		return \app\ProfileFieldLib::entries(null, null);
	}

	/**
	 * @return array
	 */
	function action_update_profile()
	{
		$id = $_POST['id'];

		$validator = \app\ProfileFieldLib::update_profile($id, $_POST);

		$errors = [];
		if ($validator !== null)
		{
			$errors = $validator->validate();
		}

		if (empty($errors))
		{
			\app\Server::redirect
				(
					\app\URL::href
						(
							'mjolnir:backend.route',
							['slug' => 'user-profile'],
							['id' => $id]
						)
				);
		}
		else # got errors
		{
			return ['user-update-profile' => $errors];
		}
	}

} # class
