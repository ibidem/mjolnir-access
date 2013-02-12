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
		\app\Task::consolewriter($this->writer);

		$username = $this->get('username', null);
		$password = $this->get('password', null);
		$email = $this->get('email', null);
		
		$errors = \app\Model_User::push
			(
				[
					'nickname' => $username, 
					'password' => $password, 
					'email' => $email,
					'role' => \app\Model_Role::role_by_name('admin'),
				]
			);
		
		if ($errors === null)
		{
			$this->writer->writef(" Created administrator: $username")->eol();
		}
		else # got errors
		{
			$this->writer->writef(' Failed to create user. ')->eol();
			foreach ($errors as $field => $error)
			{
				$this->writer->writef("  - $field: ".\array_pop($error))->eol();
			}
		}
	}

} # class
