<?php namespace mjolnir\access;

/**
 * @package    mjolnir
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
			\app\Session::set('facebook_state', null);
			$authconfig = \app\CFS::config('mjolnir/auth');
			$provider = $authconfig['signin']['facebook'];

			$appid = $provider['AppID'];
			$appsecret = $provider['AppSecret'];
			$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
			$redirect = $protocol.':'.\app\URL::route('mjolnir:access/channel.route')
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
				  . $params['access_token'] . '&fields=id,name,picture.type(large),first_name,last_name,gender,locale,username,link,email,verified';

				$user = \json_decode(\file_get_contents($graph_url));

				// save in session the access token for later use
				\app\Session::set($authconfig['signin']['facebook']['session.token.name'], $params['access_token']);

				$signedin_user = \app\Auth::inferred_signin($user->username, $user->email, 'facebook', $user);

				\app\Server::redirect(\app\Server::url_homepage($signedin_user));
			}
			else # error in `code` to `token` excahnge
			{
				\mjolnir\log('OAuth', 'Facebook: "'.$response['message'].'" Code: '.$response['code']);
				throw new \app\Exception_NotAllowed('An error has occured during the access flow, please try again.');
			}
		}
		else # invalid state, assume CSFR
		{
			\mjolnir\log('Hacking', 'Possible CSFR attempt.');
			throw new \app\Exception_NotAllowed('Potential CSFR attack detected. Access denied.');
		}
	}

	private static $signin_url = null;

	static function signin_url()
	{
		if (self::$signin_url === null)
		{
			$state = \app\Session::get('facebook_state', null);

			if ($state === null)
			{
				$state = \app\Session::set('facebook_state', \md5(\uniqid(\rand(), true)));
			}

			$authconfig = \app\CFS::config('mjolnir/auth');
			$provider = $authconfig['signin']['facebook'];
			$appid = $provider['AppID'];
			$redirect = \app\URL::route('mjolnir:access/channel.route')->url(['provider' => 'facebook']);
			$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';

			$permissions = $authconfig['signin']['facebook']['scope'];

			self::$signin_url = 'https://www.facebook.com/dialog/oauth?client_id='
				. $appid.'&amp;redirect_uri='.$protocol.':'.$redirect.'&amp;scope='.$permissions.'&amp;state='.$state;
		}

		return self::$signin_url;
	}

} # class
