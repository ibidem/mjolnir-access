<?php return array
	(
		// mapping targets to files
		'targets' => array
			(
				'lobby' => array
					(
						'components/base',
						'lobby'
					),

				'signin' => array
					(
						'components/base',
						'signin'
					),

				'signup' => array
					(
						'components/base',
						'signup'
					),

				'pwdreset' => array
					(
						'components/base',
						'pwdreset'
					),

			//// Exceptions ////////////////////////////////////////////////////

				'exception-NotFound' => array
					(
						'components/errors/base',
						'errors/not-found'
					),
				'exception-NotAllowed' => array
					(
						'components/errors/base',
						'errors/not-allowed'
					),
				'exception-NotApplicable' => array
					(
						'components/errors/base',
						'errors/not-applicable'
					),
				'exception-Unknown' => array
					(
						'components/errors/base',
						'errors/unknown'
					),
			),
	);

