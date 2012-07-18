<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_User extends \app\Model_SQL_Factory
{
	/**
	 * @var string 
	 */
	protected static $table = 'users';
	
	/**
	 * @var string 
	 */
	protected static $user_role_table = 'user_role';
		
	/**
	 * @return string table name
	 */
	static function assoc_roles()
	{
		$database_config = \app\CFS::config('ibidem/database');
		return $database_config['table_prefix'].static::$user_role_table;
	}
	
	/**
	 * @return string table
	 */
	static function roles_table()
	{
		return \app\Model_Role::table();
	}
	
	// -------------------------------------------------------------------------
	// Model_Factory interface
	
	/**
	 * @param array fields
	 * @return Validator 
	 */
	static function validator(array $fields) 
	{
		$user_config = \app\CFS::config('model/User');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('nickname', 'not_empty')
			->rule('nickname', 'max_length', $user_config['fields']['nickname']['maxlength'])
			->rule('nickname', '\app\Model_User::unique_nickname')
			->rule('email', 'not_empty')
			->rule('password', 'not_empty')
			->rule('password', 'min_length', $user_config['fields']['password']['minlength'])
			->rule('verifier', 'equal_to', $fields['password'])
			->rule('role', 'not_empty');
	}
	
	/**
	 * @param array (nickname, email, password, verifier) 
	 */
	static function assemble(array $fields) 
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
							UPDATE `'.static::assoc_roles().'`
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
	static function dependencies($id, array $config = null)
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
	static function update_validator($id, array $fields)
	{
		$user_config = \app\CFS::config('model/User');
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('nickname', '\app\Model_User::unique_new_nickname', $id);
	}
	
	/**
	 * @param int id
	 * @param array fields 
	 * @return \app\Validator|null
	 */
	static function update_assemble($id, array $fields)
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
	static function entries($page, $limit, $offset = 0, $sort = 'id', $order = 'ASC')
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT user.id id,
					       user.nickname nickname, 
					       user.email email,
					       role.id role,
					       role.title roletitle,
						   user.timestamp timestamp,
					       user.ipaddress ipaddress
					  FROM `'.static::table().'` AS user,
					       `'.static::roles_table().'` AS role,
					       `'.static::assoc_roles().'` AS assoc_roles
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
	static function entry($id)
	{
		$entry = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT user.id id,
					       assoc.role role,
						   role.title roletitle,
						   user.nickname nickname,
						   user.email email,
						   user.timestamp timestamp,
						   user.ipaddress ipaddress
					  FROM `'.static::table().'` user
					  JOIN `'.static::assoc_roles().'` assoc
						ON user.id = assoc.user
					  JOIN `'.static::roles_table().'` role
						ON role.id = assoc.role
					 WHERE user.id = :id
						   
				'
			)
			->set_int(':id', $id)
			->execute()
			->fetch_array();
		
		$entry['timestamp'] = new \DateTime($entry['timestamp']);
		
		return $entry;
	}
	
	// -------------------------------------------------------------------------
	// Extended
	
	/**
	 * @param array (identification, email, provider)
	 * @return \app\Validator
	 */
	static function inferred_validator(array $fields)
	{
		return \app\Validator::instance([], $fields)
			->rule('identification', 'not_empty')
			->rule('email', 'not_empty')
			->rule('role', 'not_empty')
			->rule('provider', 'not_empty');
	}
	
	/**
	 * @param array (identification, email, provider)
	 * @throws \ibidem\access\Exception
	 */
	static function inferred_assemble(array $fields)
	{
		$identification = \str_replace('@', '[at]', $fields['identification']);
		
		\app\SQL::begin();
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
								provider
							)
						VALUES
							(
								:nickname, 
								:email,
								:ipaddress, 
								:provider
							)
					'
				)
				->set(':nickname', $identification)
				->set(':email', $fields['email'])
				->set(':ipaddress', $ipaddress)
				->set(':provider', $fields['provider'])
				->execute();
			
			$user = \app\SQL::last_inserted_id();
			
			static::$last_inserted_id = $user;
			
			// assign role if set
			if (isset($fields['role']))
			{
				\app\SQL::prepare
					(
						__METHOD__.':assign_role',
						'
							INSERT `'.static::assoc_roles().'`
								(user, role)
							VALUES 
								(:user, :role)
						',
						'mysql'
					)
					->set_int(':role', $fields['role'])
					->set_int(':user', $user)
					->execute();
			}
			
			\app\SQL::commit();
		}
		catch (\Exception $e)
		{
			\app\SQL::rollback();
			throw $e;
		}
				
	}
	
	/**
	 * @param array (identification, email, provider)
	 * @return \app\Validator|null
	 */
	static function inferred_signup(array $fields)
	{
		$validator = static::inferred_validator($fields);
		
		if ($validator->validate() === null)
		{					
			static::inferred_assemble($fields);
			return null;
		}
		else # invalid
		{
			return $validator;
		}
	}
	
	/**
	 * @param array fields
	 * @return \app\Validator|null
	 */
	static function validator_change_passwords($user, array $fields)
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
	static function change_password($user, array $fields)
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
	static function recompute_password(array $fields)
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
					   AND provider IS NULL
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
	 * @param array (identity, password)
	 * @return boolean 
	 */
	static function signin_check(array $fields = null)
	{
		// got fields?
		if ( ! $fields)
		{
			return null;
		}
		
		// got required fields
		if ( ! isset($fields['identity']) || ! isset($fields['password']))
		{
			return null;
		}
		
		// load configuration
		$security = \app\CFS::config('ibidem/security');
		
		if (\strpos($fields['identity'], '@') === false)
		{
			$user = \app\SQL::prepare
				(
					__METHOD__,
					'
						SELECT *
						  FROM '.static::table().'
						 WHERE nickname = :nickname
						   AND provider IS NULL
						 LIMIT 1
					',
					'mysql'
				)
				->bind(':nickname', $fields['identity'])
				->execute()
				->fetch_array();
		}
		else # email
		{
			$user = \app\SQL::prepare
				(
					__METHOD__.':email_signin_check',
					'
						SELECT *
						  FROM '.static::table().'
						 WHERE email = :email
						   AND provider IS NULL
						 LIMIT 1
					',
					'mysql'
				)
				->bind(':email', $fields['identity'])
				->execute()
				->fetch_array();
		}
		
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
	 * @param int user_id
	 * @return string|null
	 */
	static function role_for($user_id)
	{
		$roles = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT role.title role
					  FROM `'.static::roles_table().'` role
					  JOIN `'.static::assoc_roles().'` assoc
						ON assoc.role = role.id
					 WHERE assoc.user = :user
					 LIMIT 1
				',
				'mysql'
			)
			->set_int(':user', $user_id)
			->execute()
			->fetch_all();
		
		if (empty($roles))
		{
			return null; # no role
		}
		else # found role
		{
			return $roles[0]['role'];
		}
	}
	
	static function for_email($email)
	{
		$result = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT id
					  FROM `'.static::table().'`
					 WHERE email = :email
					 LIMIT 1
				',
				'mysql'
			)
			->set(':email', $email)
			->execute()
			->fetch_all();
		
		if ( ! empty($result))
		{
			return $result[0]['id'];
		}
		else # empty resultset
		{
			return null;
		}
	}

	// -------------------------------------------------------------------------
	// Validator Helpers
	
	/**
	 * @return boolean
	 */
	static function unique_nickname($nickname)
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
	static function unique_new_nickname($nickname, $user)
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
	static function matching_password($password, $user)
	{
		// get user data
		$user_info = \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT users.passwordsalt salt,
					       users.passwordverifier verifier
					  FROM `'.static::table().'` users
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

