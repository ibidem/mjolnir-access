<?php namespace mjolnir\access\tests;

use \mjolnir\access\Model_User;

class Model_UserTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Model_User'));
	}

	// @todo tests for \mjolnir\access\Model_User

} # test
