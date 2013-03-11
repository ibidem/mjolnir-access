<?php return array // ăÎîŞşŢţâ
	(
		'mjolnir:access/sign-up-now' => 'Inregistreazăte astăzi!',

		'mjolnir:access/username-or-email' => '<b>Utilizator</b> sau <b>Email</b>',
		'mjolnir:access/signin-title' => 'Autentificare',
		'mjolnir:access/not-yet-a-member' => 'Nu eşti incă membru?',
		'mjolnir:access/remember-me' => 'Zonă privată. Ţinemă logat...',
		'mjolnir:access/dont-remember-me' => 'Zonă publică; logare temporară.',

		'mjolnir:access/stats-title' => 'Bun venit!',
		'mjolnir:access/currently-logged-in-as' => function ($in)
			{
				return \strtr('Sunteti logat ca:<br> <strong>:username</strong>', $in);
			},

		'mjolnir:access/signup-title' => 'Inregistrare',

		'mjolnir:access/pwdreset-title' => 'Resetare Parolă',
		'mjolnir:access/pwdreset-success'
			=> '<b>Instrucțiuni suplimentare</b> au fost trimise la adresa dumneavoastră de email. Vă rugăm să verificați și <b>secțiunea Spam</b> a căsuței de email.',
		'mjolnir:access/pwdreset-failure'
			=> 'Procesul de resetare a eșuat. Vă rugăm încercați să resetați din nou de la început.',
		'mjolnir:access/pwdreset-finished'
			=> 'Parola dumneavoastră a fost resetată. Vă rugăm să vă logați folosind formularul de login.',
		'mjolnir:access/pwdreset-reset-url' => function ($in)
			{
				return \strtr
					(
						"Dacă nu ați cerut o resetare de parola va rugăm să ignorați acest email.\n\n".
						"Pentru a continua cu procesul de resetare a parolei vizitați pagina următoare:\n :url",
						$in
					);
			},
					
		'mjolnir:access/emails-title' => 'Adrese de Email',
					
		'mjolnir:access/emails-intructions' => 
'Pentru a vă loga cu situri externe trebuie sa va conectați adresele de email
prin care sunteți recunoscut pe siturile respective. Daca aveți deja alt cont
la noi cu aceiasi adresă de email, contul respectiv va fi blocat și logarea prin
emailul respectiv vă va redirecta către contul curent.',
					
		'mjolnir:access/emails-no-secondary-emails' 
			=> 'Nu aveți momentan nici un email suplimentar.',
					
		'mjolnir:access/email-visit-url-to-finish' => function ($in)
			{
				return \strtr('Va rugăm sa vizitați url-ul următor pentru a finaliza procesul:'."\n:url", $in);
			},
					
		'mjolnir:access/invalid-token' 
			=> 'Token invalid. Vă rugăm să repetați procesul. Această eroare apare în cazul unui cod expirat or url malformat.',
					
		'mjolnir:access/account-activated'
			=> 'Contul dumeneavoastră a fost activat.',
					
		'mjolnir:access/your-account-is-inactive'
			=> 'Contul dumneavoastră nu este activ. Un cod nou de activare a fost trimis la adresa dumneavoastră de email.',
					
		'mjolnir:access/email-activate-account' => function ($in)
			{
				return \app\View::instance('mjolnir/emails/ro-ro/activate_account')
					->variable('nickname', $in[':nickname'])
					->variable('token_url', $in[':token_url'])
					->render();
			},
					
		'mjolnir:access/sent-activation-email'
			=> 'Contul dumneavoastră a fost creat dar este inactiv. Un email cu instrucțiuni de activare a fost trimis la adresa dumneavoastră. Pentru a primi un email nou introduceți datele corecte în formularul de logare.',
					
		'login.passwordattemps' => function ($in)
			{
				return "Ați eșuat logarea de {$in} ori.";
			},
	);