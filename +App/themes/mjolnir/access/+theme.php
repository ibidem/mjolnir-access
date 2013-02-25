<?php return array
	(
		'version' => '1.0',

		'loaders' => array # null = default configuration
			(
				'style' => [ 'default.style' => 'minimalistic' ],
				'javascript' => null,
			),

		// target-to-file mapping
		'mapping' => array
			(
				'lobby' => array
					(
						'foundation/base',
						'lobby'
					),

				'signin' => array
					(
						'foundation/base',
						'signin'
					),

				'signup' => array
					(
						'foundation/base',
						'signup'
					),

				'pwdreset' => array
					(
						'foundation/base',
						'pwdreset'
					),

				'emails' => array
					(
						'foundation/base',
						'emails'
					),

			//// Exceptions ////////////////////////////////////////////////////

				'exception-NotFound' => array
					(
						'foundation/error',
						'errors/not-found'
					),
				'exception-NotAllowed' => array
					(
						'foundation/error',
						'errors/not-allowed'
					),
				'exception-NotApplicable' => array
					(
						'foundation/error',
						'errors/not-applicable'
					),
				'exception-Unknown' => array
					(
						'foundation/error',
						'errors/unknown'
					),
			),

	); # theme
