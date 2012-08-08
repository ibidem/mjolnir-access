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
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Master;
	use \app\Trait_Model_Collection;
	
	protected static $table = 'profilefields';
	protected static $table_user_field = 'user_field';
	
	/**
	 * @return string
	 */
	static function assoc_user()
	{
		$database_config = \app\CFS::config('ibidem/database');
		return $database_config['table_prefix'].static::$table_user_field;
	}
	
	// -------------------------------------------------------------------------
	// Factory interface
	
	/**
	 * @return \app\Validator
	 */
	static function check(array $fields, $context = null) 
	{
		return \app\Validator::instance([], $fields)
			->ruleset('not_empty', ['title', 'idx', 'type', 'required']);
	}
	
	/**
	 * Create new profile field.
	 */
	static function process(array $fields) 
	{
		static::insertor($fields, ['title', 'idx', 'type', 'required'])->run();
	}
	
	/**
	 * Update profile field.
	 */
	static function update_process($id, array $fields)
	{
		static::updater($id, $fields, ['title', 'idx', 'required'])->run();
	}
	
	// -------------------------------------------------------------------------
	// Extended
	
	/**
	 * @return array profile fields
	 */
	static function profile_info($id)
	{
		$cachekey = \get_called_class().'__profile_info_ID'.$id;
		$result = \app\Stash::get($cachekey, null);
		
		if ($result !== null)
		{
			$result = \app\SQL::prepare
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
						 ORDER BY field.idx ASC
					'
				)
				->set_int(':user', $id)
				->execute()
				->fetch_all();
			
			\app\Stash::set($cachekey, $result);
		}
		
		return $result;
	}
	
	/**
	 * @return \app\Validation
	 */
	static function update_profile_check($id, array $fields)
	{
		$validator = \app\Validator::instance([], $fields);
		
		$profile_fields = \app\Model_Profile::entries(null, null);
		foreach ($profile_fields as $field)
		{
			if ($field['required'])
			{
				$validator->rule('field-'.$field['id'], 'not_empty');
			}
		}
		
		return $validator;
	}
	
	/**
	 * Dynamically update profile fields.
	 */
	static function update_profile_process($id, array $fields)
	{
		try
		{
			// retrieve profile fields; we need them for the field type mapping
			$profile_fields = \app\Model_Profile::entries(null, null);
			$map = [];
			foreach ($profile_fields as $field)
			{
				$map[(int) $field['id']] = $field;
			}

			// remove all current fields
			\app\SQL::prepare
				(
					__METHOD__.':cleanup',
					'
						DELETE FROM `'.static::assoc_user().'`
						 WHERE user = :id
					',
					'mysql'
				)
				->set_int(':id', $id)
				->execute();

			// load fieldtypes
			$field_types = \app\CFS::config('ibidem/profile-fieldtypes');

			// go though all fields
			foreach ($fields as $field => $value)
			{
				if ( ! empty($value))
				{
					// retrieve the ones we're interested in
					if (\preg_match('#^field-[0-9]+$#', $field))
					{
						// retrieve value
						$key = (int) \preg_replace('#^field-#', '', $field);
						// update
						\app\SQL::prepare
							(
								__METHOD__,
								'
									INSERT INTO `'.static::assoc_user().'`
									   SET user = :user,
										   field = :field,
										   value = :value
								',
								'mysql'
							)
							->set_int(':user', $id)
							->set_int(':field', $key)
							->set(':value', $field_types[$map[$key]['type']]['store']($value))
							->execute();
					}
				}
			}
			
			static::profile_info_clearcache($id);
		} 
		catch (\Exception $e)
		{
			static::profile_info_clearcache($id);
			throw $e;
		}
			
	}
	
	/**
	 * Clear cache for specific id.
	 */
	protected static function profile_info_clearcache($id)
	{
		$cachekey = \get_called_class().'__profile_info_ID'.$id;
		\app\Stash::delete($cachekey);
	}
	
	static function update_profile($id, array $fields)
	{
		$errors = static::update_profile_check($id, $fields)->errors();
		
		if ($errors === null)
		{
			\app\SQL::begin();
			try
			{
				static::update_profile_process($id, $fields);
				
				\app\SQL::commit();
			}
			catch (\Exception $e)
			{
				\app\SQL::rollback();
				throw $e;
			}
			
			return null;
		}
		else # got errors
		{
			return $errors;
		}
	}

} # class
