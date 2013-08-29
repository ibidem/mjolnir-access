<?php namespace mjolnir\access\tests;

use \mjolnir\access\Model_SecurityToken;

class Model_SecurityTokenTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Model_SecurityToken'));
	}

	// @todo tests for \mjolnir\access\Model_SecurityToken

} # test
