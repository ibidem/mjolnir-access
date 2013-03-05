<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirSignin
{
	/**
	 * Check if user is already signed in and redirect to appropriate page. In
	 * cases where you don't want to redirect the user simply overwrite this
	 * method.
	 */
	function redirect_signedin_users()
	{
		if (\app\Auth::role() !== \app\Auth::Guest)
		{
			\app\Server::redirect(\app\Server::url_frontpage());
		}
	}

	/**
	 * @return \mjolnir\types\Renderable
	 */
	function action_signin()
	{
		// this method is not guranteed since some implementations allow for 
		// login from all user roles
		$this->redirect_signedin_users();

		if (\app\Server::request_method() === 'POST')
		{
			$errors = [ 'form' => [] ];

			$auth_config = \app\CFS::config('mjolnir/auth');

			// got required fields
			if ( ! isset($_POST['identity']) || ! isset($_POST['password']))
			{
				$errors['identity'] = ['Field is required.'];
				$errors['password'] = ['Field is required.'];
			}

			$user = \app\Model_User::detect_identity($_POST);

			if ( ! $user)
			{
				$errors['form'][] = \app\Lang::term('Sign in failed. We do not know of any such user or email.');
				return $this->signin_view($errors);
			}

			// check password attempts
			if ($user['pwdattempts'] > $auth_config['catptcha.signin.attempts'])
			{
				$_POST['show_captcha'] = true;

				if ( ! isset($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']))
				{

					$errors['form'][] = \app\Lang::key('login.passwordattemps', [':number' => $user['pwdattempts']]);
					return $this->signin_view($errors);
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

					$errors['form'][] = \app\Lang::term('You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.');
					\app\Model_User::bump_pwdattempts($user['id']);

					return $this->signin_view($errors);
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
				$errors['password'] = [\app\Lang::term('The password you have entered is incorect.')];
				\app\Model_User::bump_pwdattempts($user['id']);
				return $this->signin_view($errors);
			}

			// check if user is active
			if ( ! $user['active'])
			{

				$errors['form'][] = \app\Lang::key('mjolnir:access/your-account-is-inactive');
				\app\Model_User::send_activation_email($user['id']);
				return $this->signin_view($errors);
			}

			// logged in
			if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on')
			{
				\app\User::remember($user['id']);
			}
			else # remember_me === off
			{
				\app\Auth::signin($user['id'], \app\Model_User::role_for($user['id']));
			}

			\app\Server::redirect(\app\Server::url_homepage($user));

			// no default frontend
			$this->forward('mjolnir:access/auth.route', ['action' => 'lobby']);
		}
		else # user === null
		{
			return $this->signin_view();
		}
	}

	/**
	 * Alias
	 *
	 * @return \mjolnir\types\Renderable
	 */
	function public_signin()
	{
		return $this->action_signin();
	}

	/**
	 * Sign Out user out of system.
	 */
	function action_signout()
	{
		\app\Auth::signout();
		\app\Server::redirect(\app\CFS::config('mjolnir/auth')['default.signin']);
	}

	/**
	 * Alias
	 *
	 * @return \mjolnir\types\Renderable
	 */
	function public_signout()
	{
		return $this->action_signout();
	}

	/**
	 * Setup view used when signing in.
	 *
	 * @return \mjolnir\types\Renderable
	 */
	function signin_view($errors = null)
	{
		$view = $this->public_index()
			->pass('context', $this);

		if ($errors !== null)
		{
			$errors = [ 'mjolnir:access/signin.errors' => $errors ];
			$view->bind('errors', $errors);
		}
		else # no errors
		{
			$view->pass('errors', []);
		}

		return $view;
	}

} # trait
