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
	public static function user_role($id)
	{
		$result = \app\SQL::prepare
			(
				'\ibidem\access\a12n',
				'
					SELECT roles.title role
					  FROM `'.\app\Model_DB_User::roles_table().'` AS roles,
						   `'.\app\Model_DB_User::assoc_roles().'` AS assoc
					 WHERE roles.id = assoc.role
					   AND assoc.user = :user
				',
				'mysql'
			)
			->bind_int(':user', $id)
			->execute()
			->fetch_array();
		
		return $result['role'];
	}

} # class

