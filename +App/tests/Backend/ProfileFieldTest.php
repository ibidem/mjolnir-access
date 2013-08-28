<?php namespace mjolnir\access\tests;

use \mjolnir\access\Backend_ProfileField;

class Backend_ProfileFieldTest extends \PHPUnit_Framework_TestCase
{
	/** @test */ function
	can_be_loaded()
	{
		$this->assertTrue(\class_exists('\mjolnir\access\Backend_ProfileField'));
	}

	// @todo tests for \mjolnir\access\Backend_ProfileField

} # test
