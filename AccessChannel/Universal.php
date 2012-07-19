<?php namespace ibidem\access;

require_once \app\CFS::dir('vendor/hybridauth').'/Hybrid/Auth.php';

/**
 * @package    ibidem
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
		
		$provider = \app\CFS::config('ibidem/a12n')['signin'][$provider_name];
		
		$provider_key = $provider['hybridauth.key'];
		$config = array
			(
				'base_url' => 'http:'.\app\Relay::route('\ibidem\access\endpoint')->url(),
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
			
			\app\A12n::inferred_signin($display_name, $email, $provider_name);
				
			\app\Layer_HTTP::redirect('\ibidem\access\a12n');
		}
		catch (\app\Exception_NotApplicable $e)
		{
			throw $e;
		}
		catch (\Exception $e)
		{
			switch( $e->getCode() ){ 
				case 0 : echo "Unspecified error."; break;
				case 1 : echo "Hybridauth configuration error."; break;
				case 2 : echo "Provider not properly configured."; break;
				case 3 : echo "Unknown or disabled provider."; break;
				case 4 : echo "Missing provider application credentials."; break;
				case 5 : echo "Authentification failed. " 
						  . "The user has canceled the authentication or the provider refused the connection."; 
					   break;
				case 6 : echo "User profile request failed. Most likely the user is not connected "
						  . "to the provider and he should to authenticate again."; 
					   $handler->logout();
					   break;
				case 7 : echo "User not connected to the provider."; 
					   $handler->logout();
					   break;
				case 8 : echo "Provider does not support this feature."; break;
			} 
		}
	}

} # class
