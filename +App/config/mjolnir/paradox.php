<?php return array
	(

		'mjolnir-access' => array
			(
				'database' => 'default',

				// versions
				'1.0.0' => \app\Pdx::gate('mjolnir-access/1.0.0', ['mjolnir-database' => '1.0.0']),
				'1.0.1' => \app\Pdx::gate('mjolnir-access/1.0.1'),
			),

	); # config
