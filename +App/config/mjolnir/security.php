<?php return array
	(
		'tokens' => array
			(
				'default.expires' => '+3 hours',
			),
	
		'hash' => array
			(
				// changing this value implies also changing the definition for
				// ":secure_hash" in your mjolnir/schematics configuration
				// and also including migrations this change is done on an 
				// populated database
				'algorythm' => 'sha512',
			),
	
		'keys' => array
			(
				'apikey' => null,
			),
	);
