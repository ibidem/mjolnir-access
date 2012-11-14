<?php return array
	(
		'version' => '1.0', # used in cache busting; update as necesary

		// set the script.root to '' (empty string) when writing (entirely) just
		// plain old js files; and not compiling coffee scripts, etc
		'script.root' => '',

		// will be included in all explicity targets; if a target needs to be
		// script free then simply ommit it in the targets declaration bellow
		'common' => array
			(
				'lib/plugins/jquery-1.7.2',
				'lib/twitter/bootstrap'
			),

		// mapping targets to files
		'targets' => array
			(
				'signin' => array
					(
						// empty; loads common
					),
				'singup' => array
					(
						// empty; loads common
					),
				'lobby' => array
					(
						// empty; loads common
					),
				'pwdreset' => array
					(
						// empty; loads common
					),
				'emails' => array
					(
						// empty; loads common
					),
			),
	);

