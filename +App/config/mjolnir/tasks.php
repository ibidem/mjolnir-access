<?php return array
	(
		'make:user' => array
			(
				'category' => 'Access',
				'description' => array
					(
						'Create user with a given role.',
						'By default the email is a random string based on the current time.',
					),
				'flags' => array
					(
						'username' => array # intentionally not nickname
							(
								'description' => 'Name',
								'short' => 'u',
								'type' => 'text',
							),
						'password' => array
							(
								'description' => 'Password',
								'short' => 'p',
								'type' => 'text',
							),
						'role' => array
							(
								'description' => 'Role name.',
								'short' => 'r',
								'type' => 'text',
							),
						'email' => array
							(
								'description' => 'Email',
								'short' => 'e',
								'type' => 'text',
							// shorthand email for development purposes
								'default' => \base_convert(\time(), 10, 32).'@nobody.tld',
							),
					),
			),
		'user:password' => array
			(
				'category' => 'Access',
				'description' => array
					(
						'Change user password.',
						'The user may be mentioned via either a username or email.',
						'If both are specified the email will take precedence.'
					),
				'flags' => array
					(
						'username' => array # intentionally not nickname
							(
								'description' => 'Name',
								'short' => 'u',
								'type' => 'text',
								'default' => false,
							),
						'email' => array
							(
								'description' => 'Email',
								'short' => 'e',
								'type' => 'text',
								'default' => false,
							),
						'password' => array
							(
								'description' => 'Password',
								'short' => 'p',
								'type' => 'text',
							),
					),
			),
	);
