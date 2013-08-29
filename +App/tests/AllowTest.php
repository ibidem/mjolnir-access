<?php namespace mjolnir\access\tests;

use \mjolnir\access\Allow;

class AllowTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Allow'));
	}

	// @todo tests for \mjolnir\access\Allow

} # test
