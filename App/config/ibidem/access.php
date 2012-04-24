<?php return array
	(
		'whitelist' => array # allow
			(
				\app\A12n::guest() => array
					(
						\app\Protocol::instance()
							->relays(array('\ibidem\access\a12n')),
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
