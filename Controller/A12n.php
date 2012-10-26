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
	use \app\Trait_Controller_MjolnirSingin;
	use \app\Trait_Controller_MjolnirSingup;
	
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
