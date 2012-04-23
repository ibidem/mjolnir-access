<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_HTTP_User extends \app\Model_DB_User
{	
	/**
	 * @var \ibidem\types\Auth
	 */
	protected $auth;
	
	/**
	 * @return \ibidem\access\Model_HTTP_User
	 */
	public static function instance()
	{
		$instance = parent::instance();
		$instance->auth(\app\A12n::instance());
		
		return $instance;
	}
	
	/**
	 * @return \ibidem\access\Model_HTTP_User $this
	 */
	public function auth(\ibidem\types\Auth $auth)
	{
		$this->auth = $auth;
		return $this;
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

