<?php return array
	(
		'signin' => array
			(
				'facebook' => array
					(
						'apikey' => '',
						'slug' => 'facebook',
						'icon' => 'facebook',
						'title' => 'Facebook',
						'register' => '\ibidem\access\openid\facebook',
					),
				'twitter' => array
					(
						'slug' => 'twitter',
						'icon' => 'twitter',
						'title' => 'Twitter',
						'register' => '\ibidem\access\openid\twitter',
					),
				'google' => array
					(
						'slug' => 'google',
						'icon' => 'signin',
						'title' => 'Google',
						'register' => '\ibidem\access\openid\google',
					),
				'yahoo' => array
					(
						'slug' => 'yahoo',
						'icon' => 'signin',
						'title' => 'Yahoo',
						'register' => '\ibidem\access\openid\yahoo',
					),
			),
	);