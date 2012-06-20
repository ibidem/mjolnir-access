<?php return array
	(
		'Access' => array
			(
				'user-manager' => array
					(
						'title' => 'User Manager',
						'context' => '\app\Backend_Access',
						'view' => 'ibidem/access/user-manager'
					),
				'user-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit User',
						'context' => '\app\Backend_Access',
						'view' => 'ibidem/access/user-edit'
					),
				'user-roles' => array
					(
						'title' => 'User Roles',
						'context' => '\app\Backend_Access',
						'view' => 'ibidem/access/user-roles'
					),
			),
	);