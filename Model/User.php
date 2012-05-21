<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_User extends \app\Model_Instantiatable
{
	const model = 'User';

	/**
	 * @return array (id, title) 
	 */
	public function user_roles()
	{
		$roles = \app\Model_DB_User::user_roles();
		
		foreach ($roles as & $role)
		{
			$role['title'] = \ucfirst(\app\Lang::tr($role['title']));
		}
		
		return $roles;
	}
	
	/**
	 * @return string 
	 */
	public function role_id()
	{
		return \app\Model_DB_User::user_role_id($this->get('id'));
	}

} # class
