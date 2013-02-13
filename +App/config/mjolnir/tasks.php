<?php 

	$random_email = \app\Mockup::rand
		(
			[
				'a@nobody.me',
				'b@nobody.me',
				'c@nobody.me',
				'd@nobody.me',
				'e@nobody.me',
				'f@nobody.me',
				'g@nobody.me',
				'h@nobody.me',
				'i@nobody.me',
				'j@nobody.me',
				'k@nobody.me',
				'l@nobody.me',
				'm@nobody.me',
				'n@nobody.me',
				'o@nobody.me',
				'p@nobody.me',
				'r@nobody.me',
				's@nobody.me',
				't@nobody.me',
				'u@nobody.me',
				'v@nobody.me',
				'w@nobody.me',
				'x@nobody.me',
				'y@nobody.me',
				'z@nobody.me',
			]
		)->render();

return array
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
								'default' => $random_email,
							),
					),
			),
	
		'make:user' => array
			(
				'category' => 'Access',
				'description' => array
					(
						'Create user with a given role.',
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
								'default' => $random_email,
							),
					),
			),
	);
