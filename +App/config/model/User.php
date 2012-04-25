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
					),
				'password' => array
					(
						'min_length' => 'Your password is too short.',
					)
			),
	);
