<?php namespace mjolnir\access\tests;

use \mjolnir\access\Model_Role;

class Model_RoleTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Model_Role'));
	}

	// @todo tests for \mjolnir\access\Model_Role

} # test
