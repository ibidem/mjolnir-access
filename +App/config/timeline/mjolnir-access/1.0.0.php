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
						\app\UserLib::table(),
						\app\RoleLib::table(),
						\app\UserLib::assoc_roles(),
						\app\ProfileFieldLib::table(),
						\app\ProfileFieldLib::assoc_user(),
						\app\SecurityTokenLib::table(),
						\app\SecondaryEmailLib::table(),
					),
			),

		'tables' => array
			(
				\app\UserLib::table() =>
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
				\app\RoleLib::table() =>
					'
						`id`    :key_primary,
						`title` :title NOT NULL,

						PRIMARY KEY (`id`)
					',
				\app\UserLib::assoc_roles() =>
					'
						`user` :key_foreign NOT NULL,
						`role` :key_foreign NOT NULL,

						KEY `user` (`user`)
					',
				\app\ProfileFieldLib::table() =>
					'
						`id`       :key_primary,
						`idx`      :counter DEFAULT 10,
						`name`     :title NOT NULL,
						`title`    :title NOT NULL,
						`type`     :title NOT NULL,
						`required` :boolean DEFAULT 0,

						PRIMARY KEY (`id`)
					',
				\app\ProfileFieldLib::assoc_user() =>
					'
						`user`  :key_foreign NOT NULL,
						`field` :key_foreign NOT NULL,
						`value` :block,

						KEY `user` (`user`,`field`),
						KEY `role` (`field`)
					',
				\app\SecurityTokenLib::table() =>
					'
						`id`      :key_primary,
						`token`   :secure_hash,
						`purpose` varchar(255),
						`expires` :datetime_required,

						PRIMARY KEY (`id`)
					',
				\app\SecondaryEmailLib::table() =>
					'
						`id`    :key_primary,
						`user`  :key_foreign,
						`email` :email,

						PRIMARY KEY (`id`)
					',
			),

		'bindings' => array
			(
				\app\UserLib::table() => array
					(
						'token' => [ \app\SecurityTokenLib::table(), 'SET NULL', 'CASCADE' ],
					),
				\app\UserLib::assoc_roles() => array
					(
						'user'  => [ \app\UserLib::table(), 'CASCADE', 'CASCADE' ],
						'role'  => [ \app\RoleLib::table(), 'CASCADE', 'CASCADE' ],
					),
				\app\ProfileFieldLib::assoc_user() => array
					(
						'field' => [ \app\ProfileFieldLib::table(), 'CASCADE', 'CASCADE' ],
						'user'  => [ \app\UserLib::table(), 'CASCADE', 'CASCADE' ],
					),
				\app\SecondaryEmailLib::table() => array
					(
						'user' => [ \app\UserLib::table(), 'CASCADE', 'CASCADE' ],
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
								INSERT INTO `'.\app\RoleLib::table().'`
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