<?php return array
	(
		'mjolnir:access/sign-up-now' => 'Sign Up now!',

		'mjolnir:access/username-or-email' => '<b>Username</b> or <b>Email</b>',
		'mjolnir:access/signin-title' => 'Sign In',
		'mjolnir:access/remember-me' => 'Private area. Remember Me...',
		'mjolnir:access/not-yet-a-member' => 'Not yet a member?',
		'mjolnir:access/dont-remember-me' => 'Public area; temporary login.',

		'mjolnir:access/stats-title' => 'Welcome back!',
		'mjolnir:access/currently-logged-in-as' => function ($in)
			{
				return \strtr('Currently logged in as:<br> <strong>:username</strong>', $in);
			},

		'mjolnir:access/signup-title' => 'Sign Up',

		'mjolnir:access/pwdreset-title'	=> 'Password Reset',
		'mjolnir:access/pwdreset-success'
			=> 'An email with further instructions has been mailed to your address.',
		'mjolnir:access/pwdreset-failure'
			=> 'Failed to reset password. Please try repeating the process from the begining.',
		'mjolnir:access/pwdreset-finished'
			=> 'Your password has been reset.',
		'mjolnir:access/pwdreset-reset-url' => function ($in)
			{
				return \strtr
					(
						"If you haven't requested a password reset please ignore this email.\n\n".
						"To reset your password please visit the following temporary page:\n :url",
						$in
					);
			},

		'mjolnir:access/emails-title' => 'Emails',

		'mjolnir:access/emails-intructions' =>
'To sign in via additional providers please link the emails by which those
providers know you as to this account. If an account already exists on our end
with the given email it will be locked and using said email will sign you into
this account.',

		'mjolnir:access/emails-no-secondary-emails'
			=> 'You currently have no secondary emails.',

		'mjolnir:access/email-visit-url-to-finish' => function ($in)
			{
				return \strtr('Please visit following url to complete the process:'."\n:url", $in);
			},

		'mjolnir:access/invalid-token'
			=> 'Invalid token supplied; please try repeating the process. This error occurs if you copy pasted the incorrect url, or to code in question has expired.',

		'mjolnir:access/account-activated'
			=> 'Your account is now active.',

		'mjolnir:access/your-account-is-inactive'
			=> 'Your account is not active, access denied. A fresh activation code has been send to your email address.',

		'mjolnir:access/email-activate-account' => function ($in)
			{
				return \app\View::instance('mjolnir/emails/en-US/activate_account')
					->pass('nickname', $in[':nickname'])
					->pass('token_url', $in[':token_url'])
					->render();
			},

		'mjolnir:access/sent-activation-email'
			=> 'Success! But your account is currently inactive. An email has been sent to your email address with activation instructions. Attempting to signin will re-issue a new activation email.',

		'login.passwordattemps' => function ($in)
			{
				return "You've failed to sign in {$in} times. Additional check required.";
			},

	);
