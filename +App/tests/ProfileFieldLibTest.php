<?php namespace mjolnir\access\tests;

use \mjolnir\access\ProfileFieldLib;

class ProfileFieldLibTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\ProfileFieldLib'));
	}

	// @todo tests for \mjolnir\access\ProfileFieldLib

} # test
