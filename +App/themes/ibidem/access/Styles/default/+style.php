<?php return array
	(
		'version' => '1.0', # used in cache busting; update as necesary

		// set the style.root to '' (empty string) when writing (entirely) just
		// plain old css files; and not compiling sass scripts, etc
		'style.root' => 'root'.DIRECTORY_SEPARATOR,

		// common files
		'common' => array
			(
				'lib/boilerplate', 'unsorted'
			),
	
		// mapping targets to files
		'targets' => array
			(
				'access/signin' => array
					(
						'signin'
					),
			),
	);

