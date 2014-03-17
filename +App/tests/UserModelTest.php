<?php namespace mjolnir\access\tests;

use \mjolnir\access\UserModel;

class UserModelTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\UserModel'));
	}

	// @todo tests for \mjolnir\access\UserModel

} # test
