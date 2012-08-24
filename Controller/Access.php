<?php namespace ibidem\access;

\app\Session::start();

require_once \app\CFS::dir('vendor/hybridauth').'/Hybrid/Auth.php';
require_once \app\CFS::dir('vendor/hybridauth').'/Hybrid/Endpoint.php';

/**
 * @package    ibidem
 * @category   Controller
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_Access extends \app\Controller_Web
{
	protected static $target = null;

	function action_channel()
	{
		// load channel
		$provider = $this->params->get('provider', null);
		if ($provider === null)
		{
			throw new \app\Exception_NotApplicable('Provider not specified.');
		}
		
		if ($provider == 'universal')
		{
			$id = $this->params->get('id', null);
			if ($id === null)
			{
				throw new \app\Exception_NotApplicable('Provider id not specified.');
			}
			
			// this hard coded security test is intentional
			$register = \app\CFS::config('ibidem/a12n')['signin'][$id]['register'];
			if (\app\Register::pull([$register])[$register] !== 'on')
			{
				throw new \app\Exception_NotApplicable('Access Denied.');
				\app\Log::message('sec error', 'Attempt to access unauthorized area.');
			}
			
			$channel_class = '\app\AccessChannel_Universal';
			$c = $channel_class::instance();
			$c->authorize($id);
		}
		else # non-universal
		{
			// this hard coded security test is intentional
			$register = \app\CFS::config('ibidem/a12n')['signin'][$provider]['register'];
			if (\app\Register::pull([$register])[$register] !== 'on')
			{
				throw new \app\Exception_NotApplicable('Access Denied.');
				\app\Log::message('sec error', 'Attempt to access unauthorized area.');
			}
			
			$channel_class = '\app\AccessChannel_'.\ucfirst($provider);
			$c = $channel_class::instance();
			$c->authorize();
		}
	}
	
	function action_endpoint()
	{
		\Hybrid_Endpoint::process();
	}

} # class
