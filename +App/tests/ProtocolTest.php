<?php namespace mjolnir\access\tests;

use \mjolnir\access\Protocol;

class ProtocolTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Protocol'));
	}

	// @todo tests for \mjolnir\access\Protocol

} # test
