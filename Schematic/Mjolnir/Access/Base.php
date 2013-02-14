<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Schematic_Mjolnir_Access_Base extends \app\Instantiatable implements \mjolnir\types\Schematic
{
	use \app\Trait_Schematic;

	/**
	 * ...
	 */
	function down()
	{
		\app\Schematic::destroy
			(
				\app\Model_User::table(),
				\app\Model_Role::table(),
				\app\Model_User::assoc_roles(),
				\app\Model_ProfileField::table(),
				\app\Model_ProfileField::assoc_user(),
				\app\Model_UserSigninToken::table(),
				\app\Model_SecurityToken::table(),
				\app\Model_SecondaryEmail::table()
			);
	}

	/**
	 * ...
	 */
	function up()
	{
		\app\Schematic::table
			(
				\app\Model_User::table(),
				'
					`id`          :key_primary,
					`token`       :key_foreign,

					`nickname`    :username,
					`email`       :email,
					`ipaddress`   :ipaddress,

					`pwdverifier` :secure_hash DEFAULT NULL,
					`pwdsalt`     :secure_hash DEFAULT NULL,
					`pwddate`     :datetime_optional DEFAULT NULL,
					`pwdattempts` int DEFAULT 0,

					`provider`    :titlename DEFAULT NULL,
					`timestamp`   :timestamp,
					`locked`      :boolean DEFAULT FALSE,
					`active`      :boolean DEFAULT FALSE,

					`pwdreset`         :secure_hash DEFAULT NULL,
					`pwdreset_expires` :datetime_optional DEFAULT NULL,
					`last_signin`      :datetime_optional DEFAULT NULL,

					PRIMARY KEY (`id`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_Role::table(),
				'
					`id`    :key_primary,
					`title` :title NOT NULL,

					PRIMARY KEY (`id`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_User::assoc_roles(),
				'
					`user` :key_foreign NOT NULL,
					`role` :key_foreign NOT NULL,

					KEY `user` (`user`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_ProfileField::table(),
				'
					`id`       :key_primary,
					`idx`      :counter DEFAULT 10,
					`name`     :title NOT NULL,
					`title`    :title NOT NULL,
					`type`     :title NOT NULL,
					`required` :boolean DEFAULT 0,

					PRIMARY KEY (`id`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_ProfileField::assoc_user(),
				'
					`user`  :key_foreign NOT NULL,
					`field` :key_foreign NOT NULL,
					`value` :block,

					KEY `user` (`user`,`field`),
					KEY `role` (`field`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_UserSigninToken::table(),
				'
					`user`  :key_foreign NOT NULL,
					`token` :secure_hash,

					UNIQUE `user` (`user`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_SecurityToken::table(),
				'
					`id`      :key_primary,
					`token`   :secure_hash,
					`purpose` varchar(255),
					`expires` :datetime_required,

					PRIMARY KEY (`id`)
				'
			);

		\app\Schematic::table
			(
				\app\Model_SecondaryEmail::table(),
				'
					`id`    :key_primary,
					`user`  :key_foreign,
					`email` :email,

					PRIMARY KEY (`id`)
				'
			);
	}

	/**
	 * ...
	 */
	function bind()
	{
		\app\Schematic::constraints
			(
				[
					\app\Model_User::table() => array
						(
							'token' => [ \app\Model_SecurityToken::table(), 'SET NULL', 'CASCADE' ],
						),
					\app\Model_User::assoc_roles() => array
						(
							'user'  => [ \app\Model_User::table(), 'CASCADE', 'CASCADE' ],
							'role'  => [ \app\Model_Role::table(), 'CASCADE', 'CASCADE' ],
						),
					\app\Model_ProfileField::assoc_user() => array
						(
							'field' => [ \app\Model_ProfileField::table(), 'CASCADE', 'CASCADE' ],
							'user'  => [ \app\Model_User::table(), 'CASCADE', 'CASCADE' ],
						),
					\app\Model_UserSigninToken::table() => array
						(
							'user' => [ \app\Model_User::table(), 'CASCADE', 'CASCADE' ],
						),
					\app\Model_SecondaryEmail::table() => array
						(
							'user' => [ \app\Model_User::table(), 'CASCADE', 'CASCADE' ],
						),
				]
			);
	}

	/**
	 * ...
	 */
	function build()
	{
		// inject roles
		$access_config = \app\CFS::config('mjolnir/access');
		$roles = $access_config['roles'];
		if ( ! empty($roles))
		{
			$id = null;
			$title = null;
			$statement = \app\SQL::prepare
				(
					__METHOD__,
					'
						INSERT INTO `'.\app\Model_Role::table().'`
							(id, title) VALUES (:id, :title)
					',
					'mysql'
				)
				->bindnum(':id', $id)
				->bindstr(':title', $title);

			\app\SQL::begin();
			try
			{
				foreach ($roles as $desired_title => $desired_id)
				{
					$title = $desired_title;
					$id = $desired_id;
					$statement->run();
				}

				\app\SQL::commit();
			}
			catch(\Exception $e)
			{
				\app\SQL::rollback();
				throw $e;
			}
		}
		
		// inject openid providers
		$providers = \app\CFS::config('mjolnir/auth')['signin'];
		foreach ($providers as $provider)
		{
			\app\Register::inject($provider['register'], 'off');
		}
	}
	
} # class
