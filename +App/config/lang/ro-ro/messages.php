<?php return array // ăÎîŞşŢţâ
	(
		'ibidem.access.signin.title' => 'Logare',
		'ibidem.access.signin.remember_me' => 'Ţinemă logat...',
		'ibidem.access.stats.title' => 'Bun venit!',
		'ibidem.access.stats.currently_logged_as' => function ($args) 
			{
				return \strtr('Sunteti logat ca:<br> <strong>:username</strong>', $args);
			}
	);