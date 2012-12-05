<?php return array
	(
		'mjolnir.access.sign_up_now' => 'Sign Up now!',

		'mjolnir.access.signin.username_or_email' => '<b>Username</b> or <b>Email</b>',
		'mjolnir.access.signin.title' => 'Sign In',
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
		'mjolnir.access.pwdreset.success' => function ($in)
			{
			    return 'An email with further instructions has been mailed to this email address: '.$in['masked_email'].'. Please check you Spam section as well.';
			},
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
					
		'\mjolnir\access\user:emails:title' => 'Emails',
					
		'\mjolnir\access\user:emails:intructions' => 
'To sign in via additional providers please link the emails by which those 
providers know you as to this account. If an account already exists on our end 
with the given email it will be locked and using said email will sign you into 
this account.',
					
		'\mjolnir\access\user:emails:no_secondary_emails' 
			=> 'You currently have no secondary emails.',
					
		'mjolnir:email:visit_url_to_finish' => function ($in)
			{
				return \strtr('Please visit following url to complete the process:'."\n:url", $in);
			},
			
		'mjolnir:invalid_token' 
			=> 'Invalid token supplied; please try repeating the process. This error occurs if you copy pasted the incorrect url, or to code in question has expired.',
					
		'mjolnir:account_activated'
			=> 'Your account is now active.',
					
		'mjolnir:your_account_is_inactive'
			=> 'Your account is not active, access defined. A fresh activation code has been send to your email address.',
					
		'mjolnir:email:activate_account' => function ($in)
			{
				return \app\View::instance('mjolnir/emails/en-us/activate_account')
					->variable('nickname', $in[':nickname'])
					->variable('token_url', $in[':token_url'])
					->render();
			},
					
		'mjolnir:sent_activation_email'
			=> 'Success! But your account is currently inactive. An email has been sent to your email address with activation instructions. Attempting to signin will re-issue a new activation email.'
					
	);
	