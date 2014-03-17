<?php return array
	(
		// please place 'require' inside the main paradox configuration file
		// this file should only contain information required by the operation

		'description'
			=> 'Install for User, Role (along with User -> Role association), '
			.  'Profile fields, Security Tokens, and Secondary Emails support.'
			,

		'configure' => array
			(
				'tables' => array
					(
						\app\Model_User::table(),
						\app\Model_Role::table(),
						\app\Model_User::assoc_roles(),
						\app\Model_ProfileField::table(),
						\app\Model_ProfileField::assoc_user(),
						\app\Model_SecurityToken::table(),
						\app\Model_SecondaryEmail::table(),
					),
			),

		'tables' => array
			(
				\app\Model_User::table() =>
					'
						`id`           :key_primary,
						`token`        :key_foreign,

						`nickname`     :username,
						`email`        :email,
						`ipaddress`    :ipaddress,

						`pwdalgorythm` :title,
						`pwdverifier`  :secure_hash DEFAULT NULL,
						`pwdsalt`      :secure_hash DEFAULT NULL,
						`pwddate`      :datetime_optional DEFAULT NULL,
						`pwdattempts`  int DEFAULT 0,

						`provider`     :titlename DEFAULT NULL,
						`timestamp`    :timestamp,
						`locked`       :boolean DEFAULT FALSE,
						`active`       :boolean DEFAULT FALSE,

						`pwdreset`         :secure_hash DEFAULT NULL,
						`pwdreset_expires` :datetime_optional DEFAULT NULL,
						`last_signin`      :datetime_optional DEFAULT NULL,

						PRIMARY KEY (`id`)
					',
				\app\Model_Role::table() =>
					'
						`id`    :key_primary,
						`title` :title NOT NULL,

						PRIMARY KEY (`id`)
					',
				\app\Model_User::assoc_roles() =>
					'
						`user` :key_foreign NOT NULL,
						`role` :key_foreign NOT NULL,

						KEY `user` (`user`)
					',
				\app\Model_ProfileField::table() =>
					'
						`id`       :key_primary,
						`idx`      :counter DEFAULT 10,
						`name`     :title NOT NULL,
						`title`    :title NOT NULL,
						`type`     :title NOT NULL,
						`required` :boolean DEFAULT 0,

						PRIMARY KEY (`id`)
					',
				\app\Model_ProfileField::assoc_user() =>
					'
						`user`  :key_foreign NOT NULL,
						`field` :key_foreign NOT NULL,
						`value` :block,

						KEY `user` (`user`,`field`),
						KEY `role` (`field`)
					',
				\app\Model_SecurityToken::table() =>
					'
						`id`      :key_primary,
						`token`   :secure_hash,
						`purpose` varchar(255),
						`expires` :datetime_required,

						PRIMARY KEY (`id`)
					',
				\app\Model_SecondaryEmail::table() =>
					'
						`id`    :key_primary,
						`user`  :key_foreign,
						`email` :email,

						PRIMARY KEY (`id`)
					',
			),

		'bindings' => array
			(
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
				\app\Model_SecondaryEmail::table() => array
					(
						'user' => [ \app\Model_User::table(), 'CASCADE', 'CASCADE' ],
					),
			),

		'populate' => function (\mjolnir\types\SQLDatabase $db, array $state)
			{
				// inject roles
				$access_config = \app\CFS::config('mjolnir/access');
				$roles = $access_config['roles'];
				if ( ! empty($roles))
				{
					$id = null;
					$title = null;
					$statement = $db->prepare
						(
							'
								INSERT INTO `'.\app\Model_Role::table().'`
									(id, title) VALUES (:id, :title)
							'
						)
						->bindnum(':id', $id)
						->bindstr(':title', $title);

					$db->begin();
					try
					{
						foreach ($roles as $desired_title => $desired_id)
						{
							$title = $desired_title;
							$id = $desired_id;
							$statement->run();
						}

						$db->commit();
					}
					catch(\Exception $e)
					{
						$db->rollback();
						throw $e;
					}
				}

				// inject openid providers
				$providers = \app\CFS::config('mjolnir/auth')['signin'];
				foreach ($providers as $provider)
				{
					\app\Register::inject($provider['register'], 'off');
				}
			},

	); # config