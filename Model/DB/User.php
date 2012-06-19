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
		$database_config = \app\CFS::config('ibidem/database');
		return $database_config['table_prefix'].static::$roles_table;
	}
	
	/**
	 * @return string 
	 */
	public static function assoc_roles()
	{
		$database_config = \app\CFS::config('ibidem/database');
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
			->rule('email', 'not_empty')
			->rule('password', 'not_empty')
			->rule('password', 'min_length', $user_config['fields']['password']['minlength'])
			->rule('verifier', 'equal_to', $fields['password'])
			->rule('role', 'not_empty');
	}
	
	/**
	 * @param array (nickname, email, password, verifier) 
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
					__METHOD__,
					'
						INSERT INTO `'.static::table().'`
							(
								nickname, 
								email,
								ipaddress, 
								passwordverifier, 
								passworddate, 
								passwordsalt
							)
						VALUES
							(
								:nickname, 
								:email,
								:ipaddress, 
								:passwordverifier, 
								:passworddate, 
								:passwordsalt
							)
					',
					'mysql'
				)
				->set(':nickname', \htmlspecialchars($fields['nickname']))
				->set(':email', \htmlspecialchars($fields['email']))
				->set(':ipaddress', $ipaddress)
				->set(':passwordverifier', $password['verifier'])
				->set(':passworddate', \date('c'))
				->set(':passwordsalt', $password['salt'])
				->execute();
			
			$user = static::$last_inserted_id = \app\SQL::last_inserted_id();

			static::dependencies(static::$last_inserted_id, \app\CFS::config('model/User'));
			
			// assign role if set
			if (isset($fields['role']))
			{
				\app\SQL::prepare
					(
						__METHOD__.':assign_role',
						'
							UPDATE `'.\app\Model_DB_User::assoc_roles().'`
							SET role = :role
							WHERE user = '.$user.'
						'
					)
					->set_int(':role', $fields['role'])
					->execute();
			}
			
			\app\SQL::commit();
		} 
		catch (\Exception $exception)
		{
			\app\SQL::rollback();
			throw $exception;
		}
	}
	
	/**
	 * Validator helper.
	 * 
	 * @return boolean
	 */
	public static function unique_role($role)
	{
		$first_row = \app\SQL::prepare
			(
				__METHOD__, 
				'
					SELECT COUNT(1)
					  FROM '.static::roles_table().'
					 WHERE role = :role
				', 
				'mysql'
			)
			->bind(':role', $role)
			->execute()
			->fetch_array();
		
		$count = $first_row['COUNT(1)'];
		
		if (\intval($count) != 0)
		{
			return false;
		}
		
		// test passed
		return true;
	}
	
	function validator_role(array $fields)
	{
		$user_config = \app\CFS::config('model/UserRole');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('title', 'not_empty')
			->rule('title', '\app\Model_DB_User::unique_role');
	}
	
	function assemble_role(array $fields)
	{
		\app\SQL::begin(); # begin transaction
		try
		{
			\app\SQL::prepare
				(
					__METHOD__,
					'
						INSERT INTO `'.static::table().'`
							(
								title
							)
						VALUES
							(
								:title 
							)
					',
					'mysql'
				)
				->set(':title', \htmlspecialchars($fields['title']))
				->execute();
			
			$user = static::$last_inserted_id = \app\SQL::last_inserted_id();
			
			\app\SQL::commit();
		} 
		catch (\Exception $exception)
		{
			\app\SQL::rollback();
			throw $exception;
		}
	}
	
	function build_role(array $fields)
	{
		$validator = static::validator_role($fields);
		
		if ($validator === null || $validator->validate() === null)
		{
			static::assemble_role($fields);
			
			// invalidate caches
			static::cache_reset($fields);
		}
		else # did not pass validation
		{
			return $validator;
		}
		
		return null;
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
				__METHOD__, 
				'
					SELECT COUNT(1)
					  FROM '.static::table().'
					 WHERE nickname = :nickname
				', 
				'mysql'
			)
			->bind(':nickname', $nickname)
			->execute()
			->fetch_array();
		
		$count = $first_row['COUNT(1)'];
		
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
				__METHOD__,
				'
					INSERT INTO `'.static::assoc_roles().'`
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
	 * @param type $id
	 * @param array $fields 
	 * @return \app\Validator
	 */
	public static function update_validator($id, array $fields)
	{
		$user_config = \app\CFS::config('model/User');
		return \app\Validator::instance($user_config['errors'], $fields);
	}
	
	/**
	 * @param int id
	 * @param array fields 
	 */
	public static function update($id, array $fields)
	{
		$validator = static::update_validator($id, $fields);
		
		if ($validator->validate() === null)
		{
			\app\SQL::begin();
			try
			{
				// get current nickname
				$current_info = \app\SQL::prepare
					(
						__METHOD__.':check_nickname',
						'
							SELECT *
							  FROM `'.static::table().'` user
							 WHERE user.id = :id
						'
					)
					->bind_int(':id', $id)
					->execute()
					->fetch_array();
				
				if ($current_info['nickname'] != $fields['nickname'])
				{
					// check if available
					if ( ! static::unique_nickname($fields['nickname']))
					{
						$fields['nickname'] = $current_info['nickname'];
					}
				}
				
				// update role
				\app\SQL::prepare
					(
						__METHOD__.':update_role',
						'
							UPDATE `'.static::assoc_roles().'`
							   SET role = :role
							 WHERE user = :user
						'
					)
					->set_int(':user', $id)
					->set_int(':role', $fields['role'])
					->execute();
				
				\app\SQL::prepare
					(
						__METHOD__,
						'
							UPDATE `'.static::table().'`
							   SET nickname = :nickname,
							       given_name = :given_name,
							       family_name = :family_name,
							       email = :email
							 WHERE id = :id
						'
					)
					->set(':nickname', $fields['nickname'])
					->set(':given_name', $fields['given_name'])
					->set(':family_name', $fields['family_name'])
					->set(':email', $fields['email'])
					->bind_int(':id', $id)
					->execute();
				
				\app\SQL::commit();
			}
			catch (\Exception $e)
			{
				\app\SQL::rollback();
				throw $e;
			}
			
			return null;
		}
		else # invalid
		{
			return $validator;
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
				__METHOD__,
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
					__METHOD__,
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
	
	/**
	 * @param array fields 
	 */
	public static function recompute_password(array $fields)
	{
		// load configuration
		$security = \app\CFS::config('ibidem/security');
		// generate password salt and hash
		$passwordsalt = \hash($security['hash']['algorythm'], (\uniqid(\rand(), true)), true);
		$apilocked_password = \hash_hmac($security['hash']['algorythm'], $fields['password'], $security['keys']['apikey'], true);
		$passwordverifier = \hash_hmac($security['hash']['algorythm'], $apilocked_password, $passwordsalt, true);
		// update
		\app\SQL::prepare
			(
				__METHOD__,
				'
					UPDATE '.static::table().'
					   SET passwordverifier = :passwordverifier
					   SET passwordsalt = :passwordsalt
					   SET passworddate = :passworddate
					   SET ipaddress = :ipaddress
					 WHERE nickname = :nickname
				',
				'mysql'
			)
			->bind(':passwordverifier', $passwordverifier)
			->bind(':passwordsalt', $passwordsalt)
			->bind(':passworddate', \time())
			->bind(':nickname', $fields['nickname'])
			->bind(':ipaddress', $fields['nickname'])
			->execute();
	}
	
	/**
	 * @param array fields
	 * @return boolean 
	 */
	public static function signin_check(array $fields = null)
	{
		// got fields?
		if ( ! $fields)
		{
			return null;
		}
		
		// got required fields
		if ( ! isset($fields['nickname']) || ! isset($fields['password']))
		{
			return null;
		}
		
		// load configuration
		$security = \app\CFS::config('ibidem/security');
		
		$user = \app\SQL::prepare
			(
				'ibidem/access:signin_check',
				'
					SELECT *
					FROM '.static::table().'
					WHERE nickname = :nickname
					  AND deleted = FALSE
					LIMIT 1
				',
				'mysql'
			)
			->bind(':nickname', $fields['nickname'])
			->execute()
			->fetch_array();

		if ( ! $user)
		{
			return null;
		}

		$passwordsalt = $user['passwordsalt'];
		
		// generate password salt and hash
		$apilocked_password = \hash_hmac
			(
				$security['hash']['algorythm'], 
				$fields['password'], 
				$security['keys']['apikey'], 
				false
			);
		$passwordverifier = \hash_hmac
			(
				$security['hash']['algorythm'], 
				$apilocked_password, 
				$passwordsalt, 
				false
			);
		
		// verify
		if ($passwordverifier !== $user['passwordverifier'])
		{
			return null;
		}
		
		// all tests passed
		return $user['id'];
	}	
		
	/**
	 * @param int id
	 * @return string
	 */
	public static function user_role($user)
	{
		$result = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT roles.title role
					  FROM `'.\app\Model_DB_User::roles_table().'` AS roles,
						   `'.\app\Model_DB_User::assoc_roles().'` AS assoc
					 WHERE roles.id = assoc.role
					   AND assoc.user = :user
				',
				'mysql'
			)
			->bind_int(':user', $user)
			->execute()
			->fetch_array();
		
		return $result['role'];
	}
	
	/**
	 * @param int page
	 * @param int limit
	 * @return array (id, nickname, email, role)
	 */
	public static function users($page = 1, $limit = 10)
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT  user.id id,
					        user.nickname nickname, 
					        user.email email,
					        role.title role,
							user.ipaddress ipaddress
					  FROM `'.\app\Model_DB_User::table().'` AS user,
						   `'.\app\Model_DB_User::roles_table().'` AS role,
						   `'.\app\Model_DB_User::assoc_roles().'` AS assoc_roles
					 WHERE assoc_roles.role = role.id 
					   AND assoc_roles.user = user.id 
					   AND user.deleted = FALSE
					 ORDER BY user.id ASC
				'
			)
			->execute()
			->fetch_all();
	}
	
	/**
	 * @param int id
	 * @return string
	 */
	public static function user_role_id($user)
	{
		$result = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT roles.id role_id
					  FROM `'.\app\Model_DB_User::roles_table().'` AS roles,
						   `'.\app\Model_DB_User::assoc_roles().'` AS assoc
					 WHERE roles.id = assoc.role
					   AND assoc.user = :user
				',
				'mysql'
			)
			->bind_int(':user', $user)
			->execute()
			->fetch_array();
		
		return $result['role_id'];
	}
	
	/**
	 * @return array (id, title)
	 */
	public static function user_roles()
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT role.id id,
					       role.title title
					  FROM `'.static::roles_table().'` role
				'
			)
			->execute()
			->fetch_all();
	}
	
	/**
	 * @param array user id's 
	 */
	public static function mass_delete(array $user_ids)
	{
		$user = null;
		$statement = \app\SQL::prepare
			(
				__METHOD__,
				'
					UPDATE `'.static::table().'` user
					   SET user.deleted = TRUE
					 WHERE user.id = :user
				'
			)
			->bind_int(':user', $user);
		
		foreach ($user_ids as $id)
		{
			$user = $id;
			$statement->execute();
		}
	}
	
	public static function count()
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT COUNT(1)
					  FROM `'.static::table().'` user
					 WHERE user.deleted = FALSE
				'
			)
			->execute()
			->fetch_array()
			['COUNT(1)'];
	}
	
	public static function roles_count()
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT COUNT(1)
					  FROM `'.static::roles_table().'` role
				'
			)
			->execute()
			->fetch_array()
			['COUNT(1)'];
	}
	
} # class

