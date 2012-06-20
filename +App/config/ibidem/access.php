<?php return array
	(
		'whitelist' => array # allow
			(
				\app\A12n::guest() => array
					(
						\app\Protocol::instance()
							->relays(['\ibidem\access\a12n'])
							->all_parameters(),
					),
			
				// the following role acts as a template, include it in the
				// alias list of your own administrator role
				'+admin' => array
					(
						\app\Protocol::instance()
							->relays(['\ibidem\backend'])
							->attributes
								(
									[
										'user-manager', 
										'user-edit',
										'user-roles'
									]
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
				# 'member' => array(\app\A12n::guest()),
				# 'admin' => array(\app\A12n::guest(), 'member'),
			),
		'roles' => array # roles in system
			(
				// empty
			),
	);
