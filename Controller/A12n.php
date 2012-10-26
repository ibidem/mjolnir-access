<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_A12n extends \app\Controller_Web
{
	protected static $target = null;

	function before()
	{
		\app\GlobalEvent::fire('webpage:title', 'Access');
	}

	function action_index()
	{
		$relay = $this->layer->get_relay();

		if (\app\A12n::instance()->role() === \app\A12n::guest())
		{
			\app\Server::redirect(\app\URL::href('\mjolnir\access\a12n', ['action' => 'signin']));
		}

		\app\GlobalEvent::fire('webpage:title', 'Lobby · Access');

		$this->body
			(
				\app\ThemeView::instance()
					->theme('mjolnir/access')
					->style('default')
					->target('lobby')
					->layer($this->layer)
					->context($relay['context']::instance())
					->control($relay['control']::instance())
					->render()
			);
	}

	/**
	 * Action: Sign In user
	 */
	function action_signin()
	{
		if (\app\A12n::instance()->role() !== \app\A12n::guest())
		{
			\app\Server::redirect(\app\URL::href('\mjolnir\access\a12n'));
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
				$errors['form'][] = 'Sign in failed. We do not know of any such user or email.';
				$this->signin_view($errors); 
				return;
			}

			// check password attempts
			if ($user['pwdattempts'] > $a12n_config['catptcha.signin.attempts'])
			{
				$_POST['show_captcha'] = true;
				
				if ( ! isset($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']))
				{	
					$errors['form'][] = 'You\'ve performed '.$user['pwdattempts'].' unnsuccesful password attempts; <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check is required.';
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

					$errors['form'][] = 'You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.';
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
				$errors['password'] = ['The password you have entered is inccorect.'];
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
			if (isset($base_config['site:frontend']))
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
		\app\Server::redirect($a12n_config['signout.redirect']);
	}

	/**
	 * Action: Sign Up user into system
	 */
	function action_signup()
	{
		if (\app\Server::request_method() === 'POST')
		{
			$_POST['role'] = \app\Model_Role::role_by_name('member');
			
			// check recaptcha
			$error = \app\ReCaptcha::verify
				(
					$_POST['recaptcha_challenge_field'], 
					$_POST['recaptcha_response_field']
				);
			
			if ($error !== null)
			{
				$errors = \app\Model_User::check($_POST)->errors();
				if ( ! isset($errors['form']))
				{
					$errors['form'] = [];
				}
				
				$errors['form'][] = 'You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.';
				
				$this->signup_view($errors);
			}
			else # captcha test passed
			{
				$errors = \app\Model_User::push($_POST);

				if ($errors === null)
				{
					\app\Server::redirect(\app\URL::href('\mjolnir\access\a12n', ['action' => 'signin']));
				}
				else # got errors
				{
					$this->signup_view($errors);
				}
			}
		}
		else # treat as GET
		{
			$this->signup_view();
		}
	}
	
	// ------------------------------------------------------------------------
	// etc
	
	function signin_view($errors = null)
	{
		$relay = $this->layer->get_relay();

		if ($relay['target'] === null)
		{
			\app\GlobalEvent::fire('webpage:title', 'Sign In · Access');

			$view = \app\ThemeView::instance()
				->theme('mjolnir/access')
				->style('default')
				->target('signin')
				->layer($this->layer)
				->context($relay['context']::instance())
				->control($relay['control']::instance());
		}
		else # target provided
		{
			$view = \app\ThemeView::instance()
				->target($relay['target'])
				->layer($this->layer)
				->context($relay['context']::instance())
				->control($relay['control']::instance());
		}

		if ($errors !== null)
		{
			$errors = ['\mjolnir\a12n\signin' => $errors];
			$view->errors($errors);
		}

		$this->body($view->render());
	}

	function signup_view($errors = null)
	{
		$relay = $this->layer->get_relay();

		if ($relay['target'] === null)
		{
			\app\GlobalEvent::fire('webpage:title', 'Sign Up');

			$view = \app\ThemeView::instance()
				->theme('mjolnir/access')
				->style('default')
				->target('signup')
				->layer($this->layer)
				->context($relay['context']::instance())
				->control($relay['control']::instance());
		}
		else # target provided
		{
			$view = \app\ThemeView::instance()
				->target($relay['target'])
				->layer($this->layer)
				->context($relay['context']::instance())
				->control($relay['control']::instance());
		}

		if ($errors !== null)
		{
			$errors = ['\mjolnir\a12n\signup' => $errors];
			$view->errors($errors);
		}

		$this->body($view->render());
	}

} # class
