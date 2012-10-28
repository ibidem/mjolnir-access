<?php return array
	(
		'mjolnir.access.sign_up_now' => 'Sign Up now!',

		'mjolnir.access.signin.username_or_email' => '<b>Username</b> or <b>Email</b>',
		'mjolnir.access.signin.title' => 'Signin',
		'mjolnir.access.signin.remember_me' => 'Private area. Remember Me...',
		'mjolnir.access.signin.not_yet_a_member' => 'Not yet a member?',
		'mjolnir.access.signin.dont_remember_me' => 'Public area; temporary login.',

		'mjolnir.access.stats.title' => 'Welcome back!',
		'mjolnir.access.stats.currently_logged_as' => function ($in)
			{
				return \strtr('Currently logged in as:<br> <strong>:username</strong>', $in);
			},

		'mjolnir.access.signup.title' => 'Sign Up',

		'mjolnir.access.pwdreset.title'	=> 'Password Reset',
		'mjolnir.access.pwdreset.success'
			=> 'An email with further instructions has been mailed to you address.',
		'mjolnir.access.pwdreset.failure'
			=> 'Failed to reset password. Please try repeating the process from the begining.',
		'mjolnir.access.pwdreset.finished'
			=> 'Your password has been reset.',
		'mjolnir.access.pwdreset.password_reset_url' => function ($in)
			{
				return \strtr
					(
						"If you haven't requested a password reset please ignore this email.\n\n".
						"To reset your password please visit the following temporary page:\n :url",
						$in
					);
			},

	);