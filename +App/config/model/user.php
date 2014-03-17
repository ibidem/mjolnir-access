<?php return array
	(
		'signup' => array
			(
				'role' => 1
			),
		'dependencies' => array
			(
				// empty
			),
		'fieldformat' => array
			(
				'pwddate' => 'datetime',
				'pwdreset_expires' => 'datetime',
				'timestamp' => 'datetime',
				'last_signin' => 'datetime',
			),
		'fields' => array
			(
				'nickname' => array
					(
						'maxlength' => 80,
						'size' => 30,
					),
				'email' => array
					(
						'maxlength' => 254,
						'size' => 30,
					),
				'password' => array
					(
						'minlength' => 8,
						'size' => 60,
					),
			),
		'errors' => array
			(
				'nickname' => array
					(
						'not_empty' => 'You must type in a username.',
						'not-empty' => 'You must type in a username.',
						'max_length' => 'Please choose a shorter account name.',
						':unique' => 'User with same name already exists.',
					),
				'password' => array
					(
						'not_empty' => 'Password is required.',
						'not-empty' => 'Password is required.',
						'min_length' => 'Your password is too short.',
						'\app\UserLib::matching_password' => 'Passwords do not match.',
					),
				'verifier' => array
					(
						'equal_to' => 'Passwords do not match',
					),
				'email' => array
					(
						':unique' => 'User with the same email already exists.',
					),
			),

	); # config
