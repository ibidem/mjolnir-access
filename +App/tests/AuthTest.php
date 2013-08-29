<?php namespace mjolnir\access\tests;

use \mjolnir\access\Auth;

class AuthTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Auth'));
	}

	// @todo tests for \mjolnir\access\Auth

} # test
