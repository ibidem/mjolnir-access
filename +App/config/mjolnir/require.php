<?php return array
	(
		'mjolnir\access' => array
			(
				 'ReCAPTCHA Keys' => function ()
					{
						$recaptcha = \app\CFS::config('mjolnir/auth')['recaptcha'];

						if ($recaptcha['public_key'] !== null && $recaptcha['private_key'] !== null)
						{
							return 'available';
						}

						return 'error';
					},

				'API key' => function ()
					{
						$securitykeys = \app\CFS::config('mjolnir/security')['keys'];

						if ($securitykeys['apikey'] !== null)
						{
							return 'available';
						}

						return 'error';
					},

			),
	);
