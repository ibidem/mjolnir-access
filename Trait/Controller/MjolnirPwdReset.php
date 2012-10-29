<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Library
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirPwdReset
{
	/**
	 * Action: Reset user's password
	 */
	function action_pwdreset()
	{
		if (\app\Server::request_method() === 'POST')
		{
			$errors = null;

			if (isset($_POST['user'], $_POST['key']))
			{
				$errors = \app\Model_User::pwdreset($_POST['user'], $_POST['key'], $_POST['password']);
				if ($errors === null)
				{
					$_POST['notice'] = \app\Lang::msg('mjolnir.access.pwdreset.finished');
					$this->pwdreset_view();
				}
				else # got errors
				{
					$_POST['notice'] = $errors[0];
					$this->pwdreset_view();
				}
			}
			else # send email phase
			{
				// check recaptcha
				$error = \app\ReCaptcha::verify
					(
						$_POST['recaptcha_challenge_field'],
						$_POST['recaptcha_response_field']
					);

				if ($error !== null)
				{
					$errors = ['form' => [\app\Lang::tr('You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.')] ];

					$this->pwdreset_view($errors);
				}
				else # captcha test passed
				{
					$user = \app\Model_User::detect_identity($_POST);

					if ($user === null)
					{
						$errors = [ 'identity' => [\app\Lang::tr('We do not know of any such user or email.')] ];
					}

					if ($errors === null)
					{
						$key = \app\Model_User::pwdreset_key($user['id']);

						$pwdreset_url = \app\CFS::config('mjolnir/a12n')['default.pwdreset'].'?user='.$user['id'].'&key='.$key;

						// send email
						$emails_sent = \app\Email::instance()->send
							(
								$user['email'],
								'no-reply@'.\app\CFS::config('mjolnir/base')['domain'],
								\app\Lang::tr('Password Reset'), # subject
								\app\Lang::msg('mjolnir.access.pwdreset.password_reset_url', [':url' => $pwdreset_url]) # message
							);

						if ($emails_sent === 0)
						{
							\mjolnir\log
								(
									'Warning',
									'Failed to send password reset email to: '.$user['email'],
									'Errors/'
								);

							$this->pwdreset_view(['form' => [\app\Lang::tr('Failed to sent reset email to account address. Please try again later; if problem persists please contact us.')] ]);
						}
						else # succesfully sent emails
						{
							$_POST['notice'] = \app\Lang::msg('mjolnir.access.pwdreset.success');
							$this->pwdreset_view();
						}
					}
					else # got errors
					{
						$this->pwdreset_view($errors);
					}
				}
			}
		}
		else # treat as GET
		{
			$this->pwdreset_view();
		}
	}

} # trait
