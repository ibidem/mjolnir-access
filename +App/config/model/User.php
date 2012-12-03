<?php return array
	(
		'signup' => array
			(
				'role' => 1
			),
		'dependencies' => array
			(
			
			),
		'fields' => array
			(
				'givenname' => array
					(
						'maxlength' => 70,
						'size' => 30,
					),
				'familyname' => array
					(
						'maxlength' => 70,
						'size' => 30,
					),
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
						'not_empty' => 'You must type in a nickname.',
						':unique' => 'User with same name already exists.',
					),
				'password' => array
					(
						'not_empty' => 'Password is required.',
						'min_length' => 'Your password is too short.',
						'\app\Model_User::matching_password' => 'Passwords do not match.',
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
	);
