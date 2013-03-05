<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirPwdReset
{
	/**
	 * Reset user's password
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
					$_POST['notice'] = \app\Lang::key('mjolnir:access/pwdreset-finished');
					$this->pwdreset_view();
				}
				else # got errors
				{
					$_POST['notice'] = $errors[0];
					return $this->pwdreset_view();
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
					$errors = ['form' => [\app\Lang::term('You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.')] ];
					return $this->pwdreset_view($errors);
				}
				else # captcha test passed
				{
					$user = \app\Model_User::detect_identity($_POST);

					if ($user === null)
					{
						$errors = [ 'identity' => [\app\Lang::term('We do not know of any such user or email.')] ];
					}

					if ($errors === null)
					{
						$key = \app\Model_User::pwdreset_key($user['id']);

						$pwdreset_url = \app\CFS::config('mjolnir/auth')['default.pwdreset'].'?user='.$user['id'].'&key='.$key;

						// send email
						$emails_sent = \app\Email::instance()->send
							(
								$user['email'],
								'no-reply@'.\app\CFS::config('mjolnir/base')['domain'],
								\app\Lang::term('Password Reset'), # subject
								\app\Lang::key('mjolnir:access/pwdreset-reset-url', [':url' => $pwdreset_url]) # message
							);

						if ( ! $emails_sent)
						{
							\mjolnir\log
								(
									'Emails',
									'Failed to send password reset email to: '.$user['email']
								);

							$this->pwdreset_view(['form' => [\app\Lang::term('Failed to send the reset email to account address. Please try again later; if problem persists please contact us.')] ]);
						}
						else # succesfully sent emails
						{
							$_POST['notice'] = \app\Lang::key('mjolnir:access/pwdreset-success');
							return $this->pwdreset_view();
						}
					}
					else # got errors
					{
						return $this->pwdreset_view($errors);
					}
				}
			}
		}
		else # treat as GET
		{
			return $this->pwdreset_view();
		}
	}

	/**
	 * Alias
	 *
	 * @return \mjolnir\types\Renderable
	 */
	function public_pwdreset()
	{
		return $this->action_pwdreset();
	}

	/**
	 * Setup view used when signing up.
	 *
	 * @return \mjolnir\types\Renderable
	 */
	function pwdreset_view($errors = null)
	{
		$target = $this->public_index()->viewtarget().'.pwdreset';

		$view = $this->public_index()
			->viewtarget_is($target);

		if ($errors !== null)
		{
			$errors = [ 'mjolnir:access/pwdreset.errors' => $errors ];
			$view->pass('errors', $errors);
		}
		else # no errors
		{
			$view->pass('errors', []);
		}

		return $view;
	}

} # trait
