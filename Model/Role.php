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

} # class
