<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Cascading File System
 * @author     Ibidem Team
 * @copyright  (c) 2012, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_Make_Admin extends \app\Instantiatable implements \mjolnir\types\Task
{
	use \app\Trait_Task;

	/**
	 * Execute task.
	 */
	function run()
	{
		$this->set('role', 'admin');
		\app\Task_Make_User::instance()
			->writer_is($this->writer())
			->metadata_is($this->metadata())
			->run();
	}

} # class
