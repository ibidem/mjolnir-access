<?php namespace app;

$validator_keyed = function ($config)
	{
		if ($config['key'] === null)
		{
			return 'App ID is missing.';
		}

		if ($config['secret'] === null)
		{
			return 'App Secret is missing.';
		}

		return null;
	};

$validator_unkeyed = function ($config)
	{
		return null;
	};

return array
	(

	// signup

		'standard.signup' => false, # true = enabled, false = disabled

		'signup.default.role' => 'member',

		'default.signup' => \app\URL::href('mjolnir:access/auth.route', ['action' => 'signup'], [], 'http'),

	// misc signin

		'default.pwdreset' => \app\URL::href('mjolnir:access/auth.route', ['action' => 'pwdreset'], [], 'http'),

		'default.emails_manager' => \app\URL::href('mjolnir:access/auth.route', ['action' => 'emails'], [], 'http'),

		'remember_me.timeout' => 60 * 60 * 24 * 14,

		'default.signin' => \app\URL::href('mjolnir:access/auth.route', ['action' => 'signin']),

		'recaptcha' => array
			(
				'public_key' => null,
				'private_key' => null,
			),

		'catptcha.signin.attempts' => 5,

	// open authentification

		'signin' => array
			(
				'facebook' => array
					(
						'AppID' => null,
						'AppSecret' => null,

						'slug' => 'facebook',
						'icon' => 'facebook',
						'title' => 'Facebook',
						'register' => 'mjolnir:access/channel/facebook',

						'scope' => 'email',

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
						},

						'session.token.name' => 'tkbf',

					),

				'twitter' => array
					(
						'slug' => 'twitter',
						'icon' => 'twitter',
						'title' => 'Twitter',
						'register' => 'mjolnir:access/channel/twitter',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'Twitter',
						'validator' => $validator_keyed,
					),

				'google' => array
					(
						'slug' => 'google',
						'icon' => 'signin',
						'title' => 'Google',
						'register' => 'mjolnir:access/channel/google',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'Google',
						'validator' => $validator_keyed,
					),

				'yahoo' => array
					(
						'slug' => 'yahoo',
						'icon' => 'signin',
						'title' => 'Yahoo',
						'register' => 'mjolnir:access/channel/yahoo',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'Yahoo',
						'validator' => $validator_unkeyed,
					),

				'aol' => array
					(
						'slug' => 'aol',
						'title' => 'AOL',
						'icon' => 'signin',
						'register' => 'mjolnir:access/channel/aol',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'AOL',
						'validator' => $validator_unkeyed,
					),

				'myspace' => array
					(
						'slug' => 'myspace',
						'title' => 'MySpace',
						'icon' => 'signin',
						'register' => 'mjolnir:access/channel/myspace',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'MySpace',
						'validator' => $validator_keyed,
					),

				'linkedin' => array
					(
						'slug' => 'linkedin',
						'title' => 'LinkedIn',
						'icon' => 'linkedin',
						'register' => 'mjolnir:access/channel/linkedin',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'LinkedIn',
						'validator' => $validator_keyed,
					),

				'foursquare' => array
					(
						'slug' => 'foursquare',
						'title' => 'Foursquare',
						'icon' => 'signin',
						'register' => 'mjolnir:access/channel/foursquare',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'Foursquare',
						'validator' => $validator_keyed,
					),

				'live' => array
					(
						'slug' => 'live',
						'title' => 'Live',
						'icon' => 'signin',
						'register' => 'mjolnir:access/channel/live',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'Live',
						'validator' => $validator_keyed,
					),

				'github' => array
					(
						'slug' => 'github',
						'title' => 'Github',
						'icon' => 'github',
						'register' => 'mjolnir:access/channel/github',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'Github',
						'validator' => $validator_keyed,
					),

				'lastfm' => array
					(
						'slug' => 'lastfm',
						'title' => 'LastFM',
						'icon' => 'signin',
						'register' => 'mjolnir:access/channel/lastfm',
						'keys' => [],
						'scope' => '',
						'hybridauth.key' => 'LastFM',
						'validator' => $validator_keyed,
					),

			),
	);