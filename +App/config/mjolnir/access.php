<?php namespace app; return array
/////// Access Protocol Configuration //////////////////////////////////////////
(
	'whitelist' => array # allow
		(
			Auth::Guest => array
				(
					Allow::relays
						(
							'mjolnir:access/auth.route',
							'mjolnir:access/channel.route',
							'mjolnir:access/endpoint.route'
						)
						->unrestricted(),
				),

			// the following role acts as a template, include it in the
			// alias list of your own administrator role
			'+admin' => array
				(
					Allow::relays
						(
							'mjolnir:access/auth.route'
						)
						->unrestricted(),

					Allow::backend
						(
							'user-index',
							'user-edit',
							'user-edit-profile',
							'user-role-index',
							'user-role-edit',
							'user-profile-index',
							'user-profile-edit',
							'user-profile',
							'user-access-settings'
						)
				),
		),
	'blacklist' => array # disallow! (no matter what)
		(
			// empty
		),
	'aliaslist' => array # alias list
		(
			/**
			 * If something is allowed for the alias it will be allowed for
			 * the permission category as well. Does not apply for
			 * exceptions. If there is an exception for an alias the
			 * exception will not apply for the permission category.
			 */

			// examples
			# 'member' => [ Auth::Guest ],
			# 'admin'  => [ Auth::Guest, 'member' ],
		),
	'roles' => array # roles in system
		(
			'admin' => 1,
		),

); # config
