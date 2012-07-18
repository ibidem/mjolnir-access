<?php namespace ibidem\access;

require_once \app\CFS::dir('vendor/hybridauth').'/Hybrid/Auth.php';

/**
 * @package    ibidem
 * @category   AccessChannel
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class AccessChannel_Twitter extends \app\Instantiatable
{
	function authorize()
	{
		\app\Session::start(); # required by hybrid auth
		
		$provider = \app\CFS::config('ibidem/a12n')['signin']['twitter'];
		$base_config = \app\CFS::config('ibidem/base');
		
		$config = array
			(
				'base_url' => 'http:'.\app\Relay::route('\ibidem\access\endpoint')->url(),
				'providers' => array
					(
						'Twitter' => array 
							( 
								'enabled' => true,
								'keys' => array 
									( 
										'key' => $provider['ConsumerKey'], 
										'secret' => $provider['ConsumerSecret'] 
									) 
							)
					),
				'debug_mode' => false,
				'debug_file' => '',
			);
		
		try
		{
			// hybridauth EP
			$hybridauth = new \Hybrid_Auth( $config );

			// automatically try to login with Twitter
			$twitter = $hybridauth->authenticate( "Twitter" );

			// return TRUE or False <= generally will be used to check if the user is connected to twitter before getting user profile, posting stuffs, etc..
			$is_user_logged_in = $twitter->isUserConnected();

			// get the user profile 
			$user_profile = $twitter->getUserProfile();

			// access user profile data
			echo "Ohai there! U are connected with: <b>{$twitter->id}</b><br />";
			echo "As: <b>{$user_profile->displayName}</b><br />";
			echo "And your provider user identifier is: <b>{$user_profile->identifier}</b><br />";  

			// or even inspect it
			echo "<pre>" . print_r( $user_profile, true ) . "</pre><br />";
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
					   $twitter->logout();
					   break;
				case 7 : echo "User not connected to the provider."; 
					   $twitter->logout();
					   break;
				case 8 : echo "Provider does not support this feature."; break;
			} 
		}
	}

} # class
