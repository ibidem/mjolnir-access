<?php namespace mjolnir\access\tests;

use \mjolnir\access\Backend_Role;

class Backend_RoleTest extends \app\PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Backend_Role'));
	}

	// @todo tests for \mjolnir\access\Backend_Role

} # test
