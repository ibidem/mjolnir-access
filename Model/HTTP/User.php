<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_HTTP_User extends \app\Model_Factory
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

		\app\SQL::insert
			(
				'ibidem/access:dependencies_role_assoc',
				array
				(
					'user' => $id,
					'role' => $user_config['signup']['role'],
				),
				static::user_role_table()
			);
	}
	
	/**
	 * @param array fields 
	 */
	public static function assemble(array $fields) 
	{
		// load configuration
		$security = \app\CFS::config('security');
		
		// generate password salt and hash
		$passwordsalt = \hash($security['hash']['algorythm'], (\uniqid(\rand(), true)), true);
		$apilocked_password = \hash_hmac($security['hash']['algorythm'], $fields['password'], $security['keys']['apikey'], true);
		$passwordverifier = \hash_hmac($security['hash']['algorythm'], $apilocked_password, $passwordsalt, true);

		\app\SQL::begin(); # begin transaction
		try
		{
			$encrypted_ipaddress = \base64_encode
				(
					\mcrypt_encrypt
					(
						MCRYPT_RIJNDAEL_256,                    # cipher
						\md5($security['keys']['apikey']),      # key
						\app\Layer_HTTP::detect_ip(),           # data
						MCRYPT_MODE_CBC,                        # mode
						\md5(\md5($security['keys']['apikey'])) # iv
					)
				);

			list($user_id) = \app\SQL::insert
				(
					'ibidem/access:assemble',
					array
					(
						'nickname' => HTML::chars($fields['nickname']),
						'ipaddress' => $encrypted_ipaddress,
						'passwordverifier' => $passwordverifier,
						'passworddate' => \date('c'), // ISO 8601 date
						'passwordsalt' => $passwordsalt,
					),
					static::table()
				);

			static::dependencies($user_id);
			
			SQL::commit();
		} 
		catch (\Exception $exception)
		{
			SQL::rollback();
			throw $exception;
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
		if (\strpos($fields['identification'], '@') === false)
		{
			\app\SQL::prepare	
				(
					'ibidem/access:remcompute_password',
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
				->bind(':nickname', $fields['identification'])
				->bind(':ipaddress', $fields['identification'])
				->execute();
		}
		else # email identification
		{
			$encrypted_email = \base64_encode
				(
					\mcrypt_encrypt
					(
						MCRYPT_RIJNDAEL_256,                    # cipher
						\md5($security['keys']['apikey']),      # key
						$fields['identification'],              # data
						MCRYPT_MODE_CBC,                        # mode
						\md5(\md5($security['keys']['apikey'])) # iv
					)
				);
			
			\app\SQL::prepare
				(
					'ibidem/access:remcompute_password_email',
					'
						UPDATE '.static::table().'
						   SET passwordverifier = :passwordverifier
						   SET passwordsalt = :passwordsalt
						   SET passworddate = :passworddate
						   SET ipaddress = :ipaddress
						 WHERE email = :email
					',
					'mysql'
				)
				->bind(':passwordverifier', $passwordverifier)
				->bind(':passwordsalt', $passwordsalt)
				->bind(':passworddate', \time())
				->bind(':email', $encrypted_email)
				->bind(':ipaddress', $fields['identification'])
				->execute();
		}
	}
	
	/**
	 * @param array fields
	 * @return boolean 
	 */
	public static function signin_check(array $fields)
	{
		// load configuration
		$security = \app\CFS::config('ibidem/security');
		
		if (\strpos($fields['identification'], '@') === false)
		{
			$user = \app\SQL::prepare
				('
					SELECT *
					  FROM '.static::table().'
					 WHERE nickname = :nickname
				')
				->bind(':nickname', $fields['identification'])
				->execute()
				->fetch_array();
			
			if ( ! $user)
			{
				return null;
			}
			
			$passwordsalt = $user['passwordsalt'];
		}
		else # email identification
		{
			// encrypt email
			$encrypted_email = \base64_encode
				(
					\mcrypt_encrypt
					(
						MCRYPT_RIJNDAEL_256,            # cipher
						\md5($fields['password']),      # key
						$fields['identification'],      # data
						MCRYPT_MODE_CBC,                # mode
						\md5(\md5($fields['password'])) # iv
					)
				);
			
			$user = SQL::select
				('
					SELECT *
					  FROM '.static::table().'
					 WHERE email = :email
				')
				->bind(':email', $encrypted_email)
				->execute()
				->fetch_array();

			if ( ! $user)
			{
				return null;
			}
			
			$passwordsalt = $user['passwordsalt'];
		}
		
		// generate password salt and hash
		$apilocked_password = \hash_hmac($security['hash']['algorythm'], $fields['password'], $security['keys']['apikey'], true);
		$passwordverifier = \hash_hmac($security['hash']['algorythm'], $apilocked_password, $passwordsalt, true);
		
		// verify
		if ($passwordverifier !== $user['passwordverifier'])
		{
			return null;
		}
		
		// all tests passed
		return $user['id'];
	}

} # class

