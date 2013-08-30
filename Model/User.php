<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_User
{
	use \app\Trait_ModelLib;
	use \app\Trait_Model_SecurityToken;

	/**
	 * @var string
	 */
	protected static $table = 'mjolnir__users';

	/**
	 * @var array
	 */
	protected static $fieldformat = 'model/user';

	/**
	 * @var string
	 */
	protected static $user_role_table = 'mjolnir__user_role';

	/**
	 * @return string table name
	 */
	static function assoc_roles()
	{
		return \app\CFS::config('mjolnir/database')['table_prefix'].static::$user_role_table;
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
	 * ...
	 */
	static function cleanup(array &$fields)
	{
		isset($fields['verifier']) or $fields['verifier'] = $fields['password'];
		isset($fields['active']) or $fields['active'] = true;
	}

	/**
	 * @return \mjolnir\types\Validator
	 */
	static function check(array $fields, $context = null)
	{
		$user_config = \app\CFS::config('model/user');

		$user_for_email = \app\Model_User::for_email($fields['email']);

		if ($user_for_email === null || $user_for_email === $context)
		{
			$unique_email = true;
		}
		else # email is taken
		{
			$unique_email = false;
		}

		$validator = \app\Validator::instance($fields, $unique_email)
			->adderrormessages($user_config['errors'])
			->rule(['nickname', 'role'], 'not_empty')
			->rule('nickname', 'max_length', \strlen($fields['nickname']) <= $user_config['fields']['nickname']['maxlength'])
			->rule('nickname', ':unique', ! static::exists($fields['nickname'], 'nickname', $context));

		$validator = static::email_checks($fields, $validator, $unique_email);

		if ($context === null)
		{
			$validator
				->rule('password', 'not_empty')
				->rule('password', 'min_length', \strlen($fields['password']) >= $user_config['fields']['password']['minlength'])
				->rule('verifier', 'equal_to', $fields['verifier'] == $fields['password']);
		}

		return $validator;
	}

	/**
	 * @return \mjolnir\types\Validator
	 */
	static function email_checks(array $fields, \mjolnir\types\Validator $validator, $unique_email)
	{
		return $validator
			->rule('email', 'not_empty')
			->test('email', \app\Email::valid($fields['email']))
			->rule('email', ':unique', $unique_email);
	}

	/**
	 * Add additional fields for processing
	 */
	static function injectfields(array &$filtered_fields, array $fields)
	{
		// empty
	}

	/**
	 * @return array
	 */
	static function filteredfieldnames()
	{
		return array
			(
				'strs' => array
					(
						'nickname',
						'email',
						'ipaddress',
						'pwdverifier',
						'pwdsalt',
						'pwddate',
						'last_signin'
					),
				'nums' => array
					(
						// empty
					),
				'bools' => array
					(
						'active'
					),
			);
	}

	/**
	 * @param array (nickname, email, password, verifier)
	 */
	static function process(array $fields)
	{
		$pwd = \app\Password::generate($fields['password']);

		$filtered_fields = array
			(
				'nickname' => \htmlspecialchars($fields['nickname']),
				'email' => \htmlspecialchars($fields['email']),
				'ipaddress' => \app\Server::client_ip(),
				'pwdverifier' => $pwd['verifier'],
				'pwdsalt' => $pwd['salt'],
				'pwdalgorythm' => $pwd['algorythm'],
				'pwddate' => \date('Y-m-d H:i:s'),
				'active' => $fields['active'],
				'last_signin' => \date('Y-m-d H:i:s'),
			);

		static::injectfields($filtered_fields, $fields);

		$fieldnames = static::filteredfieldnames();

		static::inserter
			(
				$filtered_fields,
				$fieldnames['strs'],
				$fieldnames['bools'],
				$fieldnames['nums']
			)
			->run();

		// resolve dependencies
		$user = static::$last_inserted_id = \app\SQL::last_inserted_id();
		static::dependencies(static::$last_inserted_id, \app\CFS::config('model/user'));

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
					(user, role) VALUES (:user, :role)
				',
				'mysql'
			)
			->num(':user', $id)
			->num(':role', \app\Model_Role::by_name(\app\CFS::config('mjolnir/auth')['signup.default.role']))
			->run();
	}

	/**
	 * ...
	 */
	static function update_process($id, array $fields)
	{
		// update role
		static::assign_role($id, $fields['role']);
		static::updater($id, $fields, ['nickname', 'email'], ['active'])->run();
		static::clear_entry_cache($id);
	}

	// ------------------------------------------------------------------------
	// Collection

	/**
	 * @return string
	 */
	static function extraselectfields()
	{
		return '';
	}

	/**
	 * @return string
	 */
	static function extraselectjoins()
	{
		return ''; # no joins
	}

	/**
	 * @return array
	 */
	static function select_entries(array $entries = null)
	{
		if (empty($entries))
		{
			return [];
		}

		$cache_key = __FUNCTION__.'__entries'.\implode(',', $entries);

		return static::stash
			(
				__METHOD__,
				'
					SELECT entry.id,
					       entry.nickname,
						   entry.email,
						   entry.last_signin,
						   entry.timestamp,
						   entry.ipaddress,
						   entry.active,
						   '.static::extraselectfields().'
					       role.id role,
					       role.title roletitle

					  FROM :table entry

					  JOIN `'.static::assoc_roles().'` assoc_roles
						ON assoc_roles.user = entry.id

					  JOIN `'.static::roles_table().'` role
						ON assoc_roles.role = role.id

					  '.static::extraselectjoins().'

					 WHERE entry.`'.static::unique_key().'` IN ('.\implode(', ', $entries).')
				'
			)
			->key($cache_key)
			->fetch_all(static::fieldformat());
	}

	/**
	 * @return array
	 */
	static function entries($page, $limit, $offset = 0, $order = null, $constraints = null)
	{
		if (empty($order))
		{
			$order = ['id' => 'asc'];
		}

		return static::stash
			(
				__METHOD__,
				'
					SELECT entry.id,
					       entry.nickname,
						   entry.email,
						   entry.last_signin,
						   entry.timestamp,
						   entry.ipaddress,
						   entry.active,
						   '.static::extraselectfields().'
					       role.id role,
					       role.title roletitle

					  FROM :table entry

					  JOIN `'.static::assoc_roles().'` assoc_roles
						ON assoc_roles.user = entry.id

					  JOIN `'.static::roles_table().'` role
						ON assoc_roles.role = role.id

					  '.static::extraselectjoins().'
				'
			)
			->key(__FUNCTION__)
			->page($page, $limit, $offset)
			->order($order)
			->constraints($constraints)
			->fetch_all(static::fieldformat());
	}

	/**
	 * @return boolean
	 */
	protected static function nullentry_for_current_user(&$entry, $id)
	{
		return $entry === null
			&& \app\Auth::role() !== \app\Auth::Guest
			&& $id === \app\Auth::id();
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
						SELECT entry.*,
						       '.static::extraselectfields().'
							   assoc.role role,
							   role.title roletitle
						  FROM :table entry

						  JOIN `'.static::assoc_roles().'` assoc
							ON entry.id = assoc.user

						  JOIN `'.static::roles_table().'` role
							ON role.id = assoc.role

						  '.static::extraselectjoins().'

						 WHERE entry.id = :id
					',
					'mysql'
				)
				->num(':id', $id)
				->run()
				->fetch_entry(static::fieldformat());

			if (static::nullentry_for_current_user($entry, $id))
			{
				\app\Auth::signout();
				\app\Server::redirect(\app\Server::url_frontpage());
				exit(1);
			}

			\app\Stash::store($stashkey, $entry, \app\Stash::tags(\get_called_class(), ['change']));
		}

		return $entry;
	}

	// -------------------------------------------------------------------------
	// Extended


	/**
	 * @param array (identification, email, provider)
	 * @return \mjolnir\types\Validator
	 */
	static function inferred_signup_check(array $fields)
	{
		return \app\Validator::instance($fields)
			->rule(['identification', 'email', 'role', 'provider'], 'not_empty');
	}

	/**
	 * @param array fields
	 */
	static function inferred_signup_process(array $fields)
	{
		$fields['ipaddress'] = \app\Server::client_ip();
		$fields['nickname'] = \str_replace('@', '[at]', $fields['identification']);

		// most providers are pretty bad at passing a sensible username; so we have
		// to do some really burtish processing on it
		$fields['nickname'] = \preg_replace('[\. ]', '-', \preg_replace('#[^-a-zA-Z0-9_\. ]#', '', \trim($fields['nickname'])));

		$fields['active'] = true;
		static::inserter($fields, ['nickname', 'email', 'ipaddress', 'provider'], ['active'])->run();
		$user = static::$last_inserted_id = \app\SQL::last_inserted_id();

		// assign role if set
		if (isset($fields['role']))
		{
			static::assign_role($user, $fields['role']);
		}
	}

	/**
	 * @param array (identification, email, provider)
	 * @return \mjolnir\types\Validator|null
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
	 * @return \mjolnir\types\Validator|null
	 */
	static function change_passwords_check($user, array $fields)
	{
		$user_config = \app\CFS::config('model/user');

		return \app\Validator::instance($fields)
			->adderrormessages($user_config['errors'])
			->rule('password', 'not_empty')
			->rule('verifier', 'equal_to', $fields['verifier'] == $fields['password']);
	}

	/**
	 * @return \mjolnir\types\Validator|null
	 */
	static function change_password($user, array $fields)
	{
		isset($fields['verifier']) or $fields['verifier'] = $fields['password'];

		$errors = static::change_passwords_check($user, $fields)->errors();

		if (empty($errors))
		{
			\app\SQL::begin();
			try
			{
				// compute password
				$pwd = \app\Password::generate($fields['password']);

				$new_fields = array
					(
						'pwdverifier' => $pwd['verifier'],
						'pwdsalt' => $pwd['salt'],
						'pwdalgorythm' => $pwd['algorythm'],
					);

				static::updater
					(
						$user,
						$new_fields,
						[
							'pwdverifier',
							'pwdsalt',
							'pwdalgorythm'
						]
					)
					->run();

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

	// ------------------------------------------------------------------------
	// etc

	/**
	 * Update last_signin field to current time or specified time (string) if
	 * provided.
	 */
	static function update_last_singin($id, \DateTime $datetime = null)
	{
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET last_signin = :date
					 WHERE id = :id
				'
			)
			->date(':date', $datetime === null ? \date('Y-m-d H:i:s') : $datetime->format('Y-m-d H:i:s'))
			->num(':id', $id)
			->run();
	}

	/**
	 * ...
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
			->num(':user', $id)
			->run()
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
				->num(':user', $id)
				->num(':role', $role)
				->run();
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
				->num(':role', $role)
				->num(':user', $id)
				->run();
		}

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

	/**
	 * @param array fields
	 */
	static function recompute_password(array $fields)
	{
		$pwd = \app\Password::generate($fields['password']);

		// update
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET pwdverifier = :pwdverifier,
					       pwdsalt = :pwdsalt,
						   pwdalgorythm = :pwdalgorythm,
					       pwddate = :pwddate,
					       ipaddress = :ipaddress
					 WHERE nickname = :nickname
					   AND provider IS NULL
				',
				'mysql'
			)
			->str(':pwdverifier', $pwd['verifier'])
			->str(':pwdsalt', $pwd['salt'])
			->str(':pwdalgorythm', $pwd['algorythm'])
			->str(':pwddate', \date('Y-m-d H:i:s'))
			->str(':nickname', $fields['nickname'])
			->str(':ipaddress', \app\Server::client_ip())
			->run();
	}

	/**
	 * @return array or null
	 */
	static function detect_identity(array $fields)
	{
		if (\strpos($fields['identity'], '@') === false)
		{
			$user = static::statement
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

						 WHERE user.nickname = :nickname
						   AND user.provider IS NULL
						   AND user.`locked` = FALSE
						 LIMIT 1
					',
					'mysql'
				)
				->bindstr(':nickname', $fields['identity'])
				->run()
				->fetch_entry(static::fieldformat());
		}
		else # email
		{
			$user = static::statement
				(
					__METHOD__.':email_signin_check',
					'
						SELECT user.*,
							   assoc.role role,
							   role.title roletitle
						  FROM :table user

						  JOIN `'.static::assoc_roles().'` assoc
							ON user.id = assoc.user

						  JOIN `'.static::roles_table().'` role
							ON role.id = assoc.role

						 WHERE email = :email
						   AND provider IS NULL
						   AND `locked` = FALSE
						 LIMIT 1
					',
					'mysql'
				)
				->bindstr(':email', $fields['identity'])
				->run()
				->fetch_entry(static::fieldformat());

			if ($user === null)
			{
				// check secondary emails
				$entry = \app\Model_SecondaryEmail::find_entry(['email' => $fields['identity']]);

				if ($entry !== null)
				{
					$user = static::entry($entry['user']);
				}
			}
		}

		return $user;
	}

	/**
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
				->num(':user', $user_id)
				->run()
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
						SELECT user.*,
							   assoc.role role,
							   role.title roletitle
						  FROM :table user

						  JOIN `'.static::assoc_roles().'` assoc
							ON user.id = assoc.user

						  JOIN `'.static::roles_table().'` role
							ON role.id = assoc.role

						 WHERE email = :email
						   AND `locked` = FALSE
						   AND `active` = TRUE
						 LIMIT 1
					',
					'mysql'
				)
				->str(':email', $email)
				->run()
				->fetch_all();

			if (empty($result))
			{
				$entry = \app\Model_SecondaryEmail::find_entry(['email' => $email]);

				if ($entry !== null)
				{
					$result = $entry['user'];
				}
				else # no secondary email match
				{
					$result = null;
				}
			}
			else # not empty result
			{
				$result = $result[0]['id'];
			}

			\app\Stash::store
				(
					$cachekey,
					$result,
					\app\Stash::tags(\get_called_class(), ['change'])
				);
		}

		return $result;
	}

	/**
	 * Sends the activation email with a token for activating the account; if
	 * the account is not activated the user will be blocked on signin, but
	 * otherwise the account will behave normally.
	 *
	 * This function logs failed attempts. Status is passed for any additional
	 * processing.
	 *
	 * @return boolean sent?
	 */
	static function send_activation_email($user_id)
	{
		$key = \app\Model_User::token($user_id, '+7 days', 'mjolnir:signup');
		$confirm_email_url = \app\CFS::config('mjolnir/auth')['default.signup'].'?user='.$user_id.'&key='.$key;

		$user = static::entry($user_id);

		// send code via email
		$sent = \app\Email::instance()
			->send
			(
				$user['email'],
				null,
				\app\Lang::term('Confirmation of Email Ownership'),
				\app\Lang::key
					(
						'mjolnir:access/email-activate-account',
						[
							':token_url' => $confirm_email_url,
							':nickname' => $user['nickname'],
						]
					),
				true # is html
			);

		if ( ! $sent)
		{
			\mjolnir\log('Emails', 'Failed to send activation email for ['.$user_id.']');
		}

		return $sent;
	}

	/**
	 * Set account to [active] state, allowing user to login. Until account is
	 * active the user won't be able to login into it.
	 */
	static function activate_account($user_id)
	{
		static::statement
			(
				__METHOD__,
				'
					UPDATE `'.static::table().'`
					   SET active = TRUE
					 WHERE id = :user_id
				'
			)
			->num(':user_id', $user_id)
			->run();

		$user = \app\Model_User::entry($user_id);

		// close all other accounts with this email
		static::autolock_for_email($user['email'], $user_id);

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

	/**
	 * Add secondary email. If a user with the same email already exists, the
	 * account will be locked.
	 */
	static function add_secondary_email($user_id, $email)
	{
		static::autolock_for_email($email, $user_id);

		$entry = \app\Model_SecondaryEmail::find_entry
			(
				[
					'user' => $user_id,
					'email' => $email
				]
			);

		// check if entry doesn't exist
		if ($entry === null)
		{
			$errors = \app\Model_SecondaryEmail::push
				(
					[
						'email' => $email,
						'user' => $user_id,
					]
				);

			if ($errors !== null)
			{
				throw new \Exception('Failed to add secondary email.');
			}
		}
	}

	/**
	 * Changes the email for the current user. All other users with the same
	 * email will have their accounts locked.
	 */
	static function change_email($user_id, $email)
	{
		static::autolock_for_email($email, $user_id);

		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET `email` = :email
					 WHERE `id` = :id
				'
			)
			->str(':email', $email)
			->num(':id', $user_id)
			->run();

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

	/**
	 * If a user has the email as a main email or as a secondary email the
	 * account will be locked; the context is used to specify an exception user
	 * for exception (errornous) input.
	 */
	protected static function autolock_for_email($email, $context = null)
	{
		// search for main emails
		$entries = static::statement
			(
				__METHOD__,
				'
					SELECT id
					  FROM :table
					 WHERE `email` = :email
					   AND NOT id <=> :context
					   AND `locked` = FALSE
				'
			)
			->str(':email', $email)
			->num(':context', $context)
			->run()
			->fetch_all();

		foreach ($entries as $entry)
		{
			static::lock($entry['id']);
		}

		// search for secondary emails
		$secondary_emails = static::statement
			(
				__METHOD__,
				'
					SELECT id
					  FROM `'.\app\Model_SecondaryEmail::table().'`
					 WHERE `email` = :email
					   AND NOT user <=> :context
				'
			)
			->str(':email', $email)
			->num(':context', $context)
			->run()
			->fetch_all();

		foreach ($secondary_emails as $entry)
		{
			static::lock($entry['user']);
		}

		// reset cache
		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

	/**
	 * Lock account, prevent access to it. Inspection functions will still work
	 * but authorization functions will ignore the account.
	 */
	static function lock($user_id)
	{
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET `locked` = TRUE
					 WHERE `id` = :id
				'
			)
			->num(':id', $user_id)
			->run();

		static::purgetoken($user_id);

		// remove all associated secondary emails
		\app\Model_SecondaryEmail::purge_for($user_id);

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
	}

	/**
	 * Password attempts are incremented by 1.
	 */
	static function bump_pwdattempts($user)
	{
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET pwdattempts = pwdattempts + 1
					 WHERE id = :user
				'
			)
			->num(':user', $user)
			->run();
	}

	/**
	 * Password attempts are reset to 0.
	 */
	static function reset_pwdattempts($user)
	{
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET pwdattempts = 0
					 WHERE id = :user
				'
			)
			->num(':user', $user)
			->run();
	}

	/**
	 * @return string new password reset key
	 */
	static function pwdreset_key($user)
	{
		$user_entry = static::entry($user);

		// load configuration
		$security = \app\CFS::config('mjolnir/security');

		// pwdreset_key = hash(hash(salt, nans), apikey)
		$key = \hash_hmac($security['hash']['algorythm'], \uniqid(\rand(), true), $user_entry['pwdsalt'], false);
		$pwdreset_key = \hash_hmac($security['hash']['algorythm'], $key, $security['keys']['apikey'], false);

		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET `pwdreset` = :key,
					       `pwdreset_expires` = :expires
					 WHERE `id` = :user
				'
			)
			->num(':user', $user)
			->date(':expires', \date_create('+3 hours')->format('Y-m-d H:i:s'))
			->str(':key', $pwdreset_key)
			->run();

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
		\app\Model_User::clear_entry_cache($user);

		return $pwdreset_key;
	}

	/**
	 * @return array of errors or null on success
	 */
	static function pwdreset($user, $key, $password)
	{
		$entry = static::entry($user);

		// verify pwd reset key and that reset has not expired
		if ($entry['pwdreset'] !== $key)
		{
			return [ \app\Lang::term('Invalid password reset key. Please repeat the process.') ];
		}
		elseif ($entry['pwdreset_expires'] < \date_create('now'))
		{
			return [ \app\Lang::term('Password reset has expired. Please repeat the process.') ];
		}

		$pwd = \app\Password::generate($password);

		// update
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET pwdverifier = :pwdverifier,
					       pwdsalt = :pwdsalt,
						   pwdalgorythm = :pwdalgorythm,
					       pwddate = :pwddate,
					       ipaddress = :ipaddress,
						   pwdreset = NULL,
						   pwdreset_expires = NULL
					 WHERE id = :user
				',
				'mysql'
			)
			->str(':pwdverifier', $pwd['verifier'])
			->str(':pwdsalt', $pwd['salt'])
			->str(':pwdalgorythm', $pwd['algorythm'])
			->str(':pwddate', \date('Y-m-d H:i:s'))
			->num(':user', $entry['id'])
			->str(':ipaddress', \app\Server::client_ip())
			->run();

		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
		\app\Model_User::clear_entry_cache($user);

		return null;
	}

	// -------------------------------------------------------------------------
	// Validator Helpers

	/**
	 * Confirm password matches user.
	 *
	 * @return boolean
	 */
	static function matching_password($password, $user)
	{
		$cachekey = __METHOD__.'__userinfo_'.$user;
		$entry = \app\Stash::get($cachekey, null);

		if ($entry === null)
		{
			// get user data
			$entry = static::statement
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
				->num(':user', $user)
				->run()
				->fetch_entry();

			\app\Stash::store
				(
					$cachekey,
					$entry,
					\app\Stash::tags('User', ['change'])
				);
		}

		return \app\Password::match
			(
				$password,
				$entry['verifier'],
				$entry['salt'],
				$entry['algorythm']
			);
	}

} # class

