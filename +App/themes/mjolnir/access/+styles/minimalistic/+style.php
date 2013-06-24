<?php require 'library/extras.php';

$complete = array
	(
		'base'
	);

return array
	(
		'version' => '1.0.0',
		'root' => 'root/',
		'sources' => 'src/',
		'mode' => 'complete',

	# Complete mode

		'complete-mapping' => $complete,

	# Targetted mode

		// common files used in targeted mapping
		'targeted-common' => [ ],

		// mapping targets to files; if a target is not mapped it won't have
		// any style associated
		'targeted-mapping' => array
			(
				// empty
			),

	); # config
