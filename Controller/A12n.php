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
			$view->errors($errors);
		}

		$this->body($view->render());
	}

	/**
	 * @throws \app\Exception_NotAllowed
	 */
	function action_signin()
	{
		if (\app\A12n::instance()->role() !== \app\A12n::guest())
		{
			\app\Server::redirect(\app\URL::href('\mjolnir\access\a12n'));
		}

		if (\app\Server::request_method() === 'POST')
		{
			$user = \app\Model_User::signin_check($_POST);

			if ($user !== null)
			{
				// logged in
				if (isset($_POST['remember_me']) && $_POST['remember_me'] === 'on')
				{
					\app\A12n::remember_user($user);
				}
				else # remember_me === off
				{
					\app\A12n::signin($user, \app\Model_User::role_for($user));
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
			else # signin failed
			{
				$errors = array
					(
						'\mjolnir\a12n\signin' => array
							(
								'form' => ['Sign in failed. Please check your credentials or try a different password.']
							)
					);

				$this->signin_view($errors);
			}
		}
		else # user === null
		{
			$this->signin_view();
		}


	}

	function action_signout()
	{
		\app\A12n::signout();
		$a12n_config = \app\CFS::config('mjolnir/a12n');
		\app\Server::redirect($a12n_config['signout.redirect']);
	}


} # class
