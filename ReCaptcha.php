<?php namespace mjolnir\access;

require_once \app\CFS::dir('vendor/recaptcha').'/recaptchalib'.EXT;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class ReCaptcha
{
	/**
	 * Outputs html required for recaptcha test.
	 *
	 * @return string
	 */
	static function html()
	{
		$recaptcha_config = \app\CFS::config('mjolnir/a12n')['recaptcha'];

		if ( ! isset($recaptcha_config['public_key'], $recaptcha_config['private_key']))
		{
			throw new \app\Exception('ReCaptcha keys not set.');
		}

		return \recaptcha_get_html($recaptcha_config['public_key']);
	}

	/**
	 * Verify recaptcha is valid.
	 *
	 * @return string or null on success
	 */
	static function verify($recaptcha_challenge_field, $recaptcha_response_field)
	{
		$recaptcha_config = \app\CFS::config('mjolnir/a12n')['recaptcha'];

		$response = \recaptcha_check_answer
			(
				$recaptcha_config['private_key'],
				$_SERVER['REMOTE_ADDR'],
				$recaptcha_challenge_field,
				$recaptcha_response_field
			);

		if ($response->is_valid)
		{
			return null;
		}
		else # error
		{
			return $response->error;
		}
	}

} # class
