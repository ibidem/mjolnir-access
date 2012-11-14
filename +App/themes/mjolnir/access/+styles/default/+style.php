<?php return array
	(
		'version' => '1.0', # used in cache busting; update as necesary

		// set the style.root to '' (empty string) when writing (entirely) just
		// plain old css files; and not compiling sass scripts, etc
		'style.root' => 'root'.DIRECTORY_SEPARATOR,

		// common files
		'common' => array
			(
				'unsorted',
			),

		// mapping targets to files
		'targets' => array
			(
				'signin' => array
					(
						// empty
					),

				'lobby' => array
					(
						// empty
					),

				'signup' => array
					(
						// empty
					),

				'pwdreset' => array
					(
						// empty
					),
			
				'emails' => array
					(
						// empty
					),

				'exception-NotFound' => array
					(
						// empty
					),
				'exception-NotAllowed' => array
					(
						// empty
					),
				'exception-NotApplicable' => array
					(
						// empty
					),
				'exception-Unknown' => array
					(
						// empty
					),
			),
	);

