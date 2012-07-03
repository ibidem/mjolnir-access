<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   AccessChannel
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class AccessChannel_Facebook extends \app\Instantiatable
{
	function authorize()
	{
		$code = $_REQUEST['code'];
		$state = isset($_REQUEST['state']) ? $_REQUEST['state'] : null;
		$session_state = \app\Session::get('facebook_state');
		
		if($session_state && ($session_state === $state)) 
		{
			$provider = \app\CFS::config('ibidem/a12n')['signin']['facebook'];
			
			$appid = $provider['AppID'];
			$appsecret = $provider['AppSecret'];
			$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
			$redirect = $protocol.':'.\app\Relay::route('\ibidem\access\channel')
				->url(['provider' => 'facebook']);
			
			$token_url = "https://graph.facebook.com/oauth/access_token?"
			  . "client_id=" . $appid . "&redirect_uri=" . $redirect 
			  . "&client_secret=" . $appsecret . "&code=" . $code;

			$response = \file_get_contents($token_url);
			$params = null;
			
			// check for error
			if ( ! isset($response['error']))
			{
				\parse_str($response, $params);

				$graph_url = "https://graph.facebook.com/me?access_token=" 
				  . $params['access_token'];

				$user = \json_decode(\file_get_contents($graph_url));

				\app\A12n::inferred_signin($email, 'facebook');
			}
			else # error in `code` to `token` excahnge
			{
				\app\Log::message('Feedback', 'Facebook: "'.$response['message'].'" Code: '.$response['code'], 'oauth');
				throw new \app\Exception_NotAllowed('An error has occured during the access flow, please try again.');
			}
		}
		else # invalid state, assume CSFR
		{
			\app\Log::message('Alert', 'Possible CSFR attempt.', '+security');
			throw new \app\Exception_NotAllowed('Potential CSFR attack detected. Access denied.');
		}
	}

} # class
