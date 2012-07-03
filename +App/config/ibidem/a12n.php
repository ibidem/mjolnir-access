<?php return array
	(
		'signin' => array
			(
				'facebook' => array
					(
						'AppID' => null,
						'AppSecret' => null,
					
						'slug' => 'facebook',
						'icon' => 'facebook',
						'title' => 'Facebook',
						'register' => '\ibidem\access\channel\facebook',
					
						'validator' => function ($config) {
							if ($config['AppID'] === null)
							{
								return 'App ID is missing.';
							}
							
							if ($config['AppSecret'] === null)
							{
								return 'App Secret is missing.';
							}
							
							return null;
						}
						
					),
				
				'twitter' => array
					(
						'slug' => 'twitter',
						'icon' => 'twitter',
						'title' => 'Twitter',
						'register' => '\ibidem\access\channel\twitter',
					),
				
				'google' => array
					(
						'slug' => 'google',
						'icon' => 'signin',
						'title' => 'Google',
						'register' => '\ibidem\access\channel\google',
					),
				
				'yahoo' => array
					(
						'slug' => 'yahoo',
						'icon' => 'signin',
						'title' => 'Yahoo',
						'register' => '\ibidem\access\channel\yahoo',
					),
			),
	);