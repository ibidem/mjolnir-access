<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2013 Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Password
{
	/**
	 * Given a password in plaintext the method will produce a verifier, salt
	 * and algorythm.
	 *
	 * You can provide an algorythm and/or salt and it will not be generated.
	 * This is useful for, as an example, using this method to check if another
	 * password is equivalent to this password by regenerating the verifier.
	 *
	 * @return array [salt, verifier, algorythm]
	 */
	static function generate($textpwd, $salt = null, $algorythm = null)
	{
		$password = [];

		// load configuration
		$security = \app\CFS::config('mjolnir/security');

		// generate password salt and hash
		if ($salt === null)
		{
			$password['salt'] = \hash($security['hash']['algorythm'], (\uniqid(\rand(), true)), false);
		}
		else # salt provided
		{
			$password['salt'] = $salt;
		}

		if ($algorythm === null)
		{
			$algorythm = $security['hash']['algorythm'];
		}

		$apilocked_password = \hash_hmac($algorythm, $textpwd, $security['keys']['apikey'], false);
		$password['verifier'] = \hash_hmac($algorythm, $apilocked_password, $password['salt'], false);
		$password['algorythm'] = $algorythm;

		return $password;
	}

	/**
	 * @return boolean
	 */
	static function match($textpwd, $verifier, $salt, $algorythm)
	{
		$pwd = static::generate($textpwd, $salt, $algorythm);
		return $pwd['verifier'] == $verifier;
	}

} # class
