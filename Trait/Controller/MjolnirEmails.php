<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Controller_MjolnirEmails
{
	/**
	 * @return array user
	 */
	function mainemail()
	{
		return \app\UserLib::entry(\app\Auth::id())['email'];
	}

	/**
	 * @return array emails
	 */
	function secondaryemails($page = null, $limit = null, $offset = 0)
	{
		return \app\SecondaryEmailLib::entries($page, $limit, $offset, [], ['user' => \app\Auth::id()]);
	}

	/**
	 * Email manager for handlign main email changes along with adding aditional
	 * email addresses for authorization purposes.
	 */
	function action_emails()
	{
		if (\app\Auth::role() === \app\Auth::Guest)
		{
			throw new \app\Exception_NotAllowed
				('Page is only available when you are signed in.');
		}

		if (\app\Server::request_method() === 'POST')
		{
			if ( ! isset($_POST['action']))
			{
				throw new \Exception('Undefined action when using emails manager.');
			}

			if ($_POST['action'] === 'add-secondary-email')
			{
				$this->add_secondary_email();
			}
			else if ($_POST['action'] === 'change-main-email')
			{
				$this->change_main_email();
			}
			else if ($_POST['action'] === 'remove-secondary-email')
			{
				$this->remove_secondary_email();
			}
			else # unknown action
			{
				throw new \Exception('Undefined action when using emails manager: '.$_POST['action']);
			}

			return $this->emails_view();
		}
		else # treat as GET
		{
			if (isset($_GET['action']))
			{
				if ($_GET['action'] === 'change_main_email')
				{
					$this->change_main_email($_GET['code']);
				}
				else if ($_GET['action'] === 'add_secondary_email')
				{
					$this->add_secondary_email($_GET['code']);
				}
				else # unknown action
				{
					throw new \Exception('Unknown action: '.$_GET['action']);
				}
			}

			return $this->emails_view();
		}
	}

	/**
	 * ...
	 */
	protected function add_secondary_email($code = null)
	{
		if ($code === null)
		{
			$token = \app\UserLib::token(\app\Auth::id(), '+3 hours', 'add-secondary-email');

			$change_email_url = \app\CFS::config('mjolnir/auth')['default.emails_manager'].'?action=add_secondary_email&code='.$token;

			\app\Session::set('mjolnir:add-secondary-email:email', $_POST['email']);
			\app\Session::set('mjolnir:add-secondary-email:user', \app\Auth::id());

			$base = \app\CFS::config('mjolnir/base');

			// send code via email
			$sent = \app\Email::instance()
				->send
				(
					$_POST['email'],
					'no-reply@'.$base['domain'],
					\app\Lang::term('Confirmation of Email ownership'),
					\app\Lang::key('mjolnir:access/email-visit-url-to-finish', [':url' => $change_email_url])
				);

			if ($sent)
			{
				\app\Notice::make(\app\Lang::term('An email has been sent, at :email, with further instructions.', [':email' => $_POST['email']]))
					->classes(['alert-warning'])
					->save();
			}
			else # sent = 0
			{
				\app\Notice::make(\app\Lang::term('Failed to send confirmation email.'))
					->classes(['alert-error'])
					->save();
			}
		}
		else # recieved code
		{
			$email = \app\Session::get('mjolnir:add-secondary-email:email', null);
			$user  = \app\Session::get('mjolnir:add-secondary-email:user', null);

			if ($email === null || $user === null || $user !== \app\Auth::id())
			{
				throw new \app\Exception_NotApplicable
					(\app\Lang::term('Potential security violation. Operation terminated.'));
			}

			// verify
			if (\app\UserLib::confirm_token(\app\Auth::id(), $code, 'add-secondary-email'))
			{
				\app\UserLib::add_secondary_email(\app\Auth::id(), $email);

				\app\Notice::make(\app\Lang::term('Succesfully added secondary email.'))
					->classes(['alert-info'])
					->save();
			}
			else # failed token check
			{
				throw new \app\Exception_NotAllowed('Invalid token provided.');
			}

			// clear session variables
			\app\Session::set('mjolnir:add-secondary-email:email', null);
			\app\Session::set('mjolnir:add-secondary-email:user', null);
		}
	}

	/**
	 * ...
	 */
	protected function change_main_email($code = null)
	{
		if ($code === null)
		{
			$token = \app\UserLib::token(\app\Auth::id(), '+3 hours', 'change-main-email');

			$change_email_url = \app\CFS::config('mjolnir/auth')['default.emails_manager'].'?action=change_main_email&code='.$token;

			\app\Session::set('mjolnir:change-main-email:email', $_POST['email']);
			\app\Session::set('mjolnir:change-main-email:user', \app\Auth::id());

			$base = \app\CFS::config('mjolnir/base');

			// send code via email
			$sent = \app\Email::instance()
				->send
				(
					$_POST['email'],
					'no-reply@'.$base['domain'],
					\app\Lang::term('Confirmation of Email ownership'),
					\app\Lang::key('mjolnir:access/email-visit-url-to-finish', [':url' => $change_email_url])
				);

			if ($sent)
			{
				\app\Notice::make(\app\Lang::term('An email has been sent, at :email, with further instructions.', [':email' => $_POST['email']]))
					->classes(['alert-warning'])
					->save();
			}
			else # ! $sent
			{
				\app\Notice::make(\app\Lang::term('Failed to send confirmation email.'))
					->classes(['alert-error'])
					->save();
			}
		}
		else # recieved code
		{
			$email = \app\Session::get('mjolnir:change-main-email:email', null);
			$user  = \app\Session::get('mjolnir:change-main-email:user', null);

			if ($email === null || $user === null || $user !== \app\Auth::id())
			{
				throw new \app\Exception_NotApplicable
					(\app\Lang::term('Potential security violation. Operation terminated.'));
			}

			// verify
			if (\app\UserLib::confirm_token(\app\Auth::id(), $code, 'change-main-email'))
			{
				\app\UserLib::change_email(\app\Auth::id(), $email);
				\app\Notice::make(\app\Lang::term('Main Email updated succesfully.'))
					->classes(['alert-info'])
					->save();
			}
			else # failed token check
			{
				throw new \app\Exception_NotAllowed('Invalid token provided.');
			}

			// clear session variables
			\app\Session::set('mjolnir:change-main-email:email', null);
			\app\Session::set('mjolnir:change-main-email:user', null);
		}
	}

	/**
	 * ...
	 */
	protected function remove_secondary_email()
	{
		// verify secondary email belongs to user
		$entry = \app\SecondaryEmailLib::entry($_POST['id']);

		if ($entry['user'] == \app\Auth::id())
		{
			\app\SecondaryEmailLib::delete([$_POST['id']]);
		}
		else # major security violation
		{
			throw new \app\Exception_NotAllowed('Attempting to delete email of another user. (current: ['.\app\Auth::id().'] email owner: ['.$entry['user'].'])');
		}
	}

} # trait
