<?php namespace mjolnir\access;

/**
 * @package    mjolnir
 * @category   Task
 * @author     Ibidem Team
 * @copyright  (c) 2013, Ibidem Team
 * @license    https://github.com/ibidem/ibidem/blob/master/LICENSE.md
 */
class Task_User_Password extends \app\Task_Base
{
	/**
	 * Execute task.
	 */
	function run()
	{
		\app\Task::consolewriter($this->writer);
		
		$nickname = $this->get('username', false);
		$email = $this->get('email', false);
		$password = $this->get('password', false);
		
		if ($password === false)
		{
			throw new \app\Exception('Password is required.');
		}
		
		if ($email !== false)
		{
			$user_id = \app\Model_User::for_email($email);
			$user = \app\Model_User::entry($user_id);
		}
		else if ($nickname !== false)
		{
			$user = \app\Model_User::find_entry(['nickname' => $nickname, 'locked' => false]);
			
			if ($user['nickname'] !== $nickname)
			{
				$this->writer->writef(" No user [{$nickname}] found.")->eol();
				exit(1);
			}
		}
		else # no nickname and email provided
		{
			// intentionally using "username" instead of "nickname"
			throw new \app\Exception('You must specify either username or email.');
		}
				
		$this->writer->writef(" Detected user [{$user['nickname']}] with email [{$user['email']}].")->eol();
		\app\Model_User::change_password($user['id'], [ 'password' => $password ]);
		$this->writer->writef(' Password has been changed.');
	}

} # class
