<?php return array
	(
	
		'text' => array
			(
				'form' => function ($form, $title, $name, $value) 
					{
						return $form->text($title, $name)->value($value);
					},
				'render' => function ($value) 
					{
						return $value;
					}
			),
					
		'textarea' => array
			(
				// @todo
			),

	);