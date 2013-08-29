<?php namespace mjolnir\access\tests;

use \mjolnir\access\Password;

class PasswordTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Password'));
	}

	// @todo tests for \mjolnir\access\Password

} # test
