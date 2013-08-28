<?php namespace mjolnir\access\tests;

use \mjolnir\access\Access;

class AccessTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Access'));
	}

	// @todo tests for \mjolnir\access\Access

} # test
