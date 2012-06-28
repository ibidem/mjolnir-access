<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_Role extends \app\Model_SQL_Factory
{
	protected static $table = 'roles';
	
	/**
	 * @return string table name
	 */
	static function assoc_users()
	{
		return \app\Model_User::assoc_roles();
	}
	
	// -------------------------------------------------------------------------
	// Model_Factory interface
	
	/**
	 * @param array (title)
	 * @return \app\Validator
	 */
	static function validator(array $fields)
	{
		$user_config = \app\CFS::config('model/UserRole');
		
		return \app\Validator::instance($user_config['errors'], $fields)
			->rule('title', 'not_empty')
			->rule('title', '\app\Model_DB_Role::unique');
	}
	
	/**
	 * @param array (title)
	 * @throws \ibidem\access\Exception
	 */
	static function assemble(array $fields)
	{
		\app\SQL::begin(); # begin transaction
		try
		{
			\app\SQL::prepare
				(
					__METHOD__,
					'
						INSERT INTO `'.static::table().'`
							(
								title
							)
						VALUES
							(
								:title 
							)
					',
					'mysql'
				)
				->set(':title', \htmlspecialchars($fields['title']))
				->execute();
			
			static::$last_inserted_id = \app\SQL::last_inserted_id();
			
			\app\SQL::commit();
		} 
		catch (\Exception $exception)
		{
			\app\SQL::rollback();
			throw $exception;
		}
	}
	
	public static function update_validator($id, array $fields) 
	{
		return \app\Validator::instance([], $fields)
			->rule('title', 'not_empty');
	}

	public static function update_assemble($id, array $fields)
	{
		\app\SQL::begin();
		try
		{
			\app\SQL::prepare
				(
					__METHOD__,
					'
						UPDATE `'.static::table().'`
						   SET title = :title
						 WHERE id = :id
					',
					'mysql'
				)
				->set(':title', $fields['title'])
				->bind_int(':id', $id)
				->execute();

			\app\SQL::commit();
		}
		catch (\Exception $e)
		{
			\app\SQL::rollback();
			throw $e;
		}
	}	
	
	/**
	 * @return array (id, title)
	 */
	public static function entries($page, $limit, $offset = 0)
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT role.id id,
					       role.title title
					  FROM `'.static::table().'` role
					 LIMIT :limit OFFSET :offset
				',
				'mysql'
			)
			->page($page, $limit, $offset)
			->execute()
			->fetch_all();
	}
	
	/**
	 * @param array (id, title)
	 */
	public static function entry($id) 
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT role.id id,
					       role.title title
					  FROM `'.static::table().'` role
					 WHERE role.id = :id
				',
				'mysql'
			)
			->set_int(':id', $id)
			->execute()
			->fetch_array();
	}
	
	// -------------------------------------------------------------------------
	// Validator Helpers	
	
	/**
	 * @return boolean
	 */
	public static function unique($role)
	{
		$first_row = \app\SQL::prepare
			(
				__METHOD__, 
				'
					SELECT COUNT(1)
					  FROM '.static::table().'
					 WHERE title = :role
				', 
				'mysql'
			)
			->bind(':role', $role)
			->execute()
			->fetch_array();
		
		$count = $first_row['COUNT(1)'];
		
		if (\intval($count) != 0)
		{
			return false;
		}
		
		// test passed
		return true;
	}

} # class
