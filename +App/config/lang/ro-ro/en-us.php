<?php return array // ăÎîŞşţâ
	(
		'Sign In' => 'Autentificare',
		'Sign Up' => 'Înregistrare',
		'Backend' => 'Administrare',
		'Password' => 'Parolă',
		'Cancel' => 'Anulează',
		'Help' => 'Ajutor',
		'Reset Password' => 'Resetează Parola',
		'Failed to send the reset email to account address. Please try again later; if problem persists please contact us.' => 
			'Din păcate nu am putut trimite emailul de reset către adresa de email a contului. Te rugăm să încerci mai târziu; dacă problema persistă contactează-ne.',
		'We do not know of any such user or email.' => 'Nu am găsit un astel de username sau adresă de email',
		'You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.'
			=> 'Nu ai trecut verificarea <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a>.',
		'Password reset has expired. Please repeat the process.' => 'Linkul de resetare a parolei a expirat. Te rugăm să repeți procedura.',
		'Invalid password reset key. Please repeat the process.' => 'Cheie de resetare a parolei nevalidă. Te rugăm să repeți procedura.',
		'<b>Username</b> or <b>Email</b>' => '<b>Username</b> sau <b>Email</b>',
	
		'Your :provider account has been linked to your site account.' => function ($in)
			{
				return \strtr('Contul dumneavoastră de :provider a fost contectat de contul dumneavoastră de pe site.', $in);
			},
		'Confirmation of Email Ownership' => 'Confirmarea adresei de email',
					
		'An email has been sent, at :email, with further instructions.' => function ($in)
			{
				return \strtr('Am trimis un email cu detalii pentru pașii următori la adresa de email :email . Vă rugăm să verificați și secțiunea SPAM a căsuței de email.', $in);
			},
		'Succesfully added secondary email.' => 'Am adăugat noua adresă de email/cont social la contul curent.',
	);