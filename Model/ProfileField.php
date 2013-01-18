<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Model
 * @author     Ibidem
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_ProfileField
{
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Utilities;
	use \app\Trait_Model_Collection;
	
	/**
	 * @var string table
	 */
	protected static $table = 'profilefields';
	
	/**
	 * @var string table 
	 */
	protected static $table_user_field = 'user_field';
	
	/**
	 * @var array
	 */
	protected static $field_format = [];
	
	/**
	 * @return string
	 */
	static function assoc_user()
	{
		$database_config = \app\CFS::config('mjolnir/database');
		return $database_config['table_prefix'].static::$table_user_field;
	}
	
	// -------------------------------------------------------------------------
	// Factory interface
	
	/**
	 * @return \app\Validator
	 */
	static function check(array $fields, $context = null) 
	{
		$errors = ['name' => ['unique' => 'Field with the same name already exists.']];
		return \app\Validator::instance($errors, $fields)
			->ruleset('not_empty', ['title', 'name', 'idx', 'type', 'required'])
			->test('name', 'unique', ! static::exists($fields['name'], 'name', $context));
	}
	
	/**
	 * Create new profile field.
	 */
	static function process(array $fields) 
	{
		static::inserter($fields, ['title', 'name', 'idx', 'type'], ['required'])->run();
		static::$last_inserted_id = \app\SQL::last_inserted_id();
	}
	
	/**
	 * Update profile field.
	 */
	static function update_process($id, array $fields)
	{
		static::updater($id, $fields, ['title', 'name', 'idx'], ['required'])->run();
		static::clear_entry_cache($id);
	}
	
	// -------------------------------------------------------------------------
	// Update profile
	
	/**
	 * @return \app\Validation
	 */
	static function update_profile_check($id, array $fields)
	{
		$validator = \app\Validator::instance([], $fields);
		
		$profile_fields = \app\Model_ProfileField::entries(null, null);
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
			$profile_fields = \app\Model_ProfileField::entries(null, null);
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
				->num(':id', $id)
				->run();

			// load fieldtypes
			$field_types = \app\CFS::config('mjolnir/profile-fieldtypes');

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
							->num(':user', $id)
							->num(':field', $key)
							->str(':value', $field_types[$map[$key]['type']]['store']($value))
							->run();
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
	 * @return array or null
	 */
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
	
	/**
	 * Clear cache for specific id.
	 */
	protected static function profile_info_clearcache($id)
	{
		$cachekey = \get_called_class().'__profile_info_ID'.$id;
		\app\Stash::delete($cachekey);
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
		
		if ($result === null)
		{
			$result = static::statement
				(
					__METHOD__,
					'
						SELECT field.id,
							   field.title,
							   field.name,
							   field.type,
							   profile.value value
						  FROM :table field
						  LEFT OUTER
						  JOIN `'.static::assoc_user().'` profile
							ON profile.field = field.id
						 WHERE profile.user = :user
						 ORDER BY field.idx ASC
					'
				)
				->num(':user', $id)
				->run()
				->fetch_all(static::field_format());
			
			$profile_config = \app\CFS::config('mjolnir/profile-fieldtypes');
			foreach ($result as & $field)
			{
				$field['render'] = $profile_config[$field['type']]['render']($field['value']);
			}
			
			\app\Stash::set($cachekey, $result);
		}
		
		return $result;
	}
	
	static function profile_field($user, $field)
	{
		$profile_info = static::profile_info($user);
		
		foreach ($profile_info as & $profile_field)
		{
			if ($profile_field['name'] === $field)
			{
				return $profile_field;
			}
		}
		
		return null;
	}
	
} # class
