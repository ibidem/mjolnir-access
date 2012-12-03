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
						"Dacă nu ați cerut o resetare de parola va rugăm să ignorați acest email.\n\n".
						"Pentru a continua cu procesul de resetare a parolei vizitați pagina următoare:\n :url",
						$in
					);
			},
					
		'\mjolnir\access\user:emails:title' => 'Adrese de Email',
					
		'\mjolnir\access\user:emails:intructions' => 
'Pentru a vă loga cu situri externe trebuie sa va conectați adresele de email
prin care sunteți recunoscut pe siturile respective. Daca aveți deja alt cont
la noi cu aceiasi adresă de email, contul respectiv va fi blocat și logarea prin
emailul respectiv vă va redirecta către contul curent.',
					
		'\mjolnir\access\user:emails:no_secondary_emails' 
			=> 'Nu aveți momentan nici un email suplimentar.',
					
		'mjolnir:email:visit_url_to_finish' => function ($in)
			{
				return \strtr('Va rugăm sa vizitați url-ul următor pentru a finaliza procesul:'."\n:url", $in);
			},
					
		'mjolnir:invalid_token' 
			=> 'Token invalid. Vă rugăm să repetați procesul. Această eroare apare în cazul unui cod expirat or url malformat.',
					
		'mjolnir:account_activated'
			=> 'Contul dumeneavoastră a fost activat.',
					
		'mjolnir:your_account_is_inactive'
			=> 'Contul dumneavoastră nu este activ. Un cod nou de activare a fost trimis la adresa dumneavoastră de email.',
	);