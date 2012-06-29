<?php return array // ăÎîŞşŢţâ
	(
		'ibiden.access.sign_up_now' => 'Inregistreazăte astăzi!',
		'ibidem.access.signin.username_or_email' => '<b>Utilizator</b> sau <b>Email</b>',
		'ibidem.access.signin.title' => 'Autentificare',
		'ibidem.access.signin.not_yet_a_member' => 'Nu eşti incă membru?',
		'ibidem.access.signin.remember_me' => 'Zonă privată. Ţinemă logat...',
		'ibidem.access.signin.dont_remember_me' => 'Zonă publică; logare temporară.',
		'ibidem.access.stats.title' => 'Bun venit!',
		'ibidem.access.stats.currently_logged_as' => function ($args) 
			{
				return \strtr('Sunteti logat ca:<br> <strong>:username</strong>', $args);
			}
	);