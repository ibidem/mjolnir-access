<?php namespace mjolnir\access;

require_once \app\CFS::dir('vendor/hybridauth').'/Hybrid/Auth.php';

/**
 * @package    mjolnir
 * @category   AccessChannel
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class AccessChannel_Universal extends \app\Instantiatable
{
	function authorize($provider_name)
	{
		\app\Session::start(); # required by hybrid auth

		$authconfig = \app\CFS::config('mjolnir/auth');
		$provider = $authconfig['signin'][$provider_name];

		$provider_key = $provider['hybridauth.key'];
		$config = array
			(
				'base_url' => \app\URL::route('mjolnir:access/endpoint.route')->url(),
				'providers' => array
					(
						$provider_key => array
							(
								'enabled' => true,
								'keys' => $provider['keys'],
								'scope' => $provider['scope'],
							)
					),
				'debug_mode' => false,
				'debug_file' => '',
			);

		try
		{
			// hybridauth EP
			$hybridauth = new \Hybrid_Auth($config);

			// automatically try to login
			$handler = $hybridauth->authenticate($provider_key);

			// return TRUE or False
			$is_user_logged_in = $handler->isUserConnected();

			// get the user profile
			$user_profile = $handler->getUserProfile();

			if (empty($user_profile->displayName) && empty($user_profile->email))
			{
				throw new \app\Exception_NotApplicable
					('Inconsufficient information passed back from provider. Please try another.');
			}

			if (empty($user_profile->displayName))
			{
				$display_name = \preg_replace('#@.*$#', '', $user_profile->email);
			}
			else # not empty
			{
				$display_name = $user_profile->displayName;
			}

			if (empty($user_profile->email))
			{
				$email = $display_name.'@'.$provider_name;
			}
			else # not empty
			{
				$email = $user_profile->email;
			}

			$signedin_user = \app\Auth::inferred_signin($display_name, $email, $provider_name);

			\app\Server::redirect(\app\Server::url_homepage($signedin_user));
		}
		catch (\app\Exception_NotApplicable $e)
		{
			throw $e;
		}
		catch (\Exception $e)
		{
			switch( $e->getCode() ){
				case 0 : $message = "Unspecified error.";
						break;
				case 1 : $message = "Hybridauth configuration error.";
						break;
				case 2 : $message = "Provider not properly configured.";
						break;
				case 3 : $message = "Unknown or disabled provider.";
						break;
				case 4 : $message = "Missing provider application credentials.";
						break;
				case 5 : $message = "Authentification failed. "
						  . "The user has canceled the authentication or the provider refused the connection.";
					   break;
				case 6 : $message = "User profile request failed. Most likely the user is not connected "
						  . "to the provider and he should to authenticate again.";
					   $handler->logout();
					   break;
				case 7 : $message = "User not connected to the provider.";
					   $handler->logout();
					   break;
				case 8 : $message = "Provider does not support this feature.";
					break;
			}

			if (\app\Auth::role() === \app\Auth::Guest)
			{
				throw new \Exception('AccessChannel: '.$message);
			}
			else # user is already signed in
			{
				\mjolnir\exception_handler($e);
			}
		}
	}

} # class
