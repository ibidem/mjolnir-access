<?php namespace mjolnir\access\tests;

use \mjolnir\access\Model_ProfileField;

class Model_ProfileFieldTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Model_ProfileField'));
	}

	// @todo tests for \mjolnir\access\Model_ProfileField

} # test
