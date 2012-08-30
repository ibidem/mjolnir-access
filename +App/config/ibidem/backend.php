<?php return array
	(
		// see: http://fortawesome.github.com/Font-Awesome/ for icon reference
	
		'General' => array
			(
				'user-profile-index' => array
					(
						'icon' => 'reorder',
						'title' => 'Profile Fields',
						'context' => '\app\Backend_ProfileField',
						'view' => 'ibidem/access/backend/profile-index'
					),
			
				'user-profile-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit Fields',
						'context' => '\app\Backend_ProfileField',
						'view' => 'ibidem/access/backend/profile-edit'
					),
			),
	
		'Access' => array
			(
				'user-index' => array
					(
						'icon' => 'user',
						'title' => 'User Manager',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/backend/user-index'
					),
				'user-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit User',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/backend/user-edit'
					),
				'user-edit-profile' => array
					(
						'hidden' => true,
						'title' => 'Edit User Profile',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/backend/user-edit-profile'
					),
				'user-profile' => array
					(
						'hidden' => true,
						'title' => 'View Profile',
						'context' => '\app\Backend_User',
						'view' => 'ibidem/access/backend/user-profile'
					),
				'user-role-index' => array
					(
						'icon' => 'key',
						'title' => 'User Roles',
						'context' => '\app\Backend_Role',
						'view' => 'ibidem/access/backend/role-index'
					),
				'user-role-edit' => array
					(
						'hidden' => true,
						'title' => 'Edit Role',
						'context' => '\app\Backend_Role',
						'view' => 'ibidem/access/backend/role-edit'
					),
				'user-access-settings' => array
					(
						'icon' => 'edit',
						'title' => 'Settings',
						'context' => '\app\Backend_Settings',
						'view' => 'ibidem/access/backend/settings'
					),
			),
			
	);