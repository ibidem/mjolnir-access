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
	protected static $user_role_table = 'user_role';
		
	/**
	 * @return string table name
	 */
	public static function assoc_roles()
	{
		$database_config = \app\CFS::config('ibidem/database');
		return $database_config['table_prefix'].static::$user_role_table;
	}
	
	// -------------------------------------------------------------------------
	// Model_Factory interface
	
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
						',
						'mysql'
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
				',
				'mysql'
			)
			->bind_int(':user', $id)
			->bind_int(':role', $config['signup']['role'])
			->execute();
	}


	/**
	 * @param int id
	 * @param array fields 
	 * @return \app\Validator
	 */
	public static function update_validator($id, array $fields)
	{
		$user_config = \app\CFS::config('model/User');
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('nickname', '\app\Model_DB_User::unique_new_nickname', $id);
	}
	
	/**
	 * @param int id
	 * @param array fields 
	 * @return \app\Validator|null
	 */
	public static function update_assemble($id, array $fields)
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
					',
					'mysql'
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
					',
					'mysql'
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
							   email = :email
						 WHERE id = :id
					',
					'mysql'
				)
				->set(':nickname', $fields['nickname'])
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
	}
	
	/**
	 * @param int page
	 * @param int limit
	 * @param int offset
	 * @param string order key
	 * @param string order
	 * @return array (id, role, roletitle, nickname, email, ipaddress)
	 */
	public static function entries($page, $limit, $offset = 0, $sort = 'id', $order = 'ASC')
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT  user.id id,
					        user.nickname nickname, 
					        user.email email,
					        role.id role,
					        role.title roletitle,
					        user.ipaddress ipaddress
					  FROM `'.\app\Model_DB_User::table().'` AS user,
					       `'.\app\Model_DB_Role::table().'` AS role,
					       `'.\app\Model_DB_User::assoc_roles().'` AS assoc_roles
					 WHERE assoc_roles.role = role.id 
					   AND assoc_roles.user = user.id 
					 ORDER BY :sort '.$order.'
					 LIMIT :limit OFFSET :offset
				',
				'mysql'
			)
			->page($page, $limit, $offset)
			->set(':sort', $sort)
			->execute()
			->fetch_all();
	}
	
	/**
	 * 
	 * @param type $id
	 * @return array (id, role, roletitle, nickname, email, ipaddress)
	 */
	public static function entry($id)
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT user.id id,
					       assoc.role role,
						   role.title roletitle,
						   user.nickname nickname,
						   user.email email,
						   user.ipaddress ipaddress
					  FROM `'.static::table().'` user
					  JOIN `'.static::assoc_roles().'` assoc
						ON user.id = assoc.user
					  JOIN `'.\app\Model_DB_Role::table().'` role
						ON role.id = assoc.role
					 WHERE user.id = :id
						   
				'
			)
			->set_int(':id', $id)
			->execute()
			->fetch_array();
	}
	
	// -------------------------------------------------------------------------
	// Extended methods
	
	/**
	 * @param array fields
	 * @return \app\Validator|null
	 */
	public static function validator_change_passwords($user, array $fields)
	{
		$user_config = \app\CFS::config('model/User');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('password', 'not_empty')
			->rule('verifier', 'equal_to', $fields['password']);
	}
	
	/**
	 * @param array fields
	 * @param int user
	 * @return \app\Validator|null 
	 */
	public static function change_password($user, array $fields)
	{
		$validator = static::validator_change_passwords($user, $fields);
		
		if ($validator->validate() === null)
		{		
			// compute password
			$password = static::generate_password($fields['password']);
			
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
	

	
	// -------------------------------------------------------------------------
	// Validator Helpers
	
	/**
	 * @return boolean
	 */
	public static function unique_nickname($nickname)
	{
		$count = \app\SQL::prepare
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
			->fetch_array()
			['COUNT(1)'];
		
		if (\intval($count) != 0)
		{
			return false;
		}
		
		// test passed
		return true;
	}
	
	/**
	 * @param string nickname
	 * @param int user
	 * @return bool 
	 */
	public static function unique_new_nickname($nickname, $user)
	{
		$count = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT COUNT(1)
					  FROM `'.static::table().'` user
					 WHERE user.id != :user 
					   AND user.nickname = :nickname
					 LIMIT 1
				',
				'mysql'
			)
			->set(':nickname', $nickname)
			->set_int(':user', $user)
			->execute()
			->fetch_array()
			['COUNT(1)'];
		
		return ((int) $count) == 0;
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

	// -------------------------------------------------------------------------
	// Helpers
	
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
	
} # class

