<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_User
{
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Utilities;
	use \app\Trait_Model_Collection;
	
	/**
	 * @var string 
	 */
	protected static $table = 'users';
	
	/**
	 * @var array
	 */
	protected static $field_format = [];
	
	/**
	 * @var string 
	 */
	protected static $user_role_table = 'user_role';
		
	/**
	 * @return string table name
	 */
	static function assoc_roles()
	{
		$database_config = \app\CFS::config('mjolnir/database');
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
	// factory interface
	
	/**
	 * @param array fields
	 * @return Validator 
	 */
	static function check(array $fields, $context = null) 
	{
		$user_config = \app\CFS::config('model/User');
		$validator = \app\Validator::instance($user_config['errors'], $fields)
			->ruleset('not_empty', ['nickname', 'email', 'role'])
			->rule('nickname', 'max_length', $user_config['fields']['nickname']['maxlength'])
			->test('nickname', ':unique', ! static::exists($fields['nickname'], 'nickname', $context));
		
		if ($context === null)
		{
			$validator
				->rule('password', 'not_empty')
				->rule('password', 'min_length', $user_config['fields']['password']['minlength'])
				->rule('verifier', 'equal_to', $fields['password']);
		}
		
		return $validator;
	}
	
	/**
	 * @param array (nickname, email, password, verifier) 
	 */
	static function process(array $fields)
	{
		$password = static::generate_password($fields['password']);
		
		$filtered_fields = array
			(
				'nickname' => \htmlspecialchars($fields['nickname']),
				'email' => \htmlspecialchars($fields['email']),
				'ipaddress' => \app\Server::client_ip(),
				'pwdverifier' => $password['verifier'],
				'pwdsalt' => $password['salt'],
				'pwddate' => \date('Y-m-d H:i:s'),
			);
		
		static::inserter
			(
				$filtered_fields, 
				[
					'nickname', 
					'email', 
					'ipaddress', 
					'pwdverifier',
					'pwdsalt',
					'pwddate',
				]
			)
			->run();
		
		// resolve dependencies
		$user = static::$last_inserted_id = \app\SQL::last_inserted_id();
		static::dependencies(static::$last_inserted_id, \app\CFS::config('model/User'));
		
		// assign role if set
		if (isset($fields['role']))
		{
			static::assign_role($user, $fields['role']);
		}
		
		// cache already reset by inserter
	}
	
	/**
	 * @param string user id 
	 * @param array config
	 */
	static function dependencies($id, array $config = null)
	{
		static::statement
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
	 */
	static function update_process($id, array $fields)
	{
		// update role
		static::assign_role($id, $fields['role']);
		static::updater($id, $fields, ['nickname', 'email'])->run();
		static::clear_entry_cache($id);
	}
	
	// ------------------------------------------------------------------------
	// Collection interface
	
	/**
	 * @param int page
	 * @param int limit
	 * @param int offset
	 * @param array order
	 * @return array
	 */
	static function entries($page, $limit, $offset = 0, $order = [])
	{
		if (empty($order))
		{
			$order = ['id' => 'ASC'];
		}
		
		return static::stash
			(
				__METHOD__,
				'
					SELECT user.id,
					       user.nickname, 
					       user.email,
						   user.timestamp,
					       user.ipaddress,
					       role.id role,
					       role.title roletitle
						   
					  FROM :table user
					  
					  JOIN `'.static::assoc_roles().'` assoc_roles
						ON assoc_roles.user = user.id

					  JOIN `'.static::roles_table().'` role
						ON assoc_roles.role = role.id
				'
			)
			->key(__FUNCTION__)
			->page($page, $limit, $offset)
			->order($order)
			->fetch_all();
	}

	protected static function nullentry_for_current_user( & $entry, $id)
	{
		return $entry === null 
			&& \app\A12n::instance()->role() !== \app\A12n::guest() 
			&& $id === \app\A12n::instance()->user();
	}
	
	/**
	 * @param int id
	 * @return array (id, role, roletitle, nickname, email, ipaddress)
	 */
	static function entry($id)
	{
		if ($id === null)
		{
			return null;
		}
		
		$stashkey = \get_called_class().'_ID'.$id;
		$entry = \app\Stash::get($stashkey, null);
		
		if ($entry === null)
		{
			$entry = static::statement
				(
					__METHOD__,
					'
						SELECT user.*,
							   assoc.role role,
							   role.title roletitle
						  FROM :table user
						  
						  JOIN `'.static::assoc_roles().'` assoc
							ON user.id = assoc.user
							
						  JOIN `'.static::roles_table().'` role
							ON role.id = assoc.role
							
						 WHERE user.id = :id
					',
					'mysql'
				)
				->set_int(':id', $id)
				->execute()
				->fetch_array(static::$field_format);

			if (static::nullentry_for_current_user($entry, $id))
			{
				\app\Controller_A12n::instance()->action_signout();
				exit(1);
			}
			
			if ($entry !== null)
			{
				$entry['timestamp'] = new \DateTime($entry['timestamp']);
			}
			
			\app\Stash::set($stashkey, $entry);
		}
			
		return $entry;
	}
	
	// -------------------------------------------------------------------------
	// Extended
	
	/**
	 * @param int user id
	 * @param int role
	 */
	static function assign_role($id, $role)
	{
		$result = static::statement
			(
				__METHOD__,
				'
					SELECT *
					  FROM `'.static::assoc_roles().'`
					 WHERE `user` = :user
				',
				'mysql'
			)
			->set_int(':user', $id)
			->execute()
			->fetch_all();
		
		if (empty($result))
		{
			static::statement
				(
					__METHOD__,
					'
						INSERT INTO `'.static::assoc_roles().'`
							(`user`, `role`)
						VALUES (:user, :role)
					',
					'mysql'
				)
				->set_int(':user', $id)
				->set_int(':role', $role)
				->execute();
		}
		else # already exists
		{
			static::statement
				(
					__METHOD__,
					'
						UPDATE `'.static::assoc_roles().'`
						   SET `role` = :role
						 WHERE `user` = :user
					',
					'mysql'
				)
				->set_int(':role', $role)
				->set_int(':user', $id)
				->execute();
		}
		
		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}
	
	/**
	 * @param array (identification, email, provider)
	 * @return \app\Validator
	 */
	static function inferred_signup_check(array $fields)
	{
		return \app\Validator::instance([], $fields)
			->ruleset('not_empty', ['identification', 'email', 'role', 'provider']);
	}
	
	/**
	 * @param array fields
	 */
	static function inferred_signup_process(array $fields)
	{
		$fields['ipaddress'] = \app\Server::client_ip();
		$fields['nickname'] = \str_replace('@', '[at]', $fields['identification']);
		
		static::inserter($fields, ['nickname', 'email', 'ipaddress', 'provider'])->run();
		$user = static::$last_inserted_id = \app\SQL::last_inserted_id();
		
		// assign role if set
		if (isset($fields['role']))
		{
			static::assign_role($user, $fields['role']);
		}
	}
	
	/**
	 * @param array (identification, email, provider)
	 * @return \app\Validator|null
	 */
	static function inferred_signup(array $fields)
	{
		$errors = static::inferred_signup_check($fields)->errors();
		
		if (empty($errors))
		{
			\app\SQL::begin();
			try
			{
				static::inferred_signup_process($fields);
				
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
			return $errors;
		}
	}
	
	/**
	 * @param array fields
	 * @return \app\Validator|null
	 */
	static function change_passwords_check($user, array $fields)
	{
		$user_config = \app\CFS::config('model/User');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('password', 'not_empty')
			->rule('verifier', 'equal_to', $fields['password']);
	}
	
	/**
	 * @return \app\Validator|null 
	 */
	static function change_password($user, array $fields)
	{
		$errors = static::change_passwords_check($user, $fields)->errors();
		
		if (empty($errors))
		{
			\app\SQL::begin();
			try
			{
				// compute password
				$password = static::generate_password($fields['password']);
				
				$new_fields = array
					(
						'pwdverifier' => $password['verifier'],
						'pwdsalt' => $password['salt'],
					);
				
				static::updater($user, $new_fields, ['pwdverifier', 'pwdsalt'])->run();
				
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
			return $errors;
		}
	}
	
	/**
	 * @param array fields 
	 */
	static function recompute_password(array $fields)
	{
		// load configuration
		$security = \app\CFS::config('mjolnir/security');
		// generate password salt and hash
		$pwdsalt = \hash($security['hash']['algorythm'], (\uniqid(\rand(), true)), false);
		$apilocked_password = \hash_hmac($security['hash']['algorythm'], $fields['password'], $security['keys']['apikey'], false);
		$pwdverifier = \hash_hmac($security['hash']['algorythm'], $apilocked_password, $pwdsalt, false);
		// update
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET pwdverifier = :pwdverifier,
					       pwdsalt = :pwdsalt,
					       pwddate = :pwddate,
					       ipaddress = :ipaddress
					 WHERE nickname = :nickname
					   AND provider IS NULL
				',
				'mysql'
			)
			->bind(':pwdverifier', $pwdverifier)
			->bind(':pwdsalt', $pwdsalt)
			->set(':pwddate', \date('Y-m-d H:i:s'))
			->bind(':nickname', $fields['nickname'])
			->set(':ipaddress', \app\Server::client_ip())
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
		$security = \app\CFS::config('mjolnir/security');
		
		if (\strpos($fields['identity'], '@') === false)
		{
			$user = static::statement
				(
					__METHOD__,
					'
						SELECT *
						  FROM :table
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
			$user = static::statement
				(
					__METHOD__.':email_signin_check',
					'
						SELECT *
						  FROM :table
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

		$pwdsalt = $user['pwdsalt'];
		
		// generate password salt and hash
		$apilocked_password = \hash_hmac
			(
				$security['hash']['algorythm'], 
				$fields['password'], 
				$security['keys']['apikey'], 
				false
			);
		
		$pwdverifier = \hash_hmac
			(
				$security['hash']['algorythm'], 
				$apilocked_password, 
				$pwdsalt, 
				false
			);
		
		// verify
		if ($pwdverifier !== $user['pwdverifier'])
		{
			return null;
		}
		
		// all tests passed
		return $user['id'];
	}
	
		
	/**
	 * @param int user_id
	 * @return string or null
	 */
	static function role_for($user_id)
	{
		$cachekey = __METHOD__.'_ID'.$user_id;
		$roles = \app\Stash::get($cachekey, null);
		
		if ($roles === null)
		{
			$roles = static::statement
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
			
			\app\Stash::store($cachekey, $roles, \app\Stash::tags('User', ['change']));
		}
		
		if (empty($roles))
		{
			return null; # no role
		}
		else # found role
		{
			return $roles[0]['role'];
		}
	}
	
	/**
	 * @param string email
	 * @return int id
	 */
	static function for_email($email)
	{
		$cachekey = __METHOD__.'_'.\sha1($email);
		$result = \app\Stash::get($cachekey, null);
		
		if ($result === null)
		{
			$result = static::statement
				(
					__METHOD__,
					'
						SELECT id
						  FROM :table
						 WHERE email = :email
						 LIMIT 1
					',
					'mysql'
				)
				->set(':email', $email)
				->execute()
				->fetch_all();
			
			\app\Stash::store
				(
					$cachekey, 
					$result, 
					\app\Stash::tags('User', ['change'])
				);
		}
		
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
	 * Confirm password matches user.
	 * 
	 * @param string password
	 * @param int user
	 * @return boolean 
	 */
	static function matching_password($password, $user)
	{
		$cachekey = __METHOD__.'__userinfo_'.$user;
		$user_info = \app\Stash::get($cachekey, null);
		
		if ($user_info === null)
		{
			// get user data
			$user_info = static::statement
				(
					__METHOD__,
					'
						SELECT users.pwdsalt salt,
							   users.pwdverifier verifier
						  FROM :table users
						 WHERE users.id = :user
						 LIMIT 1
					',
					'mysql'
				)
				->set_int(':user', $user)
				->execute()
				->fetch_array();
			
			\app\Stash::store
				(
					$cachekey, 
					$user_info, 
					\app\Stash::tags('User', ['change'])
				);
		}
		
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
	 * @return array [salt, verifier]
	 */
	protected static function generate_password($password_text, $salt = null)
	{
		$password = [];
		
		// load configuration
		$security = \app\CFS::config('mjolnir/security');
		
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

