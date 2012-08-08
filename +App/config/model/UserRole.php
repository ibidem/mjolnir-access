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
						':unique' => 'Role already exists.',
					),
			),
	);
