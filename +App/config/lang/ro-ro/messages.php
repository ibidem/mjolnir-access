<?php return array // ăÎîŞşŢţâ
	(
		'mjolnir.access.sign_up_now' => 'Inregistreazăte astăzi!',

		'mjolnir.access.signin.username_or_email' => '<b>Utilizator</b> sau <b>Email</b>',
		'mjolnir.access.signin.title' => 'Autentificare',
		'mjolnir.access.signin.not_yet_a_member' => 'Nu eşti incă membru?',
		'mjolnir.access.signin.remember_me' => 'Zonă privată. Ţinemă logat...',
		'mjolnir.access.signin.dont_remember_me' => 'Zonă publică; logare temporară.',

		'mjolnir.access.stats.title' => 'Bun venit!',
		'mjolnir.access.stats.currently_logged_as' => function ($in)
			{
				return \strtr('Sunteti logat ca:<br> <strong>:username</strong>', $in);
			},

		'mjolnir.access.signup.title' => 'Inregistrare',

		'mjolnir.access.pwdreset.title' => 'Resetare Parolă',
		'mjolnir.access.pwdreset.success'
			=> '<b>Instrucțiuni suplimentare</b> au fost trimise la adresa dumneavoastră de email. Vă rugăm să verificați și <b>secțiunea Spam</b> a căsuței de email.',
		'mjolnir.access.pwdreset.failure'
			=> 'Procesul de resetare a eșuat. Vă rugăm încercați să resetați din nou de la început.',
		'mjolnir.access.pwdreset.finished'
			=> 'Parola dumneavoastră a fost resetată. Vă rugăm să vă logați folosind formularul de login.',
		'mjolnir.access.pwdreset.password_reset_url' => function ($in)
			{
				return \strtr
					(
						"Dacă nu ați cerut o resetare de parola va rugam să ignorați acest email.\n\n".
						"Pentru a continua cu procesul de resetare a parolei vizitați pagina următoare:\n :url",
						$in
					);
			},
	);