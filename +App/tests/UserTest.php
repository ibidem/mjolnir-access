<?php namespace mjolnir\access\tests;

use \mjolnir\access\User;

class UserTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\User'));
	}

	// @todo tests for \mjolnir\access\User

} # test
