<?php return array
	(
		'description'
			=> 'Repair faulty pwdalgorythm values (NULL -> sha512).'
			,

		'fixes' => function ($db, $state)
			{
				// set all current stamps as authorized
				$db->prepare
					(
						'
							UPDATE `'.\app\Model_User::table().'`
							   SET pwdalgorythm = "sha512"
							 WHERE pwdalgorythm IS NULL
						'
					)
					->run();
			},

	); # config