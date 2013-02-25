<?php return array
	(
		'form.standards' => array
			(
				'mjolnir:access/twitter' => function (\mjolnir\types\HTMLForm $form)
					{
						return $form
							->apply('mjolnir:twitter');
					},
			),
	);
