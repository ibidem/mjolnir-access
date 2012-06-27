<?php return array
	(
		// see: http://fortawesome.github.com/Font-Awesome/ for icon reference
	
		'Access' => array
			(
				'user-index' => array
					(
						'icon' => 'user',
						'title' => 'User Manager',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/user-index'
					),
				'user-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit User',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/user-edit'
					),
				'role-index' => array
					(
						'icon' => 'hand-right',
						'title' => 'User Roles',
						'context' => '\app\Backend_Role',
						'view' => 'ibidem/access/role-index'
					),
				'role-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit Role',
						'context' => '\app\Backend_Role',
						'view' => 'ibidem/access/role-edit'
					),
			),
	);