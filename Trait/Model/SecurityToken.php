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
	static function token($entry_id, $purpose = 'mjolnir:universal')
	{
		list($token, $token_id) = \app\SecurityToken::make($purpose);
		
		static::statement
			(
				__METHOD__,
				'
					UPDATE :table
					   SET `token` = :token_id
					 WHERE `'.static::unique_key().'` = :entry_id
				'
			)
			->set_int(':token_id', $token_id)
			->set_int(':entry_id', $entry_id)
			->execute();
		
		return $token;
	}
	
	static function confirm_token($entry_id, $token, $purpose = 'mjolnir:universal')
	{
		
	}

} # trait
