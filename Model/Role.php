<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_Role extends \app\Instantiatable
{
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Master;
	use \app\Trait_Model_Collection;
	
	protected static $table = 'roles';
	
	/**
	 * @return string table name
	 */
	static function assoc_users()
	{
		return \app\Model_User::assoc_roles();
	}
	
	// -------------------------------------------------------------------------
	// Factory interface
	
	/**
	 * @param array (title)
	 * @return \app\Validator
	 */
	static function check(array $fields, $context = null)
	{
		$user_config = \app\CFS::config('model/UserRole');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('title', 'not_empty')
			->test('title', ':unique', ! static::exists($fields['title'], 'title', $context));
	}
	
	/**
	 * @param array (title)
	 * @throws \ibidem\access\Exception
	 */
	static function process(array $fields)
	{
		static::inserter($fields, ['title'])->run();
		static::$last_inserted_id = \app\SQL::last_inserted_id();
	}
	
	/**
	 * @return \app\Validator
	 */
	static function update_check($id, array $fields) 
	{
		return \app\Validator::instance([], $fields)
			->rule('title', 'not_empty');
	}

	/**
	 * @param int role id
	 * @param array fields
	 */
	static function update_process($id, array $fields)
	{
		static::updater($id, $fields, ['title']);
	}

} # class
