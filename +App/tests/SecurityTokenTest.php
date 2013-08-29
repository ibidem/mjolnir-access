<?php namespace mjolnir\access\tests;

use \mjolnir\access\SecurityToken;

class SecurityTokenTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\SecurityToken'));
	}

	// @todo tests for \mjolnir\access\SecurityToken

} # test
