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
						return $form->select($title, $name, [\app\Lang::tr('male') => 'm', \app\Lang::tr('female') => 'f'])->value($value);
					},
				'render' => function ($value) 
					{
						return $value == 'm' ? \app\Lang::tr('male') : \app\Lang::tr('female');
					},
				'store' => function ($value)
					{
						return $value;
					},
			),
		'only_friends_comment' => array
			(
				'form' => function ($form, $title, $name, $value = 'n') 
					{
						if (empty($value)) $value = 'n';
						return $form->select($title, $name, [\app\Lang::tr('Yes, block all the other users from commenting') => 'y', \app\Lang::tr('No, allow all the users to comment') => 'n'])->value($value);
					},
				'render' => function ($value) 
					{
						return $value == 'y' ? \app\Lang::tr('Yes, block all the other users from commenting') : \app\Lang::tr('No, allow all the users to comment');
					},
				'store' => function ($value)
					{
						return $value;
					},
			),
		'checkbox' => array
			(
				'form' => function ($form, $title, $name, $value) 
					{
						$realval = $value == 'on' ? 'on' : 'off';
						return $form->checkbox($title, $name)->check_value($realval);
					},
				'render' => function ($value) 
					{
						return $value == 'on' ? 'checked="checked"' : '';
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