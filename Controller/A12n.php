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
	use \app\Trait_Controller_MjolnirSignin;
	use \app\Trait_Controller_MjolnirSignup;
	use \app\Trait_Controller_MjolnirPwdReset;
	use \app\Trait_Controller_MjolnirEmails;

	/**
	 * @var string
	 */
	protected static $target = null;

	/**
	 * Exceute before any action
	 */
	function before()
	{
		\app\GlobalEvent::fire('webpage:title', 'Access');
	}

	/**
	 * Action: Show signin or user info
	 */
	function action_index()
	{
		$relay = $this->layer->get_relay();

		if (\app\A12n::instance()->role() === \app\A12n::guest())
		{
			\app\Server::redirect(\app\CFS::config('mjolnir/a12n')['default.signin']);
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

	// ------------------------------------------------------------------------
	// etc

	/**
	 * Setup view used when signing in.
	 */
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

	/**
	 * Setup view used when signing up.
	 */
	function signup_view($errors = null)
	{
		$relay = $this->layer->get_relay();

		if ($relay['target'] === null)
		{
			\app\GlobalEvent::fire('webpage:title', 'Sign Up · Access');

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

	/**
	 * Setup view used when signing up.
	 */
	function pwdreset_view($errors = null)
	{
		$relay = $this->layer->get_relay();

		if ($relay['target'] === null)
		{
			\app\GlobalEvent::fire('webpage:title', 'Password Reset · Access');

			$view = \app\ThemeView::instance()
				->theme('mjolnir/access')
				->style('default')
				->target('pwdreset')
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
			$errors = ['\mjolnir\a12n\pwdreset' => $errors];
			$view->errors($errors);
		}

		$this->body($view->render());
	}

	/**
	 * Setup view used when setting up emails.
	 */
	function emails_view($errors = null)
	{
		$relay = $this->layer->get_relay();
		
		if ($relay['target'] === null)
		{
			\app\GlobalEvent::fire('webpage:title', \app\Lang::tr('Emails · Access'));

			$view = \app\ThemeView::instance()
				->theme('mjolnir/access')
				->style('default')
				->target('emails')
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
			$errors = ['\mjolnir\a12n\emails' => $errors];
			$view->errors($errors);
		}

		$this->body($view->render());
	}
	
} # class
