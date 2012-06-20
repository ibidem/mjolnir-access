<?php return array
	(
		'dependencies' => array
			(
			
			),
		'fields' => array
			(
				'title' => array
					(
						'maxlength' => 70,
						'size' => 30,
					),
			),
		'errors' => array
			(
				'title' => array
					(
						'not_empty' => 'Invalid value.',
						'\app\Model_DB_User::unique_role' => 'Role already exists.',
					),
			),
	);
