<?php return array
	(
		'make:admin' => array
			(
				'category' => 'Access',
				'description' => array
					(
						'Create Administrator',
					),
				'flags' => array
					(
						'username' => array
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
						'email' => array
							(
								'description' => 'Email',
								'short' => 'e',
								'type' => 'text',
							// shorthand email for development purposes
								'default' => 'nobody@nowhere.me',
							),
					),
			),
	);
