<?php return array
	(
		'whitelist' => array # allow
			(
				\app\A12n::guest() => array
					(
						// sandbox testcase
						\app\Protocol::instance()
							->relays(array('!sandbox'))
							->param('action', array('test', 'testing', 'index'))
							->attributes(array('cabage')),
						// sandbox is accessible by everybody
						\app\Protocol::instance()
							->relays(array('!sandbox')),
					),
				'member' => array
					(
						// empty
					),
				'admin' => array
					(
						// empty
					),
			),
		'blacklist' => array # disallow! (no matter what)
			(
				// empty
			),
		'aliaslist' => array # alias list
			(
				/**
				* If something is allowed for the alias it will be allowed for the
				* permission category as well. Does not apply for exceptions. If
				* there is an exception for an alias the exception will not apply 
				* for the permission category.
				*/
				'member' => array(\app\A12n::guest()),
				'admin' => array(\app\A12n::guest(), 'member'),
			)
	);
