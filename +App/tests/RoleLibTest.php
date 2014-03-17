<?php namespace mjolnir\access\tests;

use \mjolnir\access\RoleLib;

class RoleLibTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\RoleLib'));
	}

	// @todo tests for \mjolnir\access\RoleLib

} # test
