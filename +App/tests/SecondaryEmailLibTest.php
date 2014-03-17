<?php namespace mjolnir\access\tests;

use \mjolnir\access\SecondaryEmailLib;

class SecondaryEmailLibTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\SecondaryEmailLib'));
	}

	// @todo tests for \mjolnir\access\SecondaryEmailLib

} # test
