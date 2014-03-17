<?php namespace mjolnir\access\tests;

use \mjolnir\access\UserLib;

class UserLibTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\UserLib'));
	}

	// @todo tests for \mjolnir\access\UserLib

} # test
