<?php namespace mjolnir\access;

\app\Session::start();

require_once \app\CFS::dir('vendor/hybridauth').'Hybrid/Auth.php';
require_once \app\CFS::dir('vendor/hybridauth').'Hybrid/Endpoint.php';

/**
 * @package    mjolnir
 * @category   Controller
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_Access extends \app\Controller_Contextual
{
	/**
	 * @var string
	 */
	protected static $target = null;

	/**
	 * Action: channel manager
	 */
	function action_channel()
	{
		// load channel
		$provider = $this->params->get('provider', null);
		if ($provider === null)
		{
			throw new \app\Exception('Provider not specified.');
		}

		if ($provider == 'universal')
		{
			$id = $this->params->get('id', null);
			if ($id === null)
			{
				throw new \app\Exception('Provider id not specified.');
			}

			// this hard coded security test is intentional
			$register = \app\CFS::config('mjolnir/a12n')['signin'][$id]['register'];
			if (\app\Register::pull([$register])[$register] !== 'on')
			{
				\mjolnir\log('SecurityError', 'Attempt to access unauthorized area.', '+Security/');
				throw new \app\Exception_NotApplicable('Access Denied.');
			}

			$channel_class = '\app\AccessChannel_Universal';
			$c = $channel_class::instance();
			$c->authorize($id);
		}
		else # non-universal
		{
			// this hard coded security test is intentional
			$register = \app\CFS::config('mjolnir/a12n')['signin'][$provider]['register'];
			if (\app\Register::pull([$register])[$register] !== 'on')
			{
				\mjolnir\log('SecurityError', 'Attempt to access unauthorized area.', '+Security/');
				throw new \app\Exception('Access Denied. Provider is not enabled.');
			}

			$channel_class = '\app\AccessChannel_'.\ucfirst($provider);
			$c = $channel_class::instance();
			$c->authorize();
		}
	}

	/**
	 * Action: endpoind for HybridAuth's protocol
	 */
	function action_endpoint()
	{
		\Hybrid_Endpoint::process();
	}

} # class
