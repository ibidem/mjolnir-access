<?php namespace mjolnir\access\tests;

use \mjolnir\access\Task_User_Password;

class Task_User_PasswordTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Task_User_Password'));
	}

	// @todo tests for \mjolnir\access\Task_User_Password

} # test
