<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_DB_User extends \app\Model_Factory
{
	protected static $table = 'users';
	protected static $roles_table = 'roles';
	protected static $user_role_table = 'user_role';	
	
	/**
	 * @return string 
	 */
	public static function table()
	{
		$database_config = \app\CFS::config('ibidem\database');
		return $database_config['table_prefix'].static::$table;
	}
	
	/**
	 * @return string 
	 */
	public static function roles_table()
	{
		$database_config = \app\CFS::config('ibidem\database');
		return $database_config['table_prefix'].static::$roles_table;
	}
	
	/**
	 * @return string 
	 */
	public static function user_role_table()
	{
		$database_config = \app\CFS::config('ibidem\database');
		return $database_config['table_prefix'].static::$user_role_table;
	}
	
	/**
	 * @param array fields
	 * @return Validator 
	 */
	public static function validator(array $fields) 
	{
		$user_config = \app\CFS::config('model/User');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('nickname', 'not_empty')
			->rule('nickname', 'max_length', $user_config['fields']['nickname']['maxlength'])
			->rule('nickname', '\app\Model_HTTP_User::uniquenickname')
			->rule('password', 'not_empty')
			->rule('password', 'min_length', $user_config['fields']['password']['minlength']);
	}	
	
	/**
	 * Validator helper.
	 * 
	 * @return boolean
	 */
	public static function uniquenickname($nickname)
	{
		$first_row = \app\SQL::prepare
			(
				'ibidem/access:valid_uniquenickname', 
				'
					SELECT COUNT(*)
					  FROM '.static::$table.'
					 WHERE nickname = :nickname
				', 
				'mysql'
			)
			->bind(':nickname', $nickname)
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
			$dependency::inject($id);
		}

		\app\SQL::prepare
			(
				'ibidem/access:dependencies_role_assoc',
				'
					INSERT INTO `'.static::user_role_table().'`
						(user, role)
					VALUES
						(:user, :role)
				'
			)
			->bindInt(':user', $id)
			->bindInt(':role', $user_config['signup']['role'])
			->execute();
	}
	
	/**
	 * @param array fields 
	 */
	public static function assemble(array $fields) 
	{
		// load configuration
		$security = \app\CFS::config('ibidem/security');
		
		// generate password salt and hash
		$passwordsalt = \hash($security['hash']['algorythm'], (\uniqid(\rand(), true)), true);
		$apilocked_password = \hash_hmac($security['hash']['algorythm'], $fields['password'], $security['keys']['apikey'], true);
		$passwordverifier = \hash_hmac($security['hash']['algorythm'], $apilocked_password, $passwordsalt, true);

		\app\SQL::begin(); # begin transaction
		try
		{
			$ipaddress = \app\Layer_HTTP::detect_ip();

			\app\SQL::prepare
				(
					'ibidem/access:assemble',
					'
						INSERT INTO `'.static::table().'`
							(nickname, ipaddress, passwordverifier, passworddate, passwordsalt)
						VALUES
							(:nickname, :ipaddress, :passwordverifier, :passworddate, :passwordsalt)
					',
					'mysql'
				)
				->set(':nickname', \htmlspecialchars($fields['nickname']))
				->set(':ipaddress', $ipaddress)
				->set(':passwordverifier', $passwordverifier)
				->set(':passworddate', \date('c'))
				->set(':passwordsalt', $passwordsalt)
				->execute();
			
			$user_id = \app\SQL::last_inserted_id();

			static::dependencies($user_id);
			
			\app\SQL::commit();
		} 
		catch (\Exception $exception)
		{
			\app\SQL::rollback();
			throw $exception;
		}
	}
	
} # class

