<?php namespace mjolnir\access\tests;

use \mjolnir\access\SecurityTokenLib;

class SecurityTokenLibTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\SecurityTokenLib'));
	}

	// @todo tests for \mjolnir\access\SecurityTokenLib

} # test
