<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Access
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Model_Role
{
	use \app\Trait_Model_Factory;
	use \app\Trait_Model_Utilities;
	use \app\Trait_Model_Collection;
	use \app\Trait_Model_Automaton;

	/**
	 * @var array
	 */
	protected static $table = 'mjolnir__roles';

	/**
	 * @var array
	 */
	protected static $fieldformat = [];

	/**
	 * @var array
	 */
	protected static $automaton = array
		(
			'fields' => [ 'title' ],
			'unique' => [ 'title' ],
			'errors' => array
				(
					'title' => array
						(
							':unique' => 'Role already exists.'
						),
				),
		);

	// ------------------------------------------------------------------------
	// etc

	/**
	 * @return int
	 */
	static function by_name($name)
	{
		return (int) static::statement
			(
				__METHOD__,
				'
					SELECT id
					  FROM :table
					 WHERE title = :name
				'
			)
			->str(':name', $name)
			->run()
			->fetch_entry()
			['id'];
	}

	/**
	 * Checks for updates to the roles configuration and updates the roles
	 * table.
	 */
	static function autoupdate()
	{
		$access = \app\CFS::config('mjolnir/access');
		$knownroles = \array_keys($access['roles']);

		$roles = static::entries(null, null);
		$existingroles = \app\Arr::gather($roles, 'title');

		$missing_roles = \array_diff($knownroles, $existingroles);

		foreach ($missing_roles as $role)
		{
			static::statement
				(
					__METHOD__,
					'
						INSERT INTO :table
						(id, title)	VALUES (:id, :title)
					'
				)
				->str(':title', $role)
				->num(':id', $access['roles'][$role])
				->run();
		}
	}

} # class
