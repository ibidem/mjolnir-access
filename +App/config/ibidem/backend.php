<?php return array
	(
		'Access' => array
			(
				'user-manager' => array
					(
						'title' => 'User Manager',
						'context' => '\app\Backend_UserManager',
						'view' => 'ibidem/access/user-manager'
					),
				'user-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit User',
						'context' => '\app\Backend_UserManager',
						'view' => 'ibidem/access/user-edit'
					),
				'role-manager' => array
					(
						'title' => 'User Roles',
						'context' => '\app\Backend_RoleManager',
						'view' => 'ibidem/access/role-manager'
					),
				'role-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit Role',
						'context' => '\app\Backend_RoleManager',
						'view' => 'ibidem/access/role-edit'
					),
			),
	);