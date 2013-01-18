<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Trait
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
trait Trait_Model_SecurityToken
{
	/**
	 * Generates a token and stores a reference to it in the entry.
	 * 
	 * For detailed information on the parameters see `SecurityToken::make`.
	 * 
	 * @return string token
	 */
	static function token($entry_id, $expires = null, $purpose = 'mjolnir:universal', $key = null)
	{
		list($token, $token_id) = \app\SecurityToken::make($expires, $purpose, $key);
		
		// remove old token
		$entry = static::entry($entry_id);
		
		if ( ! empty($entry['token']))
		{
			\app\SecurityToken::delete([$entry['token']]);
		}
		
		// remember the token id
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET `token` = :token_id
					 WHERE `'.static::unique_key().'` = :entry_id
				'
			)
			->num(':token_id', $token_id)
			->num(':entry_id', $entry_id)
			->run();
		
		\app\Stash::purge(\app\Stash::tags(\get_called_class(), ['change']));
		
		return $token;
	}
	
	/**
	 * Checks token against entry at specified ID.
	 * 
	 * @return boolean
	 */
	static function confirm_token($entry_id, $token, $purpose = 'mjolnir:universal', $key = null)
	{
		$entry = \app\Model_User::entry($entry_id);
		
		// check if entry exists
		if ($entry === null || empty($entry['token']))
		{
			return false;
		}
		
		return \app\SecurityToken::confirm($entry['token'], $token, $purpose, $key);
	}
	
	/**
	 * @return string token
	 */
	static function purgetoken($entry_id)
	{
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET `token` = NULL
					 WHERE `'.static::unique_key().'` = :entry_id
				'
			)
			->num(':entry_id', $entry_id)
			->run();
	}

} # trait
