<?php namespace ibidem\access;

\app\Session::start();

require \app\CFS::dir('vendor/hybridauth').'/Hybrid/Auth.php';
require \app\CFS::dir('vendor/hybridauth').'/Hybrid/Endpoint.php';

/**
 * @package    ibidem
 * @category   Controller
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Controller_Access extends \app\Controller_HTTP
{
	function action_channel()
	{
		// load channel
		$provider = $this->params->get('provider', null);
		if ($provider === null)
		{
			throw new \app\Exception_NotApplicable('Provider not specified.');
		}
		
		$channel_class = '\app\AccessChannel_'.\ucfirst($provider);
		$c = $channel_class::instance();
		
		$c->authorize();
	}
	
	function action_endpoint()
	{
		\Hybrid_Endpoint::process();
	}

} # class
