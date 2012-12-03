<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Library
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirSignup
{
	/**
	 * Action: Sign Up user into system
	 */
	function action_signup()
	{
		if ( ! \app\CFS::config('mjolnir/a12n')['standard.signup'])
		{
			\app\Server::redirect(\app\CFS::config('mjolnir/a12n')['default.signin']);
		}

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

				$errors['form'][] = \app\Lang::tr('You\'ve failed the <a href="http://en.wikipedia.org/wiki/CAPTCHA">CAPTCHA</a> check.');

				$this->signup_view($errors);
			}
			else # captcha test passed
			{
				$errors = \app\Model_User::push($_POST);
				
				if ($errors === null)
				{
					$this->signup_success();
					
					$user = \app\Model_User::last_inserted_id();
					\app\Model_User::send_activation_email($user['id']);
					
					\app\Server::redirect(\app\CFS::config('mjolnir/a12n')['default.signin']);
				}
				else # got errors
				{
					$this->signup_view($errors);
				}
			}
		}
		else # treat as GET
		{
			if (isset($_GET['key'], $_GET['user']))
			{
				if (\app\Model_User::confirm_token($_GET['user'], $_GET['key'], 'mjolnir:signup'))
				{
					\app\Model_User::activate_account($_GET['user']);
					\app\Notice::make(\app\Lang::msg('mjolnir:account_active'));
				}
				else # error checking token
				{
					\app\Notice::make(\app\Lang::msg('mjolnir:invalid_token'));
					
				}
				\app\Server::redirect(\app\CFS::config('mjolnir/a12n')['default.signin']);
			}
			
			$this->signup_view();
		}
	}
	
	/**
	 * Hook; called on succesful signup.
	 */
	function signup_success()
	{
	    // overwrite hook
	}

} # trait
