<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class SecurityToken
{
	/**
	 * Creates a new token. 
	 * 
	 * When expires is null the default expires for tokens from mjolnir/security
	 * will be used. 
	 * 
	 * When setting expires you may use any valid value that \strtotime would
	 * accept, such as "+3 hours", "tomorrow", etc.
	 * 
	 * A key may be provided. The key will be used when generating the token, 
	 * and is required when validating the token. Note however that the api key
	 * will already be used when doing so, so this extra key you pass does not
	 * provide extra security to the generated code but instead is meant to be
	 * used to invalidate the token. For example when generating a password 
	 * reset the password salt may be used as a key to gurantee a token 
	 * created before the password was reset can not be used after the reset.
	 * 
	 * Every time a token is created any expired tokens are purged.
	 * 
	 * It is recomended you give your tokens a "purpose" to avoid a token being
	 * misused to validate a different task then what it was created for; this
	 * is only the case when you are using a single token field in your model
	 * and there is a possibility of token misuse in such a way as to create a 
	 * security issue.
	 * 
	 * eg.
	 *	token is used in a User model for locking the account
	 *  token is used in a User model for reseting the password
	 * 
	 * If the purpose of the lock account routine token is not set it is 
	 * possible for an attacker to use the reset password token to lock a user
	 * outside his or her account.
	 * 
	 * @return int token id
	 */
	static function make($purpose = 'mjolnir:universal', $expires = null, $key = null)
	{
		// load configuration
		$security = \app\CFS::config('mjolnir/security');
		
		if ($expires === null)
		{
			$expires = $security['tokens']['default.expires'];
		}

		if ($key !== null)
		{
			$nans = \hash_hmac($security['hash']['algorythm'], \uniqid(\rand(), true),$key, false);
		}
		else # no key
		{
			$nans = \uniqid(\rand(), true);
		}
		
		$token = \hash_hmac($security['hash']['algorythm'], $key, $security['keys']['apikey'], false);
		
		\app\Model_SecurityToken::push
			(
				[
					'purpose' => $purpose,
					'token' => $token, 
					'expires' => \make_date($expires)->format('Y-m-d H:i:s')
				]
			);
		
		$token_id = \app\Model_SecurityToken::last_inserted_id();
		
		// purge all expired tokens
		\app\Model_SecurityToken::purge();
		
		return [$token, $token_id];
	}
	
	static function validate($token, $id, $key = null)
	{
		if ($key === null)
		{
			return false;
		}
		
		// retrieve token at given key
	}

} # class
