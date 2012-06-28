<?php return array
	(
		// see: http://fortawesome.github.com/Font-Awesome/ for icon reference
	
		'General' => array
			(
				'user-profile-index' => array
					(
						'icon' => 'reorder',
						'title' => 'Profile Fields',
						'context' => '\app\Backend_Profile',
						'view' => 'ibidem/access/profile-index'
					),
			
				'user-profile-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit Fields',
						'context' => '\app\Backend_Profile',
						'view' => 'ibidem/access/profile-edit'
					),
			),
	
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
				'user-edit-profile' => array
					(
						'hidden' => true,
						'title' => 'Edit User Profile',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/user-edit-profile'
					),
				'user-profile' => array
					(
						'hidden' => true,
						'title' => 'View Profile',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/user-profile'
					),
				'user-role-index' => array
					(
						'icon' => 'hand-right',
						'title' => 'User Roles',
						'context' => '\app\Backend_Role',
						'view' => 'ibidem/access/role-index'
					),
				'user-role-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit Role',
						'context' => '\app\Backend_Role',
						'view' => 'ibidem/access/role-edit'
					),
			),
			
	);