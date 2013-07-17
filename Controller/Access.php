<?php namespace mjolnir\access;

\app\Session::start();

require_once \app\CFS::dir('vendor/hybridauth').'Hybrid/Auth.php';
require_once \app\CFS::dir('vendor/hybridauth').'Hybrid/Endpoint.php';

/**
 * @package    mjolnir
 * @category   Security
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_Access extends \app\Puppet implements \mjolnir\types\Controller
{
	use \app\Trait_Controller
		{
			preprocess as protected trait_preprocess;
		}
	use \app\Trait_Controller_MjolnirSignin;
	use \app\Trait_Controller_MjolnirSignup;
	use \app\Trait_Controller_MjolnirPwdReset;
	use \app\Trait_Controller_MjolnirEmails;

	protected static $grammar = [ 'access' ];

	/**
	 * ...
	 */
	function preprocess()
	{
		$this->trait_preprocess();

		$this->channel()
			->get('layer:html')
			->set('title', 'Access');
	}

	/**
	 * ...
	 */
	function redirect_signedin_users()
	{
		if (\app\Auth::role() !== \app\Auth::Guest)
		{
			\app\Server::redirect(\app\URL::href('mjolnir:access/auth.route'));
		}
	}

	/**
	 * Sign Out user out of system.
	 */
	function action_signout()
	{
		\app\Auth::signout();
		\app\Server::redirect(\app\URL::href('mjolnir:access/auth.route'));
	}

	/**
	 * Channel manager
	 */
	function action_channel()
	{
		// load channel
		$provider = $this->channel()->get('relaynode')->get('provider', null);
		if ($provider === null)
		{
			throw new \app\Exception('Provider not specified.');
		}

		if ($provider == 'universal')
		{
			$id = $this->channel()->get('relaynode')->get('id', null);
			if ($id === null)
			{
				throw new \app\Exception('Provider id not specified.');
			}

			// this hard coded security test is intentional
			$register = \app\CFS::config('mjolnir/auth')['signin'][$id]['register'];
			if (\app\Register::pull([$register])[$register] !== 'on')
			{
				\mjolnir\log('Hacking', 'Attempt to access unauthorized area.');
				throw new \app\Exception_NotApplicable('Access Denied.');
			}

			$channel_class = '\app\AccessChannel_Universal';
			$c = $channel_class::instance();
			$c->authorize($id);
		}
		else # non-universal
		{
			// this hard coded security test is intentional
			$register = \app\CFS::config('mjolnir/auth')['signin'][$provider]['register'];
			if (\app\Register::pull([$register])[$register] !== 'on')
			{
				\mjolnir\log('Hacking', 'Attempt to access unauthorized area.');
				throw new \app\Exception('Access Denied. Provider is not enabled.');
			}

			$channel_class = '\app\AccessChannel_'.\ucfirst($provider);
			$c = $channel_class::instance();
			$c->authorize();
		}
	}

	/**
	 * Endpoind for HybridAuth's protocol
	 */
	function action_endpoint()
	{
		\Hybrid_Endpoint::process();
	}

	/**
	 * @return \mjolnir\types\Renderable
	 */
	function action_index()
	{
		if (\app\Auth::role() === \app\Auth::Guest)
		{
			\app\Server::redirect(\app\URL::href('mjolnir:access/auth.route', ['action' => 'signin']));
		}

		$this->channel()->set('web:title', 'Lobby · Access');

		$theme = \app\Theme::instance('mjolnir/access')
			->channel_is($this->channel());

		return \app\ThemeView::fortarget('lobby', $theme)
			->pass('context', $this)
			->pass('control', $this);
	}

	// ------------------------------------------------------------------------
	// Context

	/**
	 * @return array list of open authentication providers
	 */
	function authorized_a12n_providers()
	{
		// get all supported providers
		$providers = \app\CFS::config('mjolnir/auth')['signin'];

		// filter to enabled providers
		$enabled_providers = [];
		foreach ($providers as $provider)
		{
			$key = $provider['register'];
			$switch = \app\Register::pull([$key]);
			if ($switch[$key] == 'on')
			{
				$enabled_providers[] = $provider;
			}
		}

		return $enabled_providers;
	}

	/**
	 * Check if current user (ie. guest) can use signup feature.
	 *
	 * @return boolean
	 */
	function can_signup()
	{
		return \app\Access::can('mjolnir:access/auth.route', ['action' => 'signup'])
			&& \app\CFS::config('mjolnir/auth')['standard.signup']
			&& \app\Register::pull(['mjolnir:access/signup/public'])['mjolnir:access/signup/public'] === 'on';
	}

	// ------------------------------------------------------------------------
	// etc

	/**
	 * @return \mjolnir\types\ThemeView
	 */
	function setup_view($errors, $webtitle, $target, $errortarget)
	{
		$this->channel()->set('web:title', $webtitle);

		$theme = \app\Theme::instance('mjolnir/access')
			->channel_is($this->channel());

		$view = \app\ThemeView::fortarget($target, $theme);

		if ($errors !== null)
		{
			$errors = [$errortarget => $errors];
			$view->pass('errors', $errors);
		}
		else # no errors
		{
			$view->pass('errors', []);
		}

		return $view
			->pass('context', $this)
			->pass('control', $this);
	}

	/**
	 * @return \mjolnir\types\Renderable
	 */
	function signin_view($errors = null)
	{
		return $this->setup_view
			(
				$errors,
				'Sign In · Access',
				'signin',
				'mjolnir:access/signin.errors'
			);
	}

	/**
	 * @return \mjolnir\types\Renderable
	 */
	function signup_view($errors = null)
	{
		return $this->setup_view
			(
				$errors,
				'Sign Up · Access',
				'signup',
				'mjolnir:access/signup.errors'
			);
	}

	/**
	 * @return \mjolnir\types\Renderable
	 */
	function pwdreset_view($errors = null)
	{
		return $this->setup_view
			(
				$errors,
				'Password Reset · Access',
				'pwdreset',
				'mjolnir:access/pwdreset.errors'
			);
	}

	/**
	 * @return \mjolnir\types\Renderable
	 */
	function emails_view($errors = null)
	{
		return $this->setup_view
			(
				$errors,
				'Emails · Access',
				'emails',
				'mjolnir:access/emails.errors'
			);
	}

} # class
