<?php require 'library/extras.php';

	// definitions
	$unsorted = array
		(
			'unsorted',
		);
	
return array
	(
		'root' => 'root/',
		'sources' => 'src/',
		'mode' => 'targeted',
	
	# complete mode

		'complete-mapping' => \app\index
			(
				$unsorted
			),
		
	# targeted mode
	
		'targeted-common' => \app\index
			(
				$unsorted
			),
	
		'targeted-mapping' => array
			(
				'frontend' => array
					(
						'base',
					),
			),

	); # config
