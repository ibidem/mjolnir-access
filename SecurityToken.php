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
	 * Creates a new security token.
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
	static function make($expires = null, $purpose = 'mjolnir:universal', $key = null)
	{
		// load configuration
		$security = \app\CFS::config('mjolnir/security');

		if ($expires === null)
		{
			$expires = $security['tokens']['default.expires'];
		}

		$nans = \hash_hmac
			(
				$security['hash']['algorythm'],
				\uniqid(\rand(), true),
				$security['keys']['apikey'],
				false
			);

		if ($key !== null)
		{
			$token = \hash_hmac
				(
					$security['hash']['algorythm'],
					$nans,
					$key,
					false
				);
		}
		else # no key
		{
			$token = $nans;
		}

		// create token
		$errors = \app\Model_SecurityToken::push
			(
				[
					'purpose' => $purpose,
					'token' => $token,
					'expires' => \date_create($expires)->format('Y-m-d H:i:s')
				]
			);

		if ($errors !== null)
		{
			throw new \Exception('Failed to create security token.');
		}

		$token_id = \app\Model_SecurityToken::last_inserted_id();

		// purge all expired tokens
		\app\Model_SecurityToken::purge();

		// return unkeyed token along with id
		return [$nans, $token_id];
	}

	/**
	 * Checks token at specified id against test token, with given purpose and
	 * key; as explained in `SecurityToken::make`.
	 *
	 * @return boolean
	 */
	static function confirm($token_id, $test_token, $purpose = 'mjolnir:universal', $key = null)
	{
		// retrieve token at given key
		$entry = \app\Model_SecurityToken::entry($token_id);

		// check if entry exists
		if ($entry === null)
		{
			return false;
		}

		// check purpose
		if ($purpose !== $entry['purpose'])
		{
			return false;
		}

		// check if token is not expired
		if ($entry['expires'] < \date_create('now'))
		{
			return false;
		}

		// load security configuration
		$security = \app\CFS::config('mjolnir/security');

		// do we have key?
		if ($key !== null)
		{
			$test_token = \hash_hmac
				(
					$security['hash']['algorythm'],
					$test_token,
					$key,
					false
				);
		}

		// check for token match
		if ($test_token !== $entry['token'])
		{
			return false;
		}

		// all tests passed
		return true;
	}

	/**
	 * ...
	 */
	static function delete(array $tokens)
	{
		\app\Model_SecurityToken::delete($tokens);
	}

} # class
