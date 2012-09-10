<?php return array // ăÎîŞşŢţâ
	(
		'mjolnir.access.sign_up_now' => 'Inregistreazăte astăzi!',
		'mjolnir.access.signin.username_or_email' => '<b>Utilizator</b> sau <b>Email</b>',
		'mjolnir.access.signin.title' => 'Autentificare',
		'mjolnir.access.signin.not_yet_a_member' => 'Nu eşti incă membru?',
		'mjolnir.access.signin.remember_me' => 'Zonă privată. Ţinemă logat...',
		'mjolnir.access.signin.dont_remember_me' => 'Zonă publică; logare temporară.',
		'mjolnir.access.stats.title' => 'Bun venit!',
		'mjolnir.access.stats.currently_logged_as' => function ($args) 
			{
				return \strtr('Sunteti logat ca:<br> <strong>:username</strong>', $args);
			}
	);