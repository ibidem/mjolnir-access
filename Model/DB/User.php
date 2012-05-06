<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_DB_User extends \app\Model_SQL_Factory
{
	protected static $table = 'users';
	protected static $roles_table = 'roles';
	protected static $user_role_table = 'user_role';
	
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
			->rule('nickname', '\app\Model_DB_User::unique_nickname')
			->rule('password', 'not_empty')
			->rule('password', 'min_length', $user_config['fields']['password']['minlength']);
	}	
	
	/**
	 * Validator helper.
	 * 
	 * @return boolean
	 */
	public static function unique_nickname($nickname)
	{
		$first_row = \app\SQL::prepare
			(
				'ibidem/access:valid_uniquenickname', 
				'
					SELECT COUNT(*)
					  FROM '.static::table().'
					 WHERE nickname = :nickname
				', 
				'mysql'
			)
			->bind(':nickname', $nickname)
			->execute()
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
	 * @param array config
	 */
	public static function dependencies($id, array $config = null)
	{
		parent::dependencies($id, $config);

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
			->bind_int(':user', $id)
			->bind_int(':role', $config['signup']['role'])
			->execute();
	}
	
	/**
	 * @param string password (plaintext)
	 * @param string salt
	 * @return array (salt, verifier)
	 */
	protected static function generate_password($password_text, $salt = null)
	{
		$password = array();
		
		// load configuration
		$security = \app\CFS::config('ibidem/security');
		
		// generate password salt and hash
		if ($salt === null)
		{
			$password['salt'] = \hash($security['hash']['algorythm'], (\uniqid(\rand(), true)), false);
		}
		else # salt provided
		{
			$password['salt'] = $salt;
		}
		$apilocked_password = \hash_hmac($security['hash']['algorythm'], $password_text, $security['keys']['apikey'], false);
		$password['verifier'] = \hash_hmac($security['hash']['algorythm'], $apilocked_password, $password['salt'], false);
		
		return $password;
	}
	
	/**
	 * @param array fields 
	 */
	public static function assemble(array $fields) 
	{
		$password = static::generate_password($fields['password']);

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
				->set(':passwordverifier', $password['verifier'])
				->set(':passworddate', \date('c'))
				->set(':passwordsalt', $password['salt'])
				->execute();
			
			$user_id = \app\SQL::last_inserted_id();

			static::dependencies($user_id, \app\CFS::config('model/User'));
			
			\app\SQL::commit();
		} 
		catch (\Exception $exception)
		{
			\app\SQL::rollback();
			throw $exception;
		}
	}
	
	/**
	 * Confirm password matches user.
	 * 
	 * @param string password
	 * @param int user
	 * @return boolean 
	 */
	public static function matching_password($password, $user)
	{
		// get user data
		$user_info = \app\SQL::prepare
			(
				'\ibidem\access\user::matching_password:getuser',
				'
					SELECT users.passwordsalt salt,
					       users.passwordverifier verifier
					  FROM `'.\app\Model_DB_User::table().'` users
					 WHERE users.id = :user
					 LIMIT 1
				',
				'mysql'
			)
			->set_int(':user', $user)
			->execute()
			->fetch_array();
		
		// compute verifier for given password
		$test = static::generate_password($password, $user_info['salt']);
		
		if ($test['verifier'] == $user_info['verifier'])
		{
			return true;
		}
		else # does not match
		{
			return false;
		}
	}
	
	/**
	 * @param array fields
	 * @return \app\Validator|null
	 */
	public static function validator_change_passwords(array $fields)
	{
		$user_config = \app\CFS::config('model/User');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('new_password', 'not_empty')
			->rule('verifier', 'equal_to', $fields['new_password'])
			->rule
				(
					'password', 
					'\app\Model_DB_User::matching_password', 
					\app\A12n::instance()->user()
				);
	}
	
	/**
	 *
	 * @param array fields
	 * @param int user
	 * @return \app\Validator|null 
	 */
	public static function change_password(array $fields, $user)
	{
		$validator = static::validator_change_passwords($fields);
		
		if ($validator->validate() === null)
		{
			// compute password
			$password = static::generate_password($fields['new_password']);
			
			\app\SQL::prepare
				(
					'\ibidem\access\user:change_password',
					'
						UPDATE `'.static::table().'` users
						   SET users.passwordverifier = :verifier,
						       users.passwordsalt = :salt
						 WHERE users.id = :id
					',
					'mysql'
				)
				->set(':verifier', $password['verifier'])
				->set(':salt', $password['salt'])
				->set(':id', $user)
				->execute();
			
			return null;
		}
		else # invalid
		{
			return $validator;
		}
	}
	
} # class

