<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Library
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirSignin
{
	/**
	 * Action: Sign In user
	 */
	function action_signin()
	{
		if (\app\A12n::instance()->role() !== \app\A12n::guest())
		{
			$base_config = \app\CFS::config('mjolnir/base');
			\app\Server::redirect($base_config['site:frontend']);
		}

		if (\app\Server::request_method() === 'POST')
		{
			$errors = ['form' => []];
			
			$a12n_config = \app\CFS::config('mjolnir/a12n');
			
			// got required fields
			if ( ! isset($_POST['identity']) || ! isset($_POST['password']))
			{
				$errors['identity'] = ['Field is required.']; 
				$errors['password'] = ['Field is required'];
			}

			$user = \app\Model_User::detect_identity($_POST);

			if ( ! $user)
			{
				$errors['form'][] = \app\Lang::tr('Sign in failed. We do not know of any such user or email.');
				$this->signin_view($errors); 
				return;
			}

			// check password attempts
			if ($user['pwdattempts'] > $a12n_config['catptcha.signin.attempts'])
			{
				$_POST['show_captcha'] = true;
				
				if ( ! isset($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']))
				{	
					
					$errors['form'][] = \app\Lang::msg('login.passwordattemps', [':number' => $user['pwdattempts']]);
					$this->signin_view($errors);
					return;
				}

				// we've got 5 failed attempts, captcha checks must pass to avoid
				// bots brute forcing their way in
				$captcha_errors = \app\ReCaptcha::verify
					(
						$_POST['recaptcha_challenge_field'],
						$_POST['recaptcha_response_field']
					);

				if ($captcha_errors !== null)
				{
					if ( ! isset($errors['form']))
					{
						$errors['form'] = [];
					}

					$errors['form'][] = \app\Lang::tr('You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.');
					\app\Model_User::bump_pwdattempts($user['id']);
					
					$this->signin_view($errors); 
					return;
				}
			}
			
			$pwdsalt = $user['pwdsalt'];

			// load configuration
			$security = \app\CFS::config('mjolnir/security');
			
			// generate password salt and hash
			$apilocked_password = \hash_hmac
				(
					$security['hash']['algorythm'],
					$_POST['password'],
					$security['keys']['apikey'],
					false
				);

			$pwdverifier = \hash_hmac
				(
					$security['hash']['algorythm'],
					$apilocked_password,
					$pwdsalt,
					false
				);

			// verify
			if ($pwdverifier !== $user['pwdverifier'])
			{
				$errors['password'] = [\app\Lang::tr('The password you have entered is incorect.')];
				\app\Model_User::bump_pwdattempts($user['id']);
				$this->signin_view($errors); 
				return;
			}

			// logged in
			if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on')
			{
				\app\A12n::remember_user($user['id']);
			}
			else # remember_me === off
			{
				\app\A12n::signin($user['id'], \app\Model_User::role_for($user['id']));
			}

			// redirect
			$base_config = \app\CFS::config('mjolnir/base');
			if (isset($a12n_config['signin.redirect']))
			{
				if (\is_string($a12n_config['signin.redirect']))
				{
					\app\Server::redirect($a12n_config['signin.redirect']);
				}
				else # assume function
				{
					\app\Server::redirect
						(
							$a12n_config['signin.redirect']($user)
						);
				}
			}
			else if (isset($base_config['site:frontend']))
			{
				\app\Server::redirect
					(
						'//'.$base_config['domain'].$base_config['path'].
						$base_config['site:frontend']
					);
			}

			// no default frontend
			$this->forward('\mjolnir\access\a12n', ['action' => 'lobby']);	
		}
		else # user === null
		{
			$this->signin_view();
		}
	}

	/**
	 * Action: Sign Out user out of system.
	 */
	function action_signout()
	{
		\app\A12n::signout();
		$a12n_config = \app\CFS::config('mjolnir/a12n');
		\app\Server::redirect($a12n_config['default.signin']);
	}

} # trait
