<?php namespace mjolnir\access\tests;

use \mjolnir\access\Task_Make_User;

class Task_Make_UserTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Task_Make_User'));
	}

	// @todo tests for \mjolnir\access\Task_Make_User

} # test
