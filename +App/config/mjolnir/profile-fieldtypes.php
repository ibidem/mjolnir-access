<?php 

/* @var $form \app\Form */

return array
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
					},
				'store' => function ($value)
					{
						return $value;
					},
			),
		'textarea' => array
			(
				'form' => function ($form, $title, $name, $value) 
					{
						return $form->textarea($title, $name)->value($value);
					},
				'render' => function ($value) 
					{
						return $value;
					},
				'store' => function ($value)
					{
						return $value;
					},
			),
		'sex' => array
			(
				'form' => function ($form, $title, $name, $value) 
					{
						return $form->select($title, $name, [\app\Lang::term('male') => 'm', \app\Lang::term('female') => 'f'])->value($value);
					},
				'render' => function ($value) 
					{
						return $value == 'm' ? \app\Lang::term('male') : \app\Lang::term('female');
					},
				'store' => function ($value)
					{
						return $value;
					},
			),
		'datetime' => array
			(
				'form' => function (\app\Form $form, $title, $name, $value) 
					{
						if ( ! empty($value))
						{
							$datetime = \unserialize($value);
							return $form->datetime($title, $name)->value($datetime->format('Y-m-d'));
						}
						else # empty
						{
							return $form->datetime($title, $name)->value(\date('Y-m-d'));
						}
					},
				'render' => function ($value) 
					{
						$datetime = \unserialize($value);
						return $datetime->format('Y-m-d');
					},
				'store' => function ($value)
					{
						return \serialize(new \DateTime($value));
					},
			)

	);