<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_User extends \app\Model_Factory
{
	protected static $table = 'users';
	protected static $roles_table = 'roles';
	protected static $user_role_table = 'user_role';
	
	/**
	 * @return string 
	 */
	public static function roles_table()
	{
		return static::$roles_table;
	}
	
	/**
	 * @return string 
	 */
	public static function user_role_table()
	{
		return static::$user_role_table;
	}
	
	/**
	 * @param array fields
	 * @return Validator 
	 */
	public static function validator(array $fields) 
	{
		$user_config = \app\CFS::config('model/User');
		return \app\Validator::instance()
			->fields($fields)
			->rule('nickname', 'not_empty')
			->rule('nickname', 'max_length', array(':value', $user_config['fields']['nickname']['maxlength']))
			->rule('nickname', __NAMESPACE__.'\Model_User::valid_uniquenickname')
			->rule('password', 'not_empty')
			->rule('password', 'min_length', array(':value', $user_config['fields']['password']['minlength']));
	}	
	
	/**
	 * Validator helper.
	 * 
	 * @return boolean
	 */
	public static function valid_uniquenickname($nickname)
	{
		$first_row = \app\SQL::prepare
			(
				'ibidem/access::valid_uniquenickname', 
				'
					SELECT COUNT(*)
					  FROM '.static::$table.'
					 WHERE nickname = :nickname
					    -- '.__METHOD__.'
				', 
				'mysql'
			)
			->fetch_array();
		
		$count = $first_row['COUNT(*)'];
		
		if (\intval($count) != 0)
		{
			return false;
		}
		
		// test passed
		return true;
	}
	
	/**
	 * @param string user id 
	 */
	public static function dependencies($id)
	{
		$user_config = \app\CFS::config('model/User');
		
		foreach ($user_config['dependencies'] as $dependency)
		{
			$class = __NAMESPACE__.'\Model_'.$dependency;
			$class::inject($id);
		}

		\app\SQL::insert
			(
				static::user_role_table(), 
				array
				(
					'user' => $id,
					'role' => $user_config['signup']['role'],
				)
			);
	}

} # class

