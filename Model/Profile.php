<?php namespace ibidem\access;

/**
 * @package    ibidem
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_Profile extends \app\Model_SQL_Factory
{
	protected static $table = 'profilefields';
	protected static $table_user_field = 'user_field';
	
	static function assoc_user()
	{
		$database_config = \app\CFS::config('ibidem/database');
		return $database_config['table_prefix'].static::$table_user_field;
	}
	
	// -------------------------------------------------------------------------
	// Model_Factory interface
	
	static function validator(array $fields) 
	{
		return \app\Validator::instance([], $fields)
			->rule('title', 'not_empty')
			->rule('idx', 'not_empty')
			->rule('type', 'not_empty')
			->rule('required', 'not_empty');
	}
	
	static function assemble(array $fields) 
	{
		\app\SQL::begin();
		try
		{
			\app\SQL::prepare
				(
					__METHOD__,
					'
						INSERT INTO `'.static::table().'`
							(title, idx, type, required)
						VALUES
							(:title, :idx, :type, :required)
					',
					'mysql'
				)
				->set_int(':idx', $fields['idx'])
				->set(':title', $fields['title'])
				->set(':type', $fields['type'])
				->set_bool(':required', $fields['required'])
				->execute();
			
			\app\SQL::commit();
		}
		catch (\Exception $e)
		{
			\app\SQL::rollback();
			throw $e;
		}
	}
	
	static function update_validator($id, array $fields) 
	{
		return \app\Validator::instance([], $fields)
			->rule('title', 'not_empty')
			->rule('idx', 'not_empty')
			->rule('required', 'not_empty');
	}
	
	static function update_assemble($id, array $fields)
	{
		\app\SQL::begin();
		try
		{
			\app\SQL::prepare
				(
					__METHOD__,
					'
						UPDATE `'.static::table().'`
						   SET title = :title, 
						       idx = :idx, 
							   required = :required
						 WHERE id = :id
					',
					'mysql'
				)
				->set_int(':id', $id)
				->set_int(':idx', $fields['idx'])
				->set(':title', $fields['title'])
				->set_bool(':required', $fields['required'])
				->execute();
			
			\app\SQL::commit();
		}
		catch (\Exception $e)
		{
			\app\SQL::rollback();
			throw $e;
		}
	}
	
	static function entries($page, $limit, $offset = 0)
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT field.id id,
					       field.idx idx,
					       field.title title,
						   field.type type,
						   field.required required
					  FROM `'.static::table().'` field
					 LIMIT :limit OFFSET :offset
				',
				'mysql'
			)
			->page($page, $limit, $offset)
			->execute()
			->fetch_all();
	}
	
	static function entry($id)
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT field.id id,
					       field.idx idx,
					       field.title title,
						   field.type type,
						   field.required required
					  FROM `'.static::table().'` field
					 WHERE field.id = :id
				',
				'mysql'
			)
			->set_int(':id', $id)
			->execute()
			->fetch_array();
	}
	
	// -------------------------------------------------------------------------
	// Extended
	
	static function profile_info($id)
	{
		return \app\SQL::prepare
			(
				__METHOD__,
				'
					SELECT field.id id,
					       field.title title,
						   profile.value value,
						   field.type type
					  FROM `'.static::table().'` field
					  LEFT OUTER
					  JOIN `'.static::assoc_user().'` profile
						ON profile.field = field.id
					 WHERE profile.user = :user
				'
			)
			->set_int(':user', $id)
			->execute()
			->fetch_all();
	}
	
	static function update_profile_validator($id, array $fields)
	{
		return \app\Validator::instance([], $fields);
	}
	
	static function update_profile_assemble($id, array $fields)
	{
		// @todo
	}
	
	static function update_profile($id, array $fields)
	{
		$validator = static::update_profile_validator($id, $fields);
		
		if ($validator->validate() === null)
		{
			static::update_profile_assemble($id, $fields);
		}
		else # invalid
		{
			return $validator;
		}
		
		return null;
	}
	
	// -------------------------------------------------------------------------
	// Validator Helpers

} # class
