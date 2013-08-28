<?php namespace mjolnir\access\tests;

use \mjolnir\access\Backend_User;

class Backend_UserTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Backend_User'));
	}

	// @todo tests for \mjolnir\access\Backend_User

} # test
